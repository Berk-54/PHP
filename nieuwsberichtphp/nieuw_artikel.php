<?php
require 'verbinding.php';

// Haal categorieën op
$categorieen = $verbinding->query("SELECT * FROM categorieen ORDER BY naam");

// Verwerk formulier
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titel = $_POST['titel'];
    $inhoud = $_POST['inhoud'];
    $categorie_id = $_POST['categorie_id'];
    $auteur = $_POST['auteur'];

    $query = $verbinding->prepare("INSERT INTO nieuwsberichten (titel, inhoud, categorie_id, auteur) VALUES (?, ?, ?, ?)");
    $query->bind_param("ssis", $titel, $inhoud, $categorie_id, $auteur);
    
    if ($query->execute()) {
        echo "<script>alert('Nieuwsbericht is succesvol toegevoegd!'); window.location.href='beheer.php';</script>";
    } else {
        echo "<script>alert('Er is een fout opgetreden bij het toevoegen.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Nieuw Artikel - NieuwsPortaal</title>
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

    button {
      width: 100%;
      padding: 15px;
      background-color: #2c5aa0;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 18px;
      font-weight: bold;
      margin-top: 20px;
    }

    button:hover {
      background-color: #1e3f73;
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
    <h1>Nieuw Artikel Toevoegen</h1>
    
    <form method="post" action="">
      <label for="titel">Titel <span class="required">*</span></label>
      <input type="text" name="titel" id="titel" required maxlength="255">
      <div class="form-help">Geef een korte, aantrekkelijke titel voor het artikel</div>

      <div class="form-row">
        <div>
          <label for="categorie_id">Categorie <span class="required">*</span></label>
          <select name="categorie_id" id="categorie_id" required>
            <option value="">-- Selecteer categorie --</option>
            <?php while ($row = $categorieen->fetch_assoc()): ?>
              <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['naam']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div>
          <label for="auteur">Auteur <span class="required">*</span></label>
          <input type="text" name="auteur" id="auteur" required maxlength="100" value="Beheerder">
        </div>
      </div>

      <label for="inhoud">Artikel Inhoud <span class="required">*</span></label>
      <textarea name="inhoud" id="inhoud" required placeholder="Schrijf hier de volledige inhoud van het artikel..."></textarea>
      <div class="form-help">Schrijf de volledige tekst van het artikel. Gebruik Enter voor nieuwe alinea's.</div>

      <button type="submit">📝 Artikel Publiceren</button>
    </form>

    <div class="navigatie">
      <a href="beheer.php">← Terug naar beheer</a>
      <a href="index.php">Naar homepage</a>
    </div>
  </div>
</body>
</html>