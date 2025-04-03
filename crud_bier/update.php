<?php
require_once('functions.php');

if(isset($_POST['btn_wzg'])) {
    if(updateRecord($_POST)) {
        header("Location: index.php");
        exit;
    } else {
        echo '<script>alert("Bier is NIET gewijzigd")</script>';
    }
}

if(isset($_GET['biercode'])) {  // Changed to $_GET
    $biercode = $_GET['biercode'];
    $row = getRecord($biercode);
    $brouwers = getBrouwers();
    
    if(!$row) {
        die("Bier niet gevonden met biercode: " . $biercode);
    }
} else {
    die("Geen biercode opgegeven");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Wijzig Bier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Wijzig Bier</h1>
    <form method="post">
        <input type="hidden" name="biercode" value="<?= $row['biercode'] ?>">
        
        <label for="naam">Naam:</label>
        <input type="text" id="naam" name="naam" value="<?= htmlspecialchars($row['naam']) ?>" required><br>

        <label for="soort">Soort:</label>
        <input type="text" id="soort" name="soort" value="<?= htmlspecialchars($row['soort']) ?>" required><br>

        <label for="stijl">Stijl:</label>
        <input type="text" id="stijl" name="stijl" value="<?= htmlspecialchars($row['stijl']) ?>" required><br>

        <label for="alcohol">Alcohol %:</label>
        <input type="number" id="alcohol" name="alcohol" step="0.1" 
               value="<?= htmlspecialchars($row['alcohol']) ?>" required><br>

        <label for="brouwcode">Brouwer:</label>
        <select id="brouwcode" name="brouwcode" required>
            <?php foreach($brouwers as $brouwer): ?>
                <option value="<?= $brouwer['brouwcode'] ?>" 
                    <?= ($row['brouwcode'] == $brouwer['brouwcode']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($brouwer['naam']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <input type="submit" name="btn_wzg" value="Wijzigingen opslaan">
    </form>
    <br>
    <a href='index.php'>Terug naar overzicht</a>
</body>
</html>