<?php
// WERKENDE REKENMACHINE - ALLES IN 1 BESTAND
session_start();

// Database setup
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'calculator';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");
    $pdo->exec("CREATE TABLE IF NOT EXISTS calculations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        expression VARCHAR(255) NOT NULL,
        result VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Initialiseer variabelen
$display = isset($_SESSION['display']) ? $_SESSION['display'] : '';
$message = '';

// Verwerk POST data
if ($_POST) {
    if (isset($_POST['btn_value'])) {
        // Getal of operator toegevoegd
        $display .= $_POST['btn_value'];
    } elseif (isset($_POST['clear'])) {
        // Clear knop
        $display = '';
        $message = 'Display gewist';
    } elseif (isset($_POST['calculate'])) {
        // Bereken
        if (!empty($display)) {
            try {
                $expression = $display;
                $calc = str_replace(['√(', '^', 'Mod'], ['sqrt(', '**', '%'], $expression);
                $result = eval("return $calc;");
                
                if (is_numeric($result)) {
                    // Sla op in database
                    $stmt = $pdo->prepare("INSERT INTO calculations (expression, result) VALUES (?, ?)");
                    $stmt->execute([$expression, $result]);
                    
                    $display = $result;
                    $message = 'Berekening voltooid en opgeslagen!';
                } else {
                    $message = 'Fout in berekening';
                }
            } catch (Exception $e) {
                $message = 'Fout: ongeldig';
                $display = '';
            }
        }
    } elseif (isset($_POST['save_db'])) {
        // Opslaan naar database
        if (!empty($display)) {
            $stmt = $pdo->prepare("INSERT INTO calculations (expression, result) VALUES (?, ?)");
            $stmt->execute([$display, $display]);
            $message = 'Opgeslagen in database';
        }
    } elseif (isset($_POST['load_db'])) {
        // Laden uit database
        $stmt = $pdo->query("SELECT * FROM calculations ORDER BY id DESC LIMIT 1");
        $row = $stmt->fetch();
        if ($row) {
            $display = $row['expression'];
            $message = 'Geladen uit database';
        } else {
            $message = 'Geen berekeningen gevonden';
        }
    } elseif (isset($_POST['clear_db'])) {
        // Database wissen
        $pdo->exec("DELETE FROM calculations");
        $message = 'Database gewist';
    } elseif (isset($_POST['load_calc'])) {
        // Specifieke berekening laden
        $id = $_POST['calc_id'];
        $stmt = $pdo->prepare("SELECT * FROM calculations WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $display = $row['expression'];
            $message = "Berekening ID $id geladen";
        }
    }
}

// Sla display op in sessie
$_SESSION['display'] = $display;

// Haal alle berekeningen op
$stmt = $pdo->query("SELECT * FROM calculations ORDER BY id DESC LIMIT 20");
$calculations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case 4: uitgebreide rekenmachine</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        .container {
            display: flex;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .calculator {
            background: white;
            padding: 15px;
            border: 1px solid #ccc;
            width: 280px;
        }
        
        .display {
            width: 100%;
            height: 40px;
            font-size: 16px;
            text-align: right;
            padding: 5px;
            border: 1px solid #999;
            margin-bottom: 10px;
            font-family: monospace;
        }
        
        .buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2px;
            margin-bottom: 10px;
        }
        
        .btn {
            height: 35px;
            border: 1px solid #999;
            background: #f9f9f9;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn:hover {
            background: #e9e9e9;
        }
        
        .controls {
            margin-bottom: 10px;
        }
        
        .controls button {
            margin: 2px;
            padding: 5px 10px;
            font-size: 12px;
            cursor: pointer;
        }
        
        .message {
            padding: 5px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            margin: 5px 0;
            font-size: 12px;
        }
        
        .history {
            flex: 1;
            background: white;
            padding: 15px;
            border: 1px solid #ccc;
        }
        
        .history h3 {
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .history-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 5px;
        }
        
        .history-item {
            padding: 5px;
            margin-bottom: 2px;
            background: #f9f9f9;
            border: 1px solid #eee;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
        }
        
        .history-item button {
            padding: 2px 5px;
            font-size: 10px;
            cursor: pointer;
        }
        
        .examples {
            margin-top: 10px;
            padding: 10px;
            background: #fff3cd;
            font-size: 12px;
        }
        
        .examples p {
            margin: 2px 0;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <h1>Case 4: uitgebreide rekenmachine</h1>
    
    <div class="container">
        <div class="calculator">
            <input type="text" class="display" value="<?= htmlspecialchars($display) ?>" readonly>
            
            <div class="buttons">
                <!-- Rij 1 -->
                <form method="POST" style="display: inline;">
                    <button type="submit" name="clear" class="btn">C</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="(" class="btn">(</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value=")" class="btn">)</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="/" class="btn">/</button>
                </form>
                
                <!-- Rij 2 -->
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="7" class="btn">7</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="8" class="btn">8</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="9" class="btn">9</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="*" class="btn">*</button>
                </form>
                
                <!-- Rij 3 -->
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="4" class="btn">4</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="5" class="btn">5</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="6" class="btn">6</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="-" class="btn">-</button>
                </form>
                
                <!-- Rij 4 -->
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="1" class="btn">1</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="2" class="btn">2</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="3" class="btn">3</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="+" class="btn">+</button>
                </form>
                
                <!-- Rij 5 -->
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="0" class="btn">0</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="." class="btn">.</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="Mod" class="btn">Mod</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="calculate" class="btn">=</button>
                </form>
                
                <!-- Rij 6 -->
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="√(" class="btn">√</button>
                </form>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="btn_value" value="^" class="btn">^</button>
                </form>
                <button type="button" class="btn">decimaal</button>
                <button type="button" class="btn">←</button>
            </div>
            
            <div class="controls">
                <form method="POST" style="display: inline;">
                    <button type="submit" name="save_db">Opslaan naar DB</button>
                    <button type="submit" name="load_db">Laden uit DB</button>
                    <button type="submit" name="clear_db" onclick="return confirm('Weet je het zeker?')">DB Wissen</button>
                </form>
            </div>
            
            <?php if($message): ?>
                <div class="message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
        </div>
        
        <div class="history">
            <h3>Database berekeningen</h3>
            <div class="history-list">
                <?php if(empty($calculations)): ?>
                    <p>Geen berekeningen</p>
                <?php else: ?>
                    <?php foreach($calculations as $calc): ?>
                        <div class="history-item">
                            <div>
                                <strong>ID: <?= $calc['id'] ?></strong><br>
                                <?= htmlspecialchars($calc['expression']) ?> = <?= $calc['result'] ?><br>
                                <small><?= $calc['created_at'] ?></small>
                            </div>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="calc_id" value="<?= $calc['id'] ?>">
                                <button type="submit" name="load_calc">Laden</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="examples">
                <h4>Voorbeelden</h4>
                <p><strong>8+3 = 11</strong></p>
                <p><strong>3*2 = 6</strong></p>
                <p><strong>8*2 = 16</strong></p>
                <p><strong>7+2 = 9</strong></p>
                <p><strong>10Mod3 = 1</strong></p>
                <p><strong>6+3 = 9</strong></p>
                <p><strong>√(25) = 5</strong></p>
                <p><strong>8^2 = 64</strong></p>
                <p><strong>5-2 = 3</strong></p>
                <p><strong>9*3 = 27</strong></p>
            </div>
        </div>
    </div>
</body>
</html>