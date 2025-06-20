<?php
// index.php - GEFIXT
session_start();
require_once 'functions.php';

$display = $_SESSION['display'] ?? '';
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

// Verwerk formulier
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'number':
            $value = $_POST['value'] ?? '';
            $display .= $value;
            break;
            
        case 'clear':
            $display = '';
            break;
            
        case 'calculate':
            if($display) {
                $result = calculate($display);
                if($result !== '') {
                    saveToDatabase($display, $result);
                    $display = $result;
                    $message = 'Berekening opgeslagen in database';
                }
            }
            break;
            
        case 'save':
            if($display) {
                saveToDatabase($display, $display);
                $message = 'Opgeslagen in database';
            }
            break;
            
        case 'load':
            $calc = loadFromDatabase();
            if($calc) {
                $display = $calc['expression'];
                $message = 'Geladen uit database';
            }
            break;
            
        case 'clear_db':
            clearDatabase();
            $message = 'Database gewist';
            break;
            
        case 'load_calc':
            $id = $_POST['calc_id'];
            $calc = loadCalculationById($id);
            if($calc) {
                $display = $calc['expression'];
                $message = 'Berekening geladen';
            }
            break;
    }
}

$_SESSION['display'] = $display;
if($message) $_SESSION['message'] = $message;

$calculations = getAllCalculations();
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
            width: 320px;
        }
        
        .display {
            width: 100%;
            height: 40px;
            font-size: 16px;
            text-align: right;
            padding: 8px;
            border: 2px solid #999;
            margin-bottom: 15px;
            font-family: monospace;
            box-sizing: border-box;
        }
        
        .buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 3px;
            margin-bottom: 15px;
        }
        
        .btn-form {
            margin: 0;
            padding: 0;
            display: contents;
        }
        
        .btn {
            height: 45px;
            border: 1px solid #999;
            background: #f0f0f0;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        
        .btn:hover {
            background: #e0e0e0;
        }
        
        .btn:active {
            background: #d0d0d0;
        }
        
        .controls {
            margin-bottom: 15px;
            text-align: center;
        }
        
        .controls button {
            margin: 3px;
            padding: 8px 12px;
            font-size: 12px;
            border: 1px solid #ccc;
            background: white;
            cursor: pointer;
        }
        
        .controls button:hover {
            background: #f0f0f0;
        }
        
        .message {
            padding: 8px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            margin: 10px 0;
            font-size: 12px;
            text-align: center;
        }
        
        .history {
            flex: 1;
            background: white;
            padding: 15px;
            border: 1px solid #ccc;
        }
        
        .history h3 {
            margin-bottom: 15px;
            font-size: 16px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .history-list {
            max-height: 350px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            background: #fafafa;
        }
        
        .history-item {
            padding: 8px;
            margin-bottom: 5px;
            background: white;
            border: 1px solid #ddd;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .history-item:hover {
            background: #f0f8ff;
        }
        
        .history-item button {
            padding: 4px 8px;
            font-size: 10px;
            border: 1px solid #007bff;
            background: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 3px;
        }
        
        .history-item button:hover {
            background: #0056b3;
        }
        
        .examples {
            margin-top: 15px;
            padding: 15px;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            font-size: 12px;
        }
        
        .examples h4 {
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .examples p {
            margin: 3px 0;
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
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="clear" class="btn">C</button>
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">(</button>
                    <input type="hidden" name="value" value="(">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">)</button>
                    <input type="hidden" name="value" value=")">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">/</button>
                    <input type="hidden" name="value" value="/">
                </form>
                
                <!-- Rij 2 -->
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">7</button>
                    <input type="hidden" name="value" value="7">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">8</button>
                    <input type="hidden" name="value" value="8">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">9</button>
                    <input type="hidden" name="value" value="9">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">*</button>
                    <input type="hidden" name="value" value="*">
                </form>
                
                <!-- Rij 3 -->
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">4</button>
                    <input type="hidden" name="value" value="4">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">5</button>
                    <input type="hidden" name="value" value="5">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">6</button>
                    <input type="hidden" name="value" value="6">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">-</button>
                    <input type="hidden" name="value" value="-">
                </form>
                
                <!-- Rij 4 -->
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">1</button>
                    <input type="hidden" name="value" value="1">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">2</button>
                    <input type="hidden" name="value" value="2">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">3</button>
                    <input type="hidden" name="value" value="3">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">+</button>
                    <input type="hidden" name="value" value="+">
                </form>
                
                <!-- Rij 5 -->
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">0</button>
                    <input type="hidden" name="value" value="0">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">.</button>
                    <input type="hidden" name="value" value=".">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">Mod</button>
                    <input type="hidden" name="value" value="Mod">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="calculate" class="btn">=</button>
                </form>
                
                <!-- Rij 6 -->
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">√</button>
                    <input type="hidden" name="value" value="√(">
                </form>
                <form method="POST" class="btn-form">
                    <button type="submit" name="action" value="number" class="btn">^</button>
                    <input type="hidden" name="value" value="^">
                </form>
                <button type="button" class="btn">decimaal</button>
                <button type="button" class="btn">←</button>
            </div>
            
            <div class="controls">
                <form method="POST" style="display: inline;">
                    <button type="submit" name="action" value="save">Opslaan naar DB</button>
                    <button type="submit" name="action" value="load">Laden uit DB</button>
                    <button type="submit" name="action" value="clear_db">DB Wissen</button>
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
                                <button type="submit" name="action" value="load_calc">Laden</button>
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
                <p><strong>8^2 = 64</strong></p>
                <p><strong>√(25) = 5</strong></p>
                <p><strong>8+9 = 17</strong></p>
                <p><strong>5-2 = 3</strong></p>
                <p><strong>16+5 = 21</strong></p>
            </div>
        </div>
    </div>
</body>
</html>