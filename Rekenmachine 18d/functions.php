<?php
// functions.php - Functies exact zoals screenshot

require_once 'database.php';

function calculate($expression) {
    try {
        if (empty($expression)) return '';
        
        // Vervang speciale functies
        $calc = str_replace('√(', 'sqrt(', $expression);
        $calc = str_replace('^', '**', $calc);
        $calc = str_replace('Mod', '%', $calc);
        
        $result = eval("return $calc;");
        
        if (!is_numeric($result)) return '';
        
        return $result;
        
    } catch (Exception $e) {
        return '';
    }
}

function saveToDatabase($expression, $result) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO calculations (expression, result) VALUES (?, ?)");
        $stmt->execute([$expression, $result]);
    } catch (PDOException $e) {
        // Stille fout
    }
}

function loadFromDatabase() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM calculations ORDER BY id DESC LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}

function getAllCalculations() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM calculations ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function clearDatabase() {
    global $pdo;
    
    try {
        $pdo->exec("DELETE FROM calculations");
    } catch (PDOException $e) {
        // Stille fout
    }
}

function loadCalculationById($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM calculations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}
?>