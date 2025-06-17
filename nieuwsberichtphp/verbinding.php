<?php
$server = 'localhost';
$gebruiker = 'root';
$wachtwoord = '';
$database = 'nieuwsdb';

$verbinding = new mysqli($server, $gebruiker, $wachtwoord, $database);
if ($verbinding->connect_error) {
    die("Fout bij verbinden: " . $verbinding->connect_error);
}

// Functie om views te tellen
function verhoog_gelezen($nieuwsbericht_id) {
    global $verbinding;
    $query = $verbinding->prepare("UPDATE nieuwsberichten SET gelezen_aantal = gelezen_aantal + 1 WHERE id = ?");
    $query->bind_param("i", $nieuwsbericht_id);
    $query->execute();
}

// Functie om te controleren of bericht al gelezen is (simpele IP check)
function is_gelezen_door_ip($nieuwsbericht_id) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $session_key = "gelezen_" . $nieuwsbericht_id;
    
    if (!isset($_SESSION[$session_key])) {
        $_SESSION[$session_key] = true;
        return false; // Nog niet gelezen
    }
    return true; // Al gelezen
}

session_start();
?>