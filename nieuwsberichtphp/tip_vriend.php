<?php
require 'verbinding.php';

$artikel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($artikel_id <= 0) {
    header('Location: index.php');
    exit;
}

// Haal artikel op
$query = $verbinding->prepare("SELECT titel FROM nieuwsberichten WHERE id = ? AND actief = 1");
$query->bind_param("i", $artikel_id);
$query->execute();
$artikel = $query->get_result()->fetch_assoc();

if (!$artikel) {
    header('Location: index.php');
    exit;
}

// Verwerk formulier
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $van_email = $_POST['van_email'];
    $naar_email = $_POST['naar_email'];
    $bericht = $_POST['bericht'];

    // Voeg tip toe aan database
    $insert_query = $verbinding->prepare("INSERT INTO tip_vriend (nieuwsbericht_id, van_email, naar_email, bericht) VALUES (?, ?, ?, ?)");
    $insert_query->bind_param("isss", $artikel_id, $van_email, $naar_email, $bericht);
    
    if ($insert_query->execute()) {
        // Hier zou je normaal gesproken een e-mail versturen
        // Voor deze demo simuleren we het alleen
        $succes_bericht = "Tip is succesvol verzonden naar " . htmlspecialchars($naar_email) . "!";
    } else {
        $fout_bericht = "Er is een fout opgetreden bij het verzenden van de tip.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Tip een Vriend - <?= htmlspecialchars($artikel['titel']) ?></title>
  <style>
    body {
      margin: 0;
      font-family: 'Times New Roman', serif;
      background: #e8f4f8;
      min-height: 100vh;
      padding: 20px;
    }

    .container {
      background-color: #ffffff;
      padding: 40px;
      border: 3px solid #2c5aa0;
      max-width: 600px;
      margin: 0 auto;
    }

    h1 {
      color: #2c5aa0;
      margin-bottom: 25px;
      font-size: 28px;
      text-align: center;
    }

    .artikel-info {
      background-color: #f9f9f9;
      padding: 20px;
      margin-bottom: 30px;
      border-left: 4px solid #2c5aa0;
    }

    .artikel-info h3 {
      color: #2c5aa0;
      margin: 0 0 10px 0;
    }

    .artikel-info p {
      margin: 0;
      color: #666;
    }

    .succes {
      background-color: #d4edda;
      color: #155724;
      padding: 15px;
      border: 1px solid #c3e6cb;
      margin-bottom: 20px;
      text-align: center;
      font-weight: bold;
    }

    .fout {
      background-color: #f8d7da;
      color: #721c24;
      padding: 15px;
      border: 1px solid #f5c6cb;
      margin-bottom: 20px;
      text-align: center;
      font-weight: bold;
    }

    label {
      display: block;
      margin: 20px 0 8px;
      font-weight: bold;
      color: #333;
      font-size: 16px;
    }

    input[type="email"],
    textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 2px solid #ddd;
      box-sizing: border-box;
      font-family: 'Times New Roman', serif;
      font-size: 16px;
    }

    textarea {
      height: 120px;
      resize: vertical;
    }

    .button-group {
      display: flex;
      gap: 15px;
      margin-top: 20px;
    }

    button {
      flex: 1;
      padding: 15px;
      border: none;
      cursor: pointer;
      font-size: 18px;
      font-weight: bold;
    }

    .btn-send {
      background-color: #2c5aa0;
      color: white;
    }

    .btn-send:hover {
      background-color: #1e3f73;
    }

    .btn-cancel {
      background-color: #95a5a6;
      color: white;
    }

    .btn-cancel:hover {
      background-color: #7f8c8d;
    }

    .navigatie {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #ddd;
    }

    .navigatie a {
      color: #2c5aa0;
      text-decoration: none;
      font-weight: bold;
      margin: 0 15px;
      padding: 10px 20px;
      border: 2px solid #2c5aa0;
    }

    .navigatie a:hover {
      background-color: #2c5aa0;
      color: white;
    }

    .required {
      color: #e74c3c;
    }

    .form-help {
      color: #666;
      font-size: 14px;
      margin-top: -15px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>📧 Tip een Vriend</h1>
    
    <div class="artikel-info">
      <h3>Artikel: <?= htmlspecialchars($artikel['titel']) ?></h3>
      <p>Deel dit interessante artikel met je vrienden!</p>
    </div>

    <?php if (isset($succes_bericht)): ?>
      <div class="succes"><?= $succes_bericht ?></div>
      <div class="navigatie" style="border: none; padding-top: 0;">
        <a href="artikel.php?id=<?= $artikel_id ?>">← Terug naar artikel</a>
        <a href="index.php">Naar homepage</a>
      </div>
    <?php elseif (isset($fout_bericht)): ?>
      <div class="fout"><?= $fout_bericht ?></div>
    <?php endif; ?>

    <?php if (!isset($succes_bericht)): ?>
    <form method="post" action="">
      <label for="van_email">Jouw E-mailadres <span class="required">*</span></label>
      <input type="email" name="van_email" id="van_email" required 
             value="<?= isset($_POST['van_email']) ? htmlspecialchars($_POST['van_email']) : '' ?>">
      <div class="form-help">We gebruiken dit adres als afzender van de tip</div>

      <label for="naar_email">E-mailadres van je vriend <span class="required">*</span></label>
      <input type="email" name="naar_email" id="naar_email" required
             value="<?= isset($_POST['naar_email']) ? htmlspecialchars($_POST['naar_email']) : '' ?>">
      <div class="form-help">Het artikel wordt naar dit adres verzonden</div>

      <label for="bericht">Persoonlijk Bericht (optioneel)</label>
      <textarea name="bericht" id="bericht" placeholder="Voeg een persoonlijk bericht toe..."><?= isset($_POST['bericht']) ? htmlspecialchars($_POST['bericht']) : '' ?></textarea>
      <div class="form-help">Dit bericht wordt toegevoegd aan de e-mail</div>

      <div class="button-group">
        <button type="submit" class="btn-send">📤 Tip Verzenden</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='artikel.php?id=<?= $artikel_id ?>'">❌ Annuleren</button>
      </div>
    </form>
    <?php endif; ?>

    <?php if (!isset($succes_bericht)): ?>
    <div class="navigatie">
      <a href="artikel.php?id=<?= $artikel_id ?>">← Terug naar artikel</a>
      <a href="index.php">Naar homepage</a>
    </div>
    <?php endif; ?>
  </div>
</body>
</html>