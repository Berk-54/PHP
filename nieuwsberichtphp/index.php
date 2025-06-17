<?php
require 'verbinding.php';

// Haal categorieën op
$categorieen = $verbinding->query("SELECT * FROM categorieen ORDER BY naam");

// Haal laatste nieuwsberichten op
$categorie_filter = isset($_GET['categorie']) ? $_GET['categorie'] : '';
$zoeken = isset($_GET['zoeken']) ? $_GET['zoeken'] : '';

$query_sql = "
    SELECT n.id, n.titel, n.inhoud, n.auteur, n.aangemaakt, n.gelezen_aantal, c.naam as categorie_naam
    FROM nieuwsberichten n
    JOIN categorieen c ON n.categorie_id = c.id
    WHERE n.actief = 1
";

$params = [];
$types = "";

if ($categorie_filter) {
    $query_sql .= " AND n.categorie_id = ?";
    $params[] = $categorie_filter;
    $types .= "i";
}

if ($zoeken) {
    $query_sql .= " AND (n.titel LIKE ? OR n.inhoud LIKE ?)";
    $zoek_term = "%$zoeken%";
    $params[] = $zoek_term;
    $params[] = $zoek_term;
    $types .= "ss";
}

$query_sql .= " ORDER BY n.aangemaakt DESC LIMIT 10";

$stmt = $verbinding->prepare($query_sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$nieuwsberichten = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>NieuwsPortaal</title>
  <style>
    body {
      margin: 0;
      font-family: 'Times New Roman', serif;
      background: #e8f4f8;
      min-height: 100vh;
      padding: 20px;
    }

    .hoofdcontainer {
      background-color: #ffffff;
      padding: 30px;
      border: 3px solid #2c5aa0;
      max-width: 1200px;
      margin: 0 auto;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
    }

    .header h1 {
      color: #2c5aa0;
      font-size: 36px;
      margin: 0;
    }

    .header p {
      color: #666;
      font-size: 18px;
      margin: 10px 0;
    }

    .navigatie {
      background-color: #2c5aa0;
      padding: 15px;
      margin-bottom: 30px;
      text-align: center;
    }

    .navigatie a {
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      margin: 0 10px;
      background-color: #5a7ba0;
      font-weight: bold;
    }

    .navigatie a:hover {
      background-color: #1e3f73;
    }

    .filters {
      background-color: #f9f9f9;
      padding: 20px;
      margin-bottom: 30px;
      border: 1px solid #ddd;
    }

    .filters form {
      display: flex;
      gap: 15px;
      align-items: center;
      flex-wrap: wrap;
    }

    .filters select,
    .filters input[type="text"] {
      padding: 8px;
      border: 2px solid #ddd;
    }

    .filters button {
      padding: 8px 20px;
      background-color: #2c5aa0;
      color: white;
      border: none;
      cursor: pointer;
      font-weight: bold;
    }

    .nieuwsitem {
      background-color: #fff;
      border: 1px solid #ddd;
      padding: 20px;
      margin-bottom: 20px;
      border-left: 4px solid #2c5aa0;
    }

    .nieuwsitem h3 {
      color: #2c5aa0;
      margin: 0 0 10px 0;
      font-size: 22px;
    }

    .nieuwsitem h3 a {
      color: #2c5aa0;
      text-decoration: none;
    }

    .nieuwsitem h3 a:hover {
      text-decoration: underline;
    }

    .meta {
      color: #666;
      font-size: 14px;
      margin-bottom: 15px;
    }

    .meta span {
      margin-right: 15px;
    }

    .voorproefje {
      color: #333;
      line-height: 1.6;
    }

    .geen-berichten {
      text-align: center;
      color: #666;
      font-style: italic;
      padding: 40px;
    }
  </style>
</head>
<body>
  <div class="hoofdcontainer">
    <div class="header">
      <h1>NieuwsPortaal</h1>
      <p>Uw dagelijkse bron voor het laatste nieuws</p>
    </div>

    <div class="navigatie">
      <a href="index.php">Home</a>
      <a href="beheer.php">Beheer</a>
      <a href="mijn_favorieten.php">Mijn Favorieten</a>
    </div>

    <div class="filters">
      <form method="get" action="">
        <label for="categorie">Categorie:</label>
        <select name="categorie" id="categorie">
          <option value="">Alle categorieën</option>
          <?php while ($cat = $categorieen->fetch_assoc()): ?>
            <option value="<?= $cat['id'] ?>" <?= $categorie_filter == $cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['naam']) ?>
            </option>
          <?php endwhile; ?>
        </select>

        <label for="zoeken">Zoeken:</label>
        <input type="text" name="zoeken" id="zoeken" placeholder="Zoek in titel of inhoud..." value="<?= htmlspecialchars($zoeken) ?>">

        <button type="submit">Filter</button>
        <a href="index.php" style="color: #2c5aa0; text-decoration: none; margin-left: 10px;">Reset</a>
      </form>
    </div>

    <div class="nieuws-lijst">
      <?php if ($nieuwsberichten->num_rows > 0): ?>
        <?php while ($bericht = $nieuwsberichten->fetch_assoc()): ?>
          <div class="nieuwsitem">
            <h3><a href="artikel.php?id=<?= $bericht['id'] ?>"><?= htmlspecialchars($bericht['titel']) ?></a></h3>
            <div class="meta">
              <span><strong>Categorie:</strong> <?= htmlspecialchars($bericht['categorie_naam']) ?></span>
              <span><strong>Auteur:</strong> <?= htmlspecialchars($bericht['auteur']) ?></span>
              <span><strong>Datum:</strong> <?= date('d-m-Y H:i', strtotime($bericht['aangemaakt'])) ?></span>
              <span><strong>Gelezen:</strong> <?= $bericht['gelezen_aantal'] ?> keer</span>
            </div>
            <div class="voorproefje">
              <?= nl2br(htmlspecialchars(substr($bericht['inhoud'], 0, 200))) ?>
              <?= strlen($bericht['inhoud']) > 200 ? '...' : '' ?>
              <?php if (strlen($bericht['inhoud']) > 200): ?>
                <br><a href="artikel.php?id=<?= $bericht['id'] ?>" style="color: #2c5aa0;">Lees meer →</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="geen-berichten">
          <p>Geen nieuwsberichten gevonden die voldoen aan uw zoekcriteria.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>