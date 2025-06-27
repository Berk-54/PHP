<?php
/**
 * TCR Ideeënbus - AJAX Stemfunctionaliteit
 * 
 * Verwerkt like/dislike stemmen met:
 * - IP-based duplicate prevention
 * - Real-time vote counting
 * - JSON response voor AJAX
 */

require_once 'config.php';

// Set JSON header
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Alleen POST requests toegestaan
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false, 
        'message' => 'Alleen POST requests toegestaan'
    ]);
    exit;
}

// Rate limiting voor stemmen
$ip = getClientIP();
if (!checkRateLimit($ip, 10, 60)) { // 10 stemmen per minuut
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'message' => 'Te veel stemmen. Wacht even voordat je opnieuw stemt.'
    ]);
    exit;
}

// Valideer input parameters
$idee_id = intval($_POST['idee_id'] ?? 0);
$type = trim($_POST['type'] ?? '');

if ($idee_id <= 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'Ongeldig idee ID'
    ]);
    exit;
}

if (!in_array($type, ['up', 'down'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Ongeldig stem type'
    ]);
    exit;
}

try {
    $pdo = getDatabaseConnection();
    
    // Begin database transaction
    $pdo->beginTransaction();
    
    // Controleer of dit idee bestaat
    $stmt = $pdo->prepare("SELECT id, titel FROM ideeen WHERE id = ?");
    $stmt->execute([$idee_id]);
    $idee = $stmt->fetch();
    
    if (!$idee) {
        $pdo->rollBack();
        echo json_encode([
            'success' => false, 
            'message' => 'Dit idee bestaat niet meer'
        ]);
        exit;
    }
    
    // Controleer of gebruiker al heeft gestemd voor dit idee
    $stmt = $pdo->prepare("SELECT stem_type FROM stemmen WHERE idee_id = ? AND ip_adres = ?");
    $stmt->execute([$idee_id, $ip]);
    $bestaande_stem = $stmt->fetch();
    
    $boodschap = '';
    
    if ($bestaande_stem) {
        if ($bestaande_stem['stem_type'] === $type) {
            // Gebruiker probeert dezelfde stem opnieuw te geven
            $pdo->rollBack();
            echo json_encode([
                'success' => false,
                'message' => 'Je hebt dit idee al ' . ($type === 'up' ? 'geliked' : 'gedisliked') . '!'
            ]);
            exit;
        } else {
            // Update bestaande stem naar nieuwe type
            $stmt = $pdo->prepare("UPDATE stemmen SET stem_type = ?, datum = NOW() WHERE idee_id = ? AND ip_adres = ?");
            $stmt->execute([$type, $idee_id, $ip]);
            $boodschap = 'Je stem is gewijzigd naar ' . ($type === 'up' ? 'like' : 'dislike') . '!';
        }
    } else {
        // Nieuwe stem toevoegen
        $stmt = $pdo->prepare("INSERT INTO stemmen (idee_id, ip_adres, stem_type) VALUES (?, ?, ?)");
        $stmt->execute([$idee_id, $ip, $type]);
        $boodschap = 'Je hebt dit idee ' . ($type === 'up' ? 'geliked' : 'gedisliked') . '!';
    }
    
    // Update vote counts in ideeen tabel (real-time counting)
    $stmt = $pdo->prepare("
        UPDATE ideeen SET 
            upvotes = (SELECT COUNT(*) FROM stemmen WHERE idee_id = ? AND stem_type = 'up'),
            downvotes = (SELECT COUNT(*) FROM stemmen WHERE idee_id = ? AND stem_type = 'down')
        WHERE id = ?
    ");
    $stmt->execute([$idee_id, $idee_id, $idee_id]);
    
    // Haal de nieuwe tellingen op
    $stmt = $pdo->prepare("SELECT upvotes, downvotes FROM ideeen WHERE id = ?");
    $stmt->execute([$idee_id]);
    $tellingen = $stmt->fetch();
    
    // Commit transaction
    $pdo->commit();
    
    // Log de stem actie (optioneel voor analytics)
    error_log("Stem geregistreerd: IP $ip stemde '$type' op idee #$idee_id ('{$idee['titel']}')");
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => $boodschap,
        'upvotes' => intval($tellingen['upvotes']),
        'downvotes' => intval($tellingen['downvotes']),
        'total_votes' => intval($tellingen['upvotes']) + intval($tellingen['downvotes'])
    ]);
    
} catch (PDOException $e) {
    // Rollback op database fout
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Database fout in stem.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database fout. Probeer later opnieuw.'
    ]);
} catch (Exception $e) {
    // Rollback op andere fouten
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Algemene fout in stem.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Er ging iets mis. Probeer opnieuw.'
    ]);
}
?>