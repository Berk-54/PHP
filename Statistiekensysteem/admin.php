<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Statistieken Filter</title>
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
            box-sizing: border-box;
        }
        
        .sidebar label {
            display: block;
            font-size: 11px;
            margin-bottom: 2px;
            font-weight: normal;
        }
        
        .sidebar input {
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
            cursor: pointer;
        }
        
        .sidebar button:hover {
            background: #e0e0e0;
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
        
        .col-id { width: 30px; text-align: center; }
        .col-land { width: 100px; }
        .col-ip1 { width: 90px; }
        .col-ip2 { width: 90px; }
        .col-browser { width: 350px; }
        .col-datum { width: 80px; }
        .col-tijd { width: 60px; }
        .col-referer { width: 400px; }
        
        .browser-cell {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 350px;
        }
        
        .referer-cell {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <form method="GET">
                <label>IP-adres:</label>
                <input type="text" name="ip_filter" value="<?= htmlspecialchars($_GET['ip_filter'] ?? '') ?>">
                
                <label>Land:</label>
                <input type="text" name="land_filter" value="<?= htmlspecialchars($_GET['land_filter'] ?? '') ?>">
                
                <label>Provider:</label>
                <input type="text" name="provider_filter" value="<?= htmlspecialchars($_GET['provider_filter'] ?? '') ?>">
                
                <label>Browser:</label>
                <input type="text" name="browser_filter" value="<?= htmlspecialchars($_GET['browser_filter'] ?? '') ?>">
                
                <label>Startdatum:</label>
                <input type="date" name="startdatum" value="<?= htmlspecialchars($_GET['startdatum'] ?? '') ?>">
                
                <label>Einddatum:</label>
                <input type="date" name="einddatum" value="<?= htmlspecialchars($_GET['einddatum'] ?? '') ?>">
                
                <div style="margin-top: 10px;">
                    <button type="submit">Filter</button>
                    <button type="button" onclick="window.location.href='admin.php'">Reset</button>
                </div>
            </form>
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
                    <?php
                    // Database configuratie
                    $host = 'localhost';
                    $dbname = 'statistiekensysteem';
                    $username = 'root';
                    $password = '';
                    
                    try {
                        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        // Build query with filters
                        $sql = "SELECT * FROM bezoekers WHERE 1=1";
                        $params = [];
                        
                        if (!empty($_GET['ip_filter'])) {
                            $sql .= " AND ip_adres LIKE ?";
                            $params[] = '%' . $_GET['ip_filter'] . '%';
                        }
                        
                        if (!empty($_GET['land_filter'])) {
                            $sql .= " AND land LIKE ?";
                            $params[] = '%' . $_GET['land_filter'] . '%';
                        }
                        
                        if (!empty($_GET['provider_filter'])) {
                            $sql .= " AND provider LIKE ?";
                            $params[] = '%' . $_GET['provider_filter'] . '%';
                        }
                        
                        if (!empty($_GET['browser_filter'])) {
                            $sql .= " AND browser LIKE ?";
                            $params[] = '%' . $_GET['browser_filter'] . '%';
                        }
                        
                        if (!empty($_GET['startdatum'])) {
                            $sql .= " AND DATE(datum_tijd) >= ?";
                            $params[] = $_GET['startdatum'];
                        }
                        
                        if (!empty($_GET['einddatum'])) {
                            $sql .= " AND DATE(datum_tijd) <= ?";
                            $params[] = $_GET['einddatum'];
                        }
                        
                        $sql .= " ORDER BY id ASC";
                        
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($results as $row) {
                            // Split IP address
                            $ip_parts = explode('.', $row['ip_adres']);
                            $ip1 = (isset($ip_parts[0]) && isset($ip_parts[1])) ? $ip_parts[0] . '.' . $ip_parts[1] : '';
                            $ip2 = (isset($ip_parts[2]) && isset($ip_parts[3])) ? $ip_parts[2] . '.' . $ip_parts[3] : '';
                            
                            // Format date and time
                            $datetime = new DateTime($row['datum_tijd']);
                            $datum = $datetime->format('Y-m-d');
                            $tijd = $datetime->format('H:i:s');
                            
                            echo "<tr>";
                            echo "<td class='col-id'>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td class='col-land'>" . htmlspecialchars($row['land']) . "</td>";
                            echo "<td class='col-ip1'>" . htmlspecialchars($ip1) . "</td>";
                            echo "<td class='col-ip2'>" . htmlspecialchars($ip2) . "</td>";
                            echo "<td class='col-browser'><div class='browser-cell' title='" . htmlspecialchars($row['browser']) . "'>" . htmlspecialchars($row['browser']) . "</div></td>";
                            echo "<td class='col-datum'>" . $datum . "</td>";
                            echo "<td class='col-tijd'>" . $tijd . "</td>";
                            echo "<td class='col-referer'><div class='referer-cell' title='" . htmlspecialchars($row['referer']) . "'>" . htmlspecialchars($row['referer']) . "</div></td>";
                            echo "</tr>";
                        }
                        
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='8' style='color: red; padding: 20px; text-align: center;'>";
                        echo "Database fout: " . htmlspecialchars($e->getMessage());
                        echo "<br>Zorg dat XAMPP/WAMP draait en de database 'statistiekensysteem' bestaat.";
                        echo "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>