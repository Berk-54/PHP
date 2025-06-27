<?php
/**
 * TCR Ideeënbus - Database Configuratie & Functies
 * 
 * Bevat alle database instellingen en hulpfuncties voor:
 * - Database verbinding
 * - Tekstverwerking (BBCode, smileys, filters)
 * - Beveiliging (XSS, SQL injection preventie)
 */

// Database configuratie - PAS DEZE INSTELLINGEN AAN!
define('DB_HOST', 'localhost');
define('DB_NAME', 'ideeenbus');
define('DB_USER', 'root');           // Verander naar jouw database gebruiker
define('DB_PASS', '');               // Verander naar jouw database wachtwoord
define('DB_CHARSET', 'utf8mb4');

// Admin configuratie
define('ADMIN_PASSWORD', 'tcr2025'); // Verander dit wachtwoord!

/**
 * Maak veilige database verbinding met PDO
 */
function getDatabaseConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connectie fout: " . $e->getMessage());
            die("Database verbinding mislukt. Probeer later opnieuw.");
        }
    }
    
    return $pdo;
}

/**
 * Filter scheldwoorden en vervang met ***
 */
function filterScheldwoorden($tekst) {
    $scheldwoorden = [
        'klootzak', 'eikel', 'lul', 'kut', 'hoer', 'pedo', 'kanker', 
        'mongool', 'debiel', 'idioot', 'sukkel', 'loser', 'fuck',
        'shit', 'damn', 'bitch', 'asshole', 'bastard', 'kankermongool',
        'kutwijf', 'teringlijer', 'godverdomme', 'verdomme'
    ];
    
    foreach ($scheldwoorden as $woord) {
        // Case-insensitive vervangen met woordgrenzen
        $tekst = preg_replace('/\b' . preg_quote($woord, '/') . '\b/i', '***', $tekst);
    }
    
    return $tekst;
}

/**
 * Converteer BBCode tags naar HTML
 */
function convertBBCode($tekst) {
    // Basis BBCode patterns met veiligheidscontroles
    $bbcode_patterns = [
        // Basis opmaak
        '/\[b\](.*?)\[\/b\]/is' => '<strong>$1</strong>',
        '/\[i\](.*?)\[\/i\]/is' => '<em>$1</em>',
        '/\[u\](.*?)\[\/u\]/is' => '<u>$1</u>',
        '/\[s\](.*?)\[\/s\]/is' => '<del>$1</del>',
        
        // Kleuren (alleen veilige kleuren en hex codes)
        '/\[color=(#[0-9a-fA-F]{3,6}|red|blue|green|yellow|orange|purple|pink|brown|black|white|gray|grey)\](.*?)\[\/color\]/is' => '<span style="color: $1;">$2</span>',
        
        // Tekstgrootte (beperkt tot redelijke groottes)
        '/\[size=([8-9]|[1-4][0-9]|50)\](.*?)\[\/size\]/is' => '<span style="font-size: $1px;">$2</span>',
        
        // URL links (optioneel - uitgeschakeld voor veiligheid)
        // '/\[url=(https?:\/\/[^\]]+)\](.*?)\[\/url\]/is' => '<a href="$1" target="_blank" rel="noopener">$2</a>',
    ];
    
    foreach ($bbcode_patterns as $pattern => $replacement) {
        $tekst = preg_replace($pattern, $replacement, $tekst);
    }
    
    return $tekst;
}

/**
 * Converteer tekstsmileys naar emoji of afbeeldingen
 */
function convertSmileys($tekst) {
    // Gebruik emoji voor betere compatibiliteit
    $smileys = [
        ':)'  => '😊',
        ':('  => '😢',
        ':o'  => '😮', 
        ':O'  => '😮',
        ':D'  => '😃',
        ';)'  => '😉',
        ':P'  => '😛',
        ':p'  => '😛',
        ':|'  => '😐',
        '<3'  => '❤️',
        ':/'  => '😕',
        '8)'  => '😎',
        'XD'  => '😆',
        ':x'  => '😶',
        ':X'  => '😶'
    ];
    
    // Sorteer op lengte (langste eerst) om conflicten te voorkomen
    uksort($smileys, function($a, $b) {
        return strlen($b) - strlen($a);
    });
    
    foreach ($smileys as $smiley => $emoji) {
        // Escape speciale regex karakters
        $escaped_smiley = preg_quote($smiley, '/');
        // Vervang alleen als het niet al binnen HTML tags staat
        $tekst = preg_replace('/(?<![<>])' . $escaped_smiley . '(?![<>])/', $emoji, $tekst);
    }
    
    return $tekst;
}

/**
 * Volledige berichtverwerking: filter, escape, converteer
 */
function formatBericht($bericht) {
    // Stap 1: Filter scheldwoorden
    $bericht = filterScheldwoorden($bericht);
    
    // Stap 2: HTML escape voor XSS bescherming
    $bericht = htmlspecialchars($bericht, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // Stap 3: Converteer BBCode (na HTML escape!)
    $bericht = convertBBCode($bericht);
    
    // Stap 4: Converteer smileys
    $bericht = convertSmileys($bericht);
    
    // Stap 5: Behoud regelbreaks
    $bericht = nl2br($bericht);
    
    return $bericht;
}

/**
 * Valideer email adres
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Krijg client IP adres (ook achter proxy)
 */
function getClientIP() {
    $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            // Voor X-Forwarded-For neem eerste IP
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            // Valideer IP
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

/**
 * Log admin acties voor audit trail
 */
function logAdminAction($actie, $details = '') {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("INSERT INTO admin_logs (actie, details, ip_adres) VALUES (?, ?, ?)");
        $stmt->execute([$actie, $details, getClientIP()]);
    } catch (PDOException $e) {
        error_log("Kon admin actie niet loggen: " . $e->getMessage());
    }
}

/**
 * Sanitize input voor veiligheid
 */
function sanitizeInput($input, $maxLength = 1000) {
    $input = trim($input);
    $input = stripslashes($input);
    return substr($input, 0, $maxLength);
}

/**
 * Genereer CSRF token voor formulier beveiliging
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valideer CSRF token
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Rate limiting - voorkom spam
 */
function checkRateLimit($ip, $maxRequests = 5, $timeWindow = 300) { // 5 requests per 5 minuten
    $filename = sys_get_temp_dir() . '/rate_limit_' . md5($ip);
    $now = time();
    
    $requests = [];
    if (file_exists($filename)) {
        $requests = json_decode(file_get_contents($filename), true) ?: [];
    }
    
    // Filter oude requests
    $requests = array_filter($requests, function($time) use ($now, $timeWindow) {
        return ($now - $time) < $timeWindow;
    });
    
    if (count($requests) >= $maxRequests) {
        return false; // Rate limit exceeded
    }
    
    // Voeg nieuwe request toe
    $requests[] = $now;
    file_put_contents($filename, json_encode($requests));
    
    return true;
}

/**
 * Format Nederlandse datum
 */
function formatDutchDate($datetime) {
    $datum = new DateTime($datetime);
    $now = new DateTime();
    $diff = $now->diff($datum);
    
    // Relatieve tijd voor recente berichten
    if ($diff->days == 0) {
        if ($diff->h == 0) {
            return $diff->i == 0 ? 'Net geplaatst' : $diff->i . ' minuten geleden';
        }
        return $diff->h . ' uur geleden';
    } elseif ($diff->days == 1) {
        return 'Gisteren om ' . $datum->format('H:i');
    } elseif ($diff->days < 7) {
        return $diff->days . ' dagen geleden';
    }
    
    // Absolute datum voor oudere berichten
    return $datum->format('d-m-Y H:i');
}
?>