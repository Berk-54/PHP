<?php
/**
 * TCR Ideeënbus - Database Connectie Test
 * 
 * Test bestand om te controleren of alles correct werkt
 * Verwijder dit bestand na het testen!
 */

// Toon PHP errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 TCR Ideeënbus - Systeem Test</h1>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:green;} .error{color:red;} .info{color:blue;} .box{background:white;padding:15px;margin:10px 0;border-radius:8px;box-shadow:0 2px 5px rgba(0,0,0,0.1);}</style>";

// Test 1: Config bestand laden
echo "<div class='box'>";
echo "<h2>📁 Test 1: Config.php laden</h2>";
try {
    require_once 'config.php';
    echo "<span class='success'>✅ Config.php succesvol geladen</span><br>";
    echo "<span class='info'>Database: " . DB_NAME . "</span><br>";
    echo "<span class='info'>Host: " . DB_HOST . "</span><br>";
} catch (Exception $e) {
    echo "<span class='error'>❌ Fout bij laden config.php: " . $e->getMessage() . "</span>";
}
echo "</div>";

// Test 2: Database verbinding
echo "<div class='box'>";
echo "<h2>🗄️ Test 2: Database Verbinding</h2>";
try {
    $pdo = getDatabaseConnection();
    echo "<span class='success'>✅ Database verbinding succesvol</span><br>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM ideeen");
    $result = $stmt->fetch();
    echo "<span class='info'>📊 Aantal ideeën in database: " . $result['count'] . "</span><br>";
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Database verbinding mislukt: " . $e->getMessage() . "</span><br>";
    echo "<span class='info'>💡 Controleer je database instellingen in config.php</span>";
}
echo "</div>";

// Test 3: Database tabellen
echo "<div class='box'>";
echo "<h2>📋 Test 3: Database Tabellen</h2>";
try {
    $pdo = getDatabaseConnection();
    $tables = ['ideeen', 'stemmen', 'admin_logs'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "<span class='success'>✅ Tabel '$table' bestaat (records: $count)</span><br>";
        } catch (Exception $e) {
            echo "<span class='error'>❌ Tabel '$table' bestaat niet of is niet toegankelijk</span><br>";
        }
    }
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Kon tabellen niet controleren: " . $e->getMessage() . "</span>";
}
echo "</div>";

// Test 4: BBCode functie
echo "<div class='box'>";
echo "<h2>🎨 Test 4: BBCode & Smiley Functies</h2>";
try {
    $test_text = "[b]Dit is vet[/b] en [color=red]rood[/color] :) en [size=18]groot[/size]";
    $formatted = formatBericht($test_text);
    
    echo "<span class='success'>✅ BBCode functie werkt</span><br>";
    echo "<strong>Input:</strong> " . htmlspecialchars($test_text) . "<br>";
    echo "<strong>Output:</strong> " . $formatted . "<br>";
    
} catch (Exception $e) {
    echo "<span class='error'>❌ BBCode functie fout: " . $e->getMessage() . "</span>";
}
echo "</div>";

// Test 5: Admin wachtwoord
echo "<div class='box'>";
echo "<h2>🔐 Test 5: Admin Configuratie</h2>";
try {
    echo "<span class='success'>✅ Admin wachtwoord ingesteld</span><br>";
    echo "<span class='info'>🔑 Wachtwoord: " . ADMIN_PASSWORD . "</span><br>";
    echo "<span class='info'>💡 Verander dit wachtwoord in config.php voor productie!</span>";
} catch (Exception $e) {
    echo "<span class='error'>❌ Admin configuratie fout: " . $e->getMessage() . "</span>";
}
echo "</div>";

// Test 6: Bestanden
echo "<div class='box'>";
echo "<h2>📄 Test 6: Benodigde Bestanden</h2>";
$required_files = [
    'config.php' => 'Database configuratie',
    'index.php' => 'Hoofdpagina',
    'ideeen.php' => 'Ideeën overzicht', 
    'admin.php' => 'Admin paneel',
    'stem.php' => 'Stemfunctionaliteit',
    'style.css' => 'Styling'
];

foreach ($required_files as $file => $description) {
    if (file_exists($file)) {
        echo "<span class='success'>✅ $file ($description)</span><br>";
    } else {
        echo "<span class='error'>❌ $file ontbreekt! ($description)</span><br>";
    }
}
echo "</div>";

// Test 7: PHP versie en extensies
echo "<div class='box'>";
echo "<h2>⚙️ Test 7: PHP Omgeving</h2>";
echo "<span class='info'>PHP Versie: " . PHP_VERSION . "</span><br>";
echo "<span class='info'>PDO: " . (extension_loaded('pdo') ? '✅ Beschikbaar' : '❌ Niet beschikbaar') . "</span><br>";
echo "<span class='info'>PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Beschikbaar' : '❌ Niet beschikbaar') . "</span><br>";
echo "</div>";

// Samenvattng
echo "<div class='box' style='background:#e8f5e8;border:2px solid #4caf50;'>";
echo "<h2>📋 Samenvatting</h2>";
echo "<p>Als alle tests ✅ tonen, dan werkt je TCR Ideeënbus perfect!</p>";
echo "<p><strong>Volgende stappen:</strong></p>";
echo "<ul>";
echo "<li>Test de applicatie via <a href='index.php'>index.php</a></li>";
echo "<li>Bekijk ideeën via <a href='ideeen.php'>ideeen.php</a></li>";
echo "<li>Login admin via <a href='admin.php'>admin.php</a></li>";
echo "<li>⚠️ Verwijder dit test.php bestand na het testen!</li>";
echo "</ul>";
echo "</div>";

echo "<hr><p style='text-align:center;color:#666;'>🔧 TCR Ideeënbus Diagnose Tool - Verwijder dit bestand na gebruik</p>";
?>