<?php
/**
 * TCR Ideeënbus - Admin Paneel (DEFINITIEF GEREPAREERD)
 * 
 * Beheer interface voor:
 * - Statistieken bekijken
 * - Ideeën modereren/verwijderen
 * - Gebruikersactiviteit monitoren
 * - Admin logs bekijken
 */

// Foutmeldingen aanzetten voor debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// Start sessie voor admin authenticatie
session_start();

// Admin authenticatie
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['password'] ?? '') === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_login_time'] = time();
        
        // Probeer admin actie te loggen, maar faal niet als het niet lukt
        try {
            logAdminAction('Login', 'Admin ingelogd');
        } catch (Exception $e) {
            error_log("Admin log fout: " . $e->getMessage());
        }
        
        header('Location: admin.php');
        exit;
    } else {
        // Toon login formulier
        ?>
        <!DOCTYPE html>
        <html lang="nl">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>TCR Ideeënbus - Admin Login</title>
            <link rel="stylesheet" href="style.css">
        </head>
        <body>
            <div class="container">
                <div class="form-container" style="max-width: 400px; margin: 100px auto;">
                    <h2>🔐 Admin Login</h2>
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                        <div class="alert alert-error">
                            ❌ Ongeldig wachtwoord!
                        </div>
                    <?php endif; ?>
                    <form method="POST" class="idee-form">
                        <div class="form-group">
                            <label for="password">Wachtwoord:</label>
                            <input type="password" id="password" name="password" required 
                                   placeholder="Voer admin wachtwoord in" autocomplete="current-password">
                        </div>
                        <button type="submit" class="submit-btn">🔓 Inloggen</button>
                    </form>
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="index.php" class="nav-btn">← Terug naar Site</a>
                    </div>
                    <div style="text-align: center; margin-top: 15px; padding: 10px; background: rgba(255,255,255,0.1); border-radius: 8px; font-size: 14px; color: #666;">
                        <strong>Demo wachtwoord:</strong> <?= htmlspecialchars(ADMIN_PASSWORD, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Auto-logout na 30 minuten inactiviteit
if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time']) > 1800) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Update laatste activiteit
$_SESSION['admin_login_time'] = time();

// Verwerk admin acties
$boodschap = '';
$fout = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'delete':
            $idee_id = intval($_POST['idee_id'] ?? 0);
            if ($idee_id > 0) {
                try {
                    $pdo = getDatabaseConnection();
                    
                    // Haal idee info op voor logging
                    $stmt = $pdo->prepare("SELECT titel, naam FROM ideeen WHERE id = ?");
                    $stmt->execute([$idee_id]);
                    $idee_info = $stmt->fetch();
                    
                    if ($idee_info) {
                        // Verwijder idee (stemmen worden automatisch verwijderd door CASCADE)
                        $stmt = $pdo->prepare("DELETE FROM ideeen WHERE id = ?");
                        $stmt->execute([$idee_id]);
                        
                        if ($stmt->rowCount() > 0) {
                            $boodschap = "Idee '{$idee_info['titel']}' van {$idee_info['naam']} is succesvol verwijderd.";
                            
                            // Probeer te loggen, maar faal niet als het niet lukt
                            try {
                                logAdminAction('Delete Idee', "Verwijderd: ID $idee_id - {$idee_info['titel']}");
                            } catch (Exception $e) {
                                error_log("Admin log fout: " . $e->getMessage());
                            }
                        } else {
                            $fout = 'Idee kon niet worden verwijderd.';
                        }
                    } else {
                        $fout = 'Idee niet gevonden.';
                    }
                } catch (PDOException $e) {
                    error_log("Admin delete fout: " . $e->getMessage());
                    $fout = 'Database fout bij verwijderen: ' . $e->getMessage();
                }
            }
            break;
            
        case 'bulk_delete':
            $idee_ids = $_POST['idee_ids'] ?? [];
            if (!empty($idee_ids) && is_array($idee_ids)) {
                try {
                    $pdo = getDatabaseConnection();
                    
                    // Valideer alle IDs
                    $clean_ids = array_map('intval', $idee_ids);
                    $clean_ids = array_filter($clean_ids, function($id) { return $id > 0; });
                    
                    if (!empty($clean_ids)) {
                        $placeholders = str_repeat('?,', count($clean_ids) - 1) . '?';
                        $stmt = $pdo->prepare("DELETE FROM ideeen WHERE id IN ($placeholders)");
                        $stmt->execute($clean_ids);
                        
                        $deleted_count = $stmt->rowCount();
                        $boodschap = "$deleted_count ideeën succesvol verwijderd.";
                        
                        // Probeer te loggen
                        try {
                            logAdminAction('Bulk Delete', "Verwijderd: $deleted_count ideeën");
                        } catch (Exception $e) {
                            error_log("Admin log fout: " . $e->getMessage());
                        }
                    } else {
                        $fout = 'Geen geldige ideeën geselecteerd.';
                    }
                } catch (PDOException $e) {
                    error_log("Admin bulk delete fout: " . $e->getMessage());
                    $fout = 'Fout bij bulk verwijdering: ' . $e->getMessage();
                }
            } else {
                $fout = 'Geen ideeën geselecteerd voor verwijdering.';
            }
            break;
            
        case 'logout':
            try {
                logAdminAction('Logout', 'Admin uitgelogd');
            } catch (Exception $e) {
                error_log("Admin log fout: " . $e->getMessage());
            }
            session_destroy();
            header('Location: admin.php');
            exit;
    }
}

// Haal statistieken op
try {
    $pdo = getDatabaseConnection();
    
    // Basis statistieken
    $stats_query = "
        SELECT 
            COUNT(*) as totaal_ideeen,
            COUNT(DISTINCT naam) as unieke_gebruikers,
            COALESCE(AVG(upvotes), 0) as gem_upvotes,
            COALESCE(AVG(downvotes), 0) as gem_downvotes,
            COALESCE(SUM(upvotes + downvotes), 0) as totaal_stemmen,
            MAX(datum) as laatste_idee
        FROM ideeen
    ";
    $stats = $pdo->query($stats_query)->fetch();
    
    // Als geen resultaten, zet defaults
    if (!$stats) {
        $stats = [
            'totaal_ideeen' => 0,
            'unieke_gebruikers' => 0, 
            'gem_upvotes' => 0,
            'gem_downvotes' => 0,
            'totaal_stemmen' => 0,
            'laatste_idee' => null
        ];
    }
    
    // Top ideeën
    $top_ideeen = $pdo->query("
        SELECT titel, naam, upvotes, downvotes, 
               (upvotes - downvotes) as score 
        FROM ideeen 
        ORDER BY score DESC, upvotes DESC 
        LIMIT 5
    ")->fetchAll();
    
    // Alle ideeën voor beheer
    $alle_ideeen = $pdo->query("
        SELECT * FROM ideeen 
        ORDER BY datum DESC
    ")->fetchAll();
    
    // Admin logs (laatste 20) - Als tabel bestaat
    $admin_logs = [];
    try {
        $admin_logs = $pdo->query("
            SELECT * FROM admin_logs 
            ORDER BY datum DESC 
            LIMIT 20
        ")->fetchAll();
    } catch (PDOException $e) {
        // Tabel bestaat mogelijk niet, dat is oké
        error_log("Admin logs tabel niet beschikbaar: " . $e->getMessage());
    }
    
} catch (PDOException $e) {
    error_log("Admin stats fout: " . $e->getMessage());
    $fout = "Database fout bij ophalen statistieken: " . $e->getMessage();
    
    // Zet defaults
    $stats = [
        'totaal_ideeen' => 0, 
        'unieke_gebruikers' => 0, 
        'gem_upvotes' => 0, 
        'gem_downvotes' => 0, 
        'totaal_stemmen' => 0, 
        'laatste_idee' => null
    ];
    $top_ideeen = $alle_ideeen = $admin_logs = [];
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TCR Ideeënbus - Admin Paneel</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🛡️</text></svg>">
</head>
<body>
    <div class="container">
        <div class="admin-header">
            <h1>🛡️ TCR Ideeënbus - Admin Paneel</h1>
            <p>Beheer ideeën, bekijk statistieken en modereer content</p>
            <div style="margin-top: 15px;">
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="danger-btn">🚪 Uitloggen</button>
                </form>
                <span style="color: rgba(255,255,255,0.8); margin-left: 15px; font-size: 14px;">
                    Ingelogd sinds: <?= date('H:i', $_SESSION['admin_login_time']) ?>
                </span>
            </div>
        </div>

        <?php if ($boodschap): ?>
            <div class="alert alert-success">
                ✅ <?= htmlspecialchars($boodschap, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($fout): ?>
            <div class="alert alert-error">
                ❌ <?= htmlspecialchars($fout, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <nav>
            <a href="index.php" class="nav-btn">💡 Idee Indienen</a>
            <a href="ideeen.php" class="nav-btn">👁️ Bekijk Ideeën</a>
            <a href="admin.php" class="nav-btn active">🛡️ Admin</a>
        </nav>

        <!-- Dashboard Statistieken -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= number_format(intval($stats['totaal_ideeen'])) ?></h3>
                <p>Totaal Ideeën</p>
            </div>
            <div class="stat-card">
                <h3><?= number_format(intval($stats['unieke_gebruikers'])) ?></h3>
                <p>Unieke Gebruikers</p>
            </div>
            <div class="stat-card">
                <h3><?= number_format(floatval($stats['gem_upvotes']), 1) ?></h3>
                <p>Gem. Likes per Idee</p>
            </div>
            <div class="stat-card">
                <h3><?= number_format(intval($stats['totaal_stemmen'])) ?></h3>
                <p>Totaal Stemmen</p>
            </div>
        </div>

        <!-- Top Ideeën -->
        <div class="form-container">
            <h2>🏆 Top 5 Populairste Ideeën</h2>
            <?php if (empty($top_ideeen)): ?>
                <p style="text-align: center; color: #7f8c8d;">Nog geen ideeën om te tonen.</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                        <thead>
                            <tr style="background: #f8f9fa; text-align: left;">
                                <th style="padding: 12px; border: 1px solid #dee2e6;">Titel</th>
                                <th style="padding: 12px; border: 1px solid #dee2e6;">Auteur</th>
                                <th style="padding: 12px; border: 1px solid #dee2e6;">👍</th>
                                <th style="padding: 12px; border: 1px solid #dee2e6;">👎</th>
                                <th style="padding: 12px; border: 1px solid #dee2e6;">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_ideeen as $idee): ?>
                                <tr>
                                    <td style="padding: 10px; border: 1px solid #dee2e6;"><?= htmlspecialchars($idee['titel'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td style="padding: 10px; border: 1px solid #dee2e6;"><?= htmlspecialchars($idee['naam'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?= intval($idee['upvotes']) ?></td>
                                    <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center;"><?= intval($idee['downvotes']) ?></td>
                                    <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; font-weight: bold;">
                                        <?= $idee['score'] >= 0 ? '+' : '' ?><?= intval($idee['score']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Ideeën Beheer -->
        <div class="form-container">
            <h2>📋 Alle Ideeën Beheren</h2>
            
            <?php if (!empty($alle_ideeen)): ?>
                <form method="POST" id="bulk-form" style="margin-bottom: 20px;">
                    <input type="hidden" name="action" value="bulk_delete">
                    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 20px; flex-wrap: wrap;">
                        <button type="button" onclick="selectAll()" class="action-btn" style="background: #6c757d;">Alles Selecteren</button>
                        <button type="button" onclick="selectNone()" class="action-btn" style="background: #6c757d;">Niets Selecteren</button>
                        <button type="submit" onclick="return confirmBulkDelete()" class="danger-btn">🗑️ Geselecteerde Verwijderen</button>
                    </div>
                    
                    <div class="ideeen-container">
                        <?php foreach ($alle_ideeen as $idee): ?>
                            <article class="idee-card">
                                <div style="display: flex; align-items: flex-start; gap: 15px;">
                                    <input type="checkbox" name="idee_ids[]" value="<?= intval($idee['id']) ?>" 
                                           style="margin-top: 5px; transform: scale(1.2);">
                                    
                                    <div style="flex: 1;">
                                        <div class="idee-header">
                                            <h3><?= htmlspecialchars($idee['titel'], ENT_QUOTES, 'UTF-8') ?></h3>
                                            <div class="idee-meta">
                                                <span>👤 <?= htmlspecialchars($idee['naam'], ENT_QUOTES, 'UTF-8') ?></span>
                                                <?php if (!empty($idee['email'])): ?>
                                                    <span>📧 <?= htmlspecialchars($idee['email'], ENT_QUOTES, 'UTF-8') ?></span>
                                                <?php endif; ?>
                                                <span>📅 <?= formatDutchDate($idee['datum']) ?></span>
                                                <span>ID: <?= intval($idee['id']) ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="idee-content" style="max-height: 100px; overflow: hidden;">
                                            <?= formatBericht($idee['bericht']) ?>
                                        </div>
                                        
                                        <div class="idee-footer">
                                            <div class="idee-actions">
                                                <span>👍 <?= intval($idee['upvotes']) ?></span>
                                                <span>👎 <?= intval($idee['downvotes']) ?></span>
                                                <span>📊 Score: <?= intval($idee['upvotes']) - intval($idee['downvotes']) ?></span>
                                            </div>
                                            <div>
                                                <button type="button" onclick="deleteIdee(<?= intval($idee['id']) ?>, '<?= htmlspecialchars(addslashes($idee['titel']), ENT_QUOTES, 'UTF-8') ?>')" 
                                                        class="danger-btn">🗑️ Verwijderen</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </form>
            <?php else: ?>
                <p style="text-align: center; color: #7f8c8d;">Nog geen ideeën om te beheren.</p>
            <?php endif; ?>
        </div>

        <!-- Admin Logs -->
        <?php if (!empty($admin_logs)): ?>
            <div class="form-container">
                <h2>📜 Recente Admin Activiteit</h2>
                <div style="overflow-x: auto; max-height: 400px; overflow-y: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="position: sticky; top: 0; background: white;">
                            <tr style="background: #f8f9fa;">
                                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Tijd</th>
                                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Actie</th>
                                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">Details</th>
                                <th style="padding: 10px; border: 1px solid #dee2e6; text-align: left;">IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admin_logs as $log): ?>
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #dee2e6; font-size: 12px;">
                                        <?= formatDutchDate($log['datum']) ?>
                                    </td>
                                    <td style="padding: 8px; border: 1px solid #dee2e6; font-weight: 600;">
                                        <?= htmlspecialchars($log['actie'], ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td style="padding: 8px; border: 1px solid #dee2e6; font-size: 14px;">
                                        <?= htmlspecialchars($log['details'] ?: '-', ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                    <td style="padding: 8px; border: 1px solid #dee2e6; font-family: monospace; font-size: 12px;">
                                        <?= htmlspecialchars($log['ip_adres'] ?: '-', ENT_QUOTES, 'UTF-8') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <footer>
            <p>&copy; 2025 TCR Ideeënbus - Admin Paneel</p>
        </footer>
    </div>

    <!-- Hidden forms voor individuele acties -->
    <form id="delete-form" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="idee_id" id="delete-idee-id">
    </form>

    <script>
        function deleteIdee(ideeId, titel) {
            if (confirm(`Weet je zeker dat je het idee "${titel}" wilt verwijderen?\n\nDeze actie kan niet ongedaan gemaakt worden.`)) {
                document.getElementById('delete-idee-id').value = ideeId;
                document.getElementById('delete-form').submit();
            }
        }
        
        function selectAll() {
            const checkboxes = document.querySelectorAll('input[name="idee_ids[]"]');
            checkboxes.forEach(cb => cb.checked = true);
        }
        
        function selectNone() {
            const checkboxes = document.querySelectorAll('input[name="idee_ids[]"]');
            checkboxes.forEach(cb => cb.checked = false);
        }
        
        function confirmBulkDelete() {
            const checked = document.querySelectorAll('input[name="idee_ids[]"]:checked');
            if (checked.length === 0) {
                alert('Selecteer eerst ideeën om te verwijderen.');
                return false;
            }
            
            return confirm(`Weet je zeker dat je ${checked.length} idee(ën) wilt verwijderen?\n\nDeze actie kan niet ongedaan gemaakt worden.`);
        }
        
        // Keyboard shortcuts voor admin
        document.addEventListener('keydown', function(e) {
            // Ctrl+A = Select All
            if (e.ctrlKey && e.key === 'a' && document.activeElement.tagName !== 'INPUT') {
                e.preventDefault();
                selectAll();
            }
            
            // Escape = Select None
            if (e.key === 'Escape') {
                selectNone();
            }
        });
        
        // Session timeout warning (25 minuten - sessie is 30 min)
        setTimeout(function() {
            if (confirm('Je sessie verloopt over 5 minuten. Wil je ingelogd blijven?')) {
                location.reload();
            }
        }, 25 * 60 * 1000);
        
        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => alert.remove(), 300);
                }, 8000);
            });
        });
    </script>
</body>
</html>