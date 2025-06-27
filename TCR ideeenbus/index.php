\<?php
/**
 * TCR Ideeënbus - Eenvoudige Versie (zoals jouw voorbeeld)
 */

require_once 'config.php';
session_start();

$boodschap = '';
$fout = '';
$form_data = [
    'naam' => '',
    'email' => '',
    'titel' => '',
    'bericht' => ''
];

// Verwerk formulier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = trim($_POST['naam'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $titel = trim($_POST['titel'] ?? '');
    $bericht = trim($_POST['bericht'] ?? '');
    
    $form_data = compact('naam', 'email', 'titel', 'bericht');
    
    if (empty($naam)) {
        $fout = 'Naam is verplicht!';
    } elseif (empty($titel)) {
        $fout = 'Titel van je idee is verplicht!';
    } elseif (empty($bericht)) {
        $fout = 'Idee / Suggestie is verplicht!';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fout = 'Ongeldig e-mailadres!';
    } else {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->prepare("INSERT INTO ideeen (naam, email, titel, bericht) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$naam, $email, $titel, $bericht])) {
                $boodschap = 'Je idee is succesvol toegevoegd!';
                $form_data = ['naam' => '', 'email' => '', 'titel' => '', 'bericht' => ''];
            } else {
                $fout = 'Er ging iets mis bij het opslaan.';
            }
        } catch (PDOException $e) {
            $fout = 'Database fout: ' . $e->getMessage();
        }
    }
}

// Haal ideeën op
try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->query("SELECT * FROM ideeen ORDER BY datum DESC LIMIT 10");
    $ideeen = $stmt->fetchAll();
} catch (PDOException $e) {
    $ideeen = [];
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TCR Ideeënbus</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>TCR Ideeënbus</h1>
        
        <div class="nav-links">
            <a href="index.php" class="active">Indienen</a>
            <a href="ideeen.php">Bekijk Ideeën</a>
            <a href="admin.php">Admin</a>
        </div>

        <?php if ($boodschap): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($boodschap, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($fout): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($fout, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="naam">Naam:</label>
                <input type="text" id="naam" name="naam" required 
                       value="<?= htmlspecialchars($form_data['naam'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($form_data['email'], ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="optioneel">
            </div>

            <div class="form-group">
                <label for="titel">Titel van je idee:</label>
                <input type="text" id="titel" name="titel" required
                       value="<?= htmlspecialchars($form_data['titel'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="bericht">Idee / Suggestie:</label>
                <textarea id="bericht" name="bericht" required 
                          placeholder="Beschrijf je idee hier... Gebruik BBCode: [b]vet[/b], [i]cursief[/i], [color=red]rood[/color] en smileys :)"><?= htmlspecialchars($form_data['bericht'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <button type="submit" class="submit-btn">Indienen</button>
        </form>

        <?php if (!empty($ideeen)): ?>
            <div class="ideeen-section">
                <h2 class="ideeen-header">Ideeën</h2>
                
                <?php foreach ($ideeen as $idee): ?>
                    <div class="idee-item">
                        <div class="idee-title"><?= htmlspecialchars($idee['titel'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="idee-meta">
                            Van: <strong><?= htmlspecialchars($idee['naam'], ENT_QUOTES, 'UTF-8') ?></strong> 
                            op <?= date('Y-m-d H:i', strtotime($idee['datum'])) ?>
                        </div>
                        <div class="idee-content">
                            <?= formatBericht($idee['bericht']) ?>
                        </div>
                        <div class="idee-actions">
                            <button class="like-btn" onclick="stemmen(<?= $idee['id'] ?>, 'up', this)">
                                👍 <span id="up-<?= $idee['id'] ?>"><?= $idee['upvotes'] ?></span>
                            </button>
                            <button class="dislike-btn" onclick="stemmen(<?= $idee['id'] ?>, 'down', this)">
                                👎 <span id="down-<?= $idee['id'] ?>"><?= $idee['downvotes'] ?></span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function stemmen(ideeId, type, button) {
            button.disabled = true;
            
            fetch('stem.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `idee_id=${ideeId}&type=${type}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`up-${ideeId}`).textContent = data.upvotes;
                    document.getElementById(`down-${ideeId}`).textContent = data.downvotes;
                } else {
                    alert(data.message || 'Er ging iets mis.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Netwerkfout.');
            })
            .finally(() => {
                button.disabled = false;
            });
        }
    </script>
</body>
</html>