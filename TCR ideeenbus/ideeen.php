<?php
/**
 * TCR Ideeënbus - Ideeën Overzicht (Eenvoudige Versie)
 */

require_once 'config.php';
session_start();

// Haal alle ideeën op
try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->query("SELECT * FROM ideeen ORDER BY datum DESC");
    $ideeen = $stmt->fetchAll();
} catch (PDOException $e) {
    $ideeen = [];
    $db_fout = "Kon ideeën niet ophalen: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TCR Ideeënbus - Alle Ideeën</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>TCR Ideeënbus</h1>
        
        <div class="nav-links">
            <a href="index.php">Indienen</a>
            <a href="ideeen.php" class="active">Bekijk Ideeën</a>
            <a href="admin.php">Admin</a>
        </div>

        <?php if (isset($db_fout)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($db_fout, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="ideeen-section">
            <h2 class="ideeen-header">Alle Ideeën (<?= count($ideeen) ?>)</h2>
            
            <?php if (empty($ideeen)): ?>
                <div class="idee-item">
                    <div class="idee-content">
                        Nog geen ideeën ingediend. <a href="index.php" style="color: #7cb342;">Wees de eerste!</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($ideeen as $idee): ?>
                    <div class="idee-item">
                        <div class="idee-title"><?= htmlspecialchars($idee['titel'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="idee-meta">
                            Van: <strong><?= htmlspecialchars($idee['naam'], ENT_QUOTES, 'UTF-8') ?></strong> 
                            op <?= date('Y-m-d H:i', strtotime($idee['datum'])) ?>
                            <?php if (!empty($idee['email'])): ?>
                                | <a href="mailto:<?= htmlspecialchars($idee['email'], ENT_QUOTES, 'UTF-8') ?>" style="color: #7cb342;">Contact</a>
                            <?php endif; ?>
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
                            <span style="margin-left: 10px; font-size: 12px; color: #666;">
                                Score: <?= $idee['upvotes'] - $idee['downvotes'] ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
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
                    
                    // Update score
                    const scoreElement = button.parentElement.querySelector('span:last-child');
                    if (scoreElement) {
                        scoreElement.textContent = `Score: ${data.upvotes - data.downvotes}`;
                    }
                    
                    // Visual feedback
                    button.style.background = type === 'up' ? '#e8f5e8' : '#ffeaea';
                    setTimeout(() => {
                        button.style.background = '';
                    }, 500);
                } else {
                    alert(data.message || 'Er ging iets mis bij het stemmen.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Netwerkfout. Probeer opnieuw.');
            })
            .finally(() => {
                button.disabled = false;
            });
        }
    </script>
</body>
</html>