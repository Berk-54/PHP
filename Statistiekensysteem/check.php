<?php
// Eenvoudige check voor je systeem
echo "<h2>Statistiekensysteem Check</h2>";

// 1. Check of bestanden bestaan
$files = ['config.php', 'admin.php', 'index.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file bestaat<br>";
    } else {
        echo "❌ $file ontbreekt<br>";
    }
}

// 2. Test database verbinding
try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "");
    echo "✅ MySQL verbinding werkt<br>";
    
    // Check database
    $stmt = $pdo->query("SHOW DATABASES LIKE 'statistiekensysteem'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Database 'statistiekensysteem' bestaat<br>";
        
        // Connect to database
        $pdo = new PDO("mysql:host=localhost;dbname=statistiekensysteem;charset=utf8mb4", "root", "");
        
        // Check table
        $stmt = $pdo->query("SHOW TABLES LIKE 'bezoekers'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabel 'bezoekers' bestaat<br>";
            
            // Count records
            $stmt = $pdo->query("SELECT COUNT(*) FROM bezoekers");
            $count = $stmt->fetchColumn();
            echo "✅ Records in tabel: $count<br>";
            
            if ($count > 0) {
                echo "<br><strong>Alles werkt! Ga naar:</strong><br>";
                echo "<a href='admin.php' style='font-size: 18px; color: blue;'>→ ADMIN.PHP (De tabel die je wilt)</a><br>";
                echo "<a href='index.php'>→ index.php (Hoofdpagina)</a><br>";
            } else {
                echo "<br>❌ Geen testdata. Importeer de SQL file eerst.";
            }
            
        } else {
            echo "❌ Tabel 'bezoekers' ontbreekt<br>";
            echo "Importeer het .sql bestand in phpMyAdmin";
        }
        
    } else {
        echo "❌ Database 'statistiekensysteem' ontbreekt<br>";
        echo "Maak de database aan in phpMyAdmin";
    }
    
} catch (PDOException $e) {
    echo "❌ Database fout: " . $e->getMessage() . "<br>";
    echo "Start XAMPP/WAMP en zorg dat MySQL draait";
}

echo "<hr>";
echo "<h3>Snelle Setup:</h3>";
echo "1. Start XAMPP/WAMP<br>";
echo "2. Open phpMyAdmin (http://localhost/phpmyadmin)<br>";
echo "3. Maak database 'statistiekensysteem' aan<br>";
echo "4. Importeer het .sql bestand<br>";
echo "5. Ga naar admin.php<br>";
?>

<style>
body { font-family: Arial; padding: 20px; }
</style>