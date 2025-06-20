<?php
// Simpel config bestand - alleen database connectie
function getDatabaseConnection() {
    $host = 'localhost';
    $dbname = 'statistiekensysteem';
    $username = 'root';
    $password = '';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connectie fout: " . $e->getMessage());
    }
}

function registreerBezoeker() {
    $pdo = getDatabaseConnection();
    
    $land = $_POST['land'] ?? 'Nederland';
    $ip_adres = gethostbyname(gethostname());
    $provider = $_POST['provider'] ?? 'Onbekend';
    $browser = $_SERVER['HTTP_USER_AGENT'] ?? 'Onbekend';
    $datum_tijd = date('Y-m-d H:i:s');
    $referer = $_SERVER['HTTP_REFERER'] ?? 'Direct';
    
    try {
        $stmt = $pdo->prepare("INSERT INTO bezoekers (land, ip_adres, provider, browser, datum_tijd, referer) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$land, $ip_adres, $provider, $browser, $datum_tijd, $referer]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
?>