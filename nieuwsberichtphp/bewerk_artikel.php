<?php
require 'verbinding.php';

$artikel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($artikel_id <= 0) {
    header('Location: beheer.php');
    exit;
}

// Haal artikel gegevens op
$query = $verbinding->prepare("SELECT * FROM nieuwsberichten WHERE id = ?");
$query->bind_param("i", $artikel_id);
$query->execute();
$artikel = $query->get_result()->fetch_assoc();

if (!$artikel) {
    header('Location: beheer.php');
    exit;
}

// Haal categorieën op
$categorieen = $verbinding->query("SELECT * FROM categorieen ORDER BY naam");

// Verwerk formulier
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titel = $_POST['titel'];
    $inhoud = $_POST['inhoud'];
    $categorie_id = $_POST['categorie_id'];
    $auteur = $_POST['auteur'];

    $update_query = $verbinding->prepare("UPDATE nieuwsberichten SET titel = ?, inhoud = ?, categorie_id = ?, auteur = ? WHERE id = ?");
    $update_query->bind_param("ssisi", $titel, $inhoud, $categorie_id, $auteur, $artikel_id);
    
    if ($update_query->execute()) {
        echo "<script>alert('Artikel is succesvol bijgewerkt!'); window.location.href='beheer.php';</script>";
    } else {
        echo "<script>alert('Er is een fout opgetreden bij het bijwerken.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Artikel Bewerken - NieuwsPortaal</title>
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
      max-width: 800px;
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
      padding: 15px;
      margin-bottom: 30px;
      border-left: 4px solid #2c5aa0;
    }

    .artikel-info p {
      margin: 5px 0;
      color: #666;
    }

    label {
      display: block;
      margin: 20px 0 8px;
      font-weight: bold;
      color: #333;
      font-size: 16px;
    }

    input[type="text"],
    select,
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
      height: 200px;
      resize: vertical;
    }

    .form-row {
      display: flex;
      gap: 20px;
    }

    .form-row > div {
      flex: 1;
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

    .btn-update {
      background-color: #2c5aa0;
      color: white;
    }

    .btn-update:hover {
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
  </style>
</head>
<body>
  <div class="container">
    <h1>Artikel Bewerken</h1>
    
    <div class="artikel-info">
      <p><strong>Artikel ID:</strong> <?= $artikel['id'] ?></p>
      <p><strong>Aangemaakt:</strong> <?= date('d-m-Y H:i', strtotime($artikel['aangemaakt'])) ?></p>
      <p><strong>Laatst bijgewerkt:</strong> <?= date('d-m-Y H:i', strtotime($artikel['bijgewerkt'])) ?></p>
      <p><strong>Aantal keer gelezen:</strong> <?= $artikel['gelezen_aantal'] ?></p>
    </div>
    
    <form method="post" action="">
      <label for="titel">Titel <span class="required">*</span></label>
      <input type="text" name="titel" id="titel" required maxlength="255" value="<?= htmlspecialchars($artikel['titel']) ?>">

      <div class="form-row">
        <div>
          <label for="categorie_id">Categorie <span class="required">*</span></label>
          <select name="categorie_id" id="categorie_id" required>
            <option value="">-- Selecteer categorie --</option>
            <?php while ($row = $categorieen->fetch_assoc()): ?>
              <option value="<?= $row['id'] ?>" <?= $artikel['categorie_id'] == $row['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($row['naam']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div>
          <label for="auteur">Auteur <span class="required">*</span></label>
          <input type="text" name="auteur" id="auteur" required maxlength="100" value="<?= htmlspecialchars($artikel['auteur']) ?>">
        </div>
      </div>

      <label for="inhoud">Artikel Inhoud <span class="required">*</span></label>
      <textarea name="inhoud" id="inhoud" required><?= htmlspecialchars($artikel['inhoud']) ?></textarea>

      <div class="button-group">
        <button type="submit" class="btn-update">💾 Wijzigingen Opslaan</button>
        <button type="button" class="btn-cancel" onclick="window.location.href='beheer.php'">❌ Annuleren</button>
      </div>
    </form>

    <div class="navigatie">
      <a href="beheer.php">← Terug naar beheer</a>
      <a href="artikel.php?id=<?= $artikel['id'] ?>">Artikel bekijken</a>
    </div>
  </div>
</body>
</html>