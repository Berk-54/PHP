<?php
// Database configuratie
$host = 'localhost';
$dbname = 'statistiekensysteem';
$username = 'root';
$password = '';

// Database verbinding
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database fout: " . $e->getMessage());
}

// Nieuwe bezoeker toevoegen als formulier is ingediend
if ($_POST['action'] ?? '' === 'add_visitor') {
    $land = $_POST['new_land'] ?? '';
    $provider = $_POST['new_provider'] ?? '';
    $browser = $_POST['new_browser'] ?? '';
    $ip_adres = $_POST['new_ip'] ?: gethostbyname(gethostname());
    $referer = $_POST['new_referer'] ?: 'Direct';
    $datum_tijd = date('Y-m-d H:i:s');
    
    if ($land && $provider) {
        $stmt = $pdo->prepare("INSERT INTO bezoekers (land, ip_adres, provider, browser, datum_tijd, referer) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$land, $ip_adres, $provider, $browser, $datum_tijd, $referer]);
        $success_message = "Nieuwe bezoeker toegevoegd!";
    }
}

// Filters ophalen
$ip_filter = $_GET['ip_filter'] ?? '';
$land_filter = $_GET['land_filter'] ?? '';
$provider_filter = $_GET['provider_filter'] ?? '';
$browser_filter = $_GET['browser_filter'] ?? '';
$startdatum = $_GET['startdatum'] ?? '';
$einddatum = $_GET['einddatum'] ?? '';

// Query bouwen met filters
$sql = "SELECT * FROM bezoekers WHERE 1=1";
$params = [];

if ($ip_filter) {
    $sql .= " AND ip_adres LIKE ?";
    $params[] = '%' . $ip_filter . '%';
}
if ($land_filter) {
    $sql .= " AND land LIKE ?";
    $params[] = '%' . $land_filter . '%';
}
if ($provider_filter) {
    $sql .= " AND provider LIKE ?";
    $params[] = '%' . $provider_filter . '%';
}
if ($browser_filter) {
    $sql .= " AND browser LIKE ?";
    $params[] = '%' . $browser_filter . '%';
}
if ($startdatum) {
    $sql .= " AND DATE(datum_tijd) >= ?";
    $params[] = $startdatum;
}
if ($einddatum) {
    $sql .= " AND DATE(datum_tijd) <= ?";
    $params[] = $einddatum;
}

$sql .= " ORDER BY id ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Statistieken Systeem</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            background: #f5f5f5;
        }
        
        .container {
            display: flex;
            height: 100vh;
        }
        
        .sidebar {
            width: 180px;
            background: #e8e8e8;
            border-right: 1px solid #999;
            padding: 10px;
            overflow-y: auto;
        }
        
        .sidebar h3 {
            font-size: 12px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .sidebar label {
            display: block;
            font-size: 11px;
            margin-bottom: 2px;
            font-weight: normal;
        }
        
        .sidebar input, .sidebar select {
            width: 100%;
            height: 20px;
            border: 1px solid #999;
            font-size: 11px;
            padding: 2px 4px;
            margin-bottom: 8px;
            background: white;
        }
        
        .sidebar button {
            height: 22px;
            border: 1px solid #999;
            background: #f0f0f0;
            font-size: 11px;
            padding: 0 8px;
            margin-right: 4px;
            margin-bottom: 4px;
            cursor: pointer;
        }
        
        .sidebar button:hover {
            background: #e0e0e0;
        }
        
        .add-section {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
        }
        
        .content {
            flex: 1;
            overflow: auto;
            background: white;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            background: white;
        }
        
        th {
            background: #e0e0e0;
            border: 1px solid #999;
            padding: 4px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            height: 20px;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        
        td {
            border: 1px solid #999;
            padding: 2px 6px;
            height: 18px;
            vertical-align: top;
            font-size: 11px;
        }
        
        tr:nth-child(even) {
            background: #f8f8f8;
        }
        
        tr:hover {
            background: #e6f3ff;
        }
        
        .col-id { width: 30px; text-align: center; }
        .col-land { width: 100px; }
        .col-ip1 { width: 90px; }
        .col-ip2 { width: 90px; }
        .col-browser { width: 350px; }
        .col-datum { width: 80px; }
        .col-tijd { width: 60px; }
        .col-referer { width: 400px; }
        
        .browser-cell, .referer-cell {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 5px;
            margin-bottom: 10px;
            border-radius: 3px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <!-- Filter Section -->
            <h3>🔍 Filters</h3>
            <form method="GET">
                <label>IP-adres:</label>
                <input type="text" name="ip_filter" value="<?= htmlspecialchars($ip_filter) ?>" placeholder="IP zoeken...">
                
                <label>Land:</label>
                <input type="text" name="land_filter" value="<?= htmlspecialchars($land_filter) ?>" placeholder="Land zoeken...">
                
                <label>Provider:</label>
                <input type="text" name="provider_filter" value="<?= htmlspecialchars($provider_filter) ?>" placeholder="Provider zoeken...">
                
                <label>Browser:</label>
                <input type="text" name="browser_filter" value="<?= htmlspecialchars($browser_filter) ?>" placeholder="Browser zoeken...">
                
                <label>Startdatum:</label>
                <input type="date" name="startdatum" value="<?= htmlspecialchars($startdatum) ?>">
                
                <label>Einddatum:</label>
                <input type="date" name="einddatum" value="<?= htmlspecialchars($einddatum) ?>">
                
                <div style="margin-top: 8px;">
                    <button type="submit">Filter</button>
                    <button type="button" onclick="window.location.href='index.php'">Reset</button>
                </div>
            </form>
            
            <!-- Add New Visitor Section -->
            <div class="add-section">
                <h3>➕ Nieuwe Bezoeker</h3>
                <?php if (isset($success_message)): ?>
                    <div class="success"><?= htmlspecialchars($success_message) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="action" value="add_visitor">
                    
                    <label>Land:</label>
                    <select name="new_land" required>
                        <option value="">Kies land</option>
                        <option value="Nederland">Nederland</option>
                        <option value="België">België</option>
                        <option value="Duitsland">Duitsland</option>
                        <option value="Frankrijk">Frankrijk</option>
                        <option value="Spanje">Spanje</option>
                        <option value="Italië">Italië</option>
                        <option value="Verenigd Koninkrijk">VK</option>
                        <option value="Polen">Polen</option>
                        <option value="Zweden">Zweden</option>
                        <option value="Turkiye">Turkiye</option>
                    </select>
                    
                    <label>Provider:</label>
                    <select name="new_provider" required>
                        <option value="">Kies provider</option>
                        <option value="KPN">KPN</option>
                        <option value="Ziggo">Ziggo</option>
                        <option value="T-Mobile">T-Mobile</option>
                        <option value="Vodafone">Vodafone</option>
                        <option value="Proximus">Proximus</option>
                        <option value="Orange">Orange</option>
                        <option value="Telenet">Telenet</option>
                        <option value="Deutsche Telekom">Deutsche Telekom</option>
                    </select>
                    
                    <label>IP-adres (optioneel):</label>
                    <input type="text" name="new_ip" placeholder="Auto-detect">
                    
                    <label>Browser (optioneel):</label>
                    <input type="text" name="new_browser" placeholder="Auto-detect">
                    
                    <label>Referer (optioneel):</label>
                    <input type="text" name="new_referer" placeholder="Direct">
                    
                    <button type="submit" style="width: 100%; margin-top: 5px;">Toevoegen</button>
                </form>
            </div>
        </div>
        
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th class="col-id">ID</th>
                        <th class="col-land">Land</th>
                        <th class="col-ip1">IP1</th>
                        <th class="col-ip2">IP2</th>
                        <th class="col-browser">Browser</th>
                        <th class="col-datum">Datum</th>
                        <th class="col-tijd">Tijd</th>
                        <th class="col-referer">Referer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): 
                        // Split IP address
                        $ip_parts = explode('.', $row['ip_adres']);
                        $ip1 = (isset($ip_parts[0]) && isset($ip_parts[1])) ? $ip_parts[0] . '.' . $ip_parts[1] : '';
                        $ip2 = (isset($ip_parts[2]) && isset($ip_parts[3])) ? $ip_parts[2] . '.' . $ip_parts[3] : '';
                        
                        // Format date and time
                        $datetime = new DateTime($row['datum_tijd']);
                        $datum = $datetime->format('Y-m-d');
                        $tijd = $datetime->format('H:i:s');
                    ?>
                        <tr>
                            <td class="col-id"><?= htmlspecialchars($row['id']) ?></td>
                            <td class="col-land"><?= htmlspecialchars($row['land']) ?></td>
                            <td class="col-ip1"><?= htmlspecialchars($ip1) ?></td>
                            <td class="col-ip2"><?= htmlspecialchars($ip2) ?></td>
                            <td class="col-browser">
                                <div class="browser-cell" title="<?= htmlspecialchars($row['browser']) ?>">
                                    <?= htmlspecialchars($row['browser']) ?>
                                </div>
                            </td>
                            <td class="col-datum"><?= $datum ?></td>
                            <td class="col-tijd"><?= $tijd ?></td>
                            <td class="col-referer">
                                <div class="referer-cell" title="<?= htmlspecialchars($row['referer']) ?>">
                                    <?= htmlspecialchars($row['referer']) ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($results)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 20px; color: #666;">
                                Geen gegevens gevonden. 
                                <?php if ($ip_filter || $land_filter || $provider_filter || $browser_filter || $startdatum || $einddatum): ?>
                                    Probeer andere filters.
                                <?php else: ?>
                                    Voeg eerst wat bezoekers toe.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>