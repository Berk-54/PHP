<?php
require 'verbinding.php';

$artikel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($artikel_id <= 0) {
    header('Location: index.php');
    exit;
}

// Tel weergave alleen als nog niet gelezen door deze gebruiker
if (!is_gelezen_door_ip($artikel_id)) {
    verhoog_gelezen($artikel_id);
}

// Haal artikel op
$query = $verbinding->prepare("
    SELECT n.*, c.naam as categorie_naam
    FROM nieuwsberichten n
    JOIN categorieen c ON n.categorie_id = c.id
    WHERE n.id = ? AND n.actief = 1
");
$query->bind_param("i", $artikel_id);
$query->execute();
$artikel = $query->get_result()->fetch_assoc();

if (!$artikel) {
    header('Location: index.php');
    exit;
}

// Check of artikel favoriet is
$gebruiker_ip = $_SERVER['REMOTE_ADDR'];
$favoriet_query = $verbinding->prepare("SELECT id FROM favorieten WHERE nieuwsbericht_id = ? AND gebruiker_ip = ?");
$favoriet_query->bind_param("is", $artikel_id, $gebruiker_ip);
$favoriet_query->execute();
$is_favoriet = $favoriet_query->get_result()->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($artikel['titel']) ?> - NieuwsPortaal</title>
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
      padding: 40px;
      border: 3px solid #2c5aa0;
      max-width: 900px;
      margin: 0 auto;
    }

    .artikel-header {
      border-bottom: 2px solid #2c5aa0;
      padding-bottom: 20px;
      margin-bottom: 30px;
    }

    .artikel-header h1 {
      color: #2c5aa0;
      font-size: 32px;
      margin: 0 0 15px 0;
      line-height: 1.3;
    }

    .artikel-meta {
      color: #666;
      font-size: 16px;
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .artikel-inhoud {
      font-size: 18px;
      line-height: 1.8;
      color: #333;
      margin-bottom: 40px;
    }

    .artikel-acties {
      background-color: #f9f9f9;
      padding: 20px;
      border: 1px solid #ddd;
      margin-bottom: 30px;
    }

    .artikel-acties h3 {
      color: #2c5aa0;
      margin: 0 0 15px 0;
    }

    .actie-knoppen {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }

    .btn {
      padding: 10px 20px;
      background-color: #2c5aa0;
      color: white;
      border: none;
      cursor: pointer;
      font-weight: bold;
      text-decoration: none;
      display: inline-block;
    }

    .btn:hover {
      background-color: #1e3f73;
    }

    .btn-favoriet {
      background-color: #e74c3c;
    }

    .btn-favoriet:hover {
      background-color: #c0392b;
    }

    .btn-favoriet.toegevoegd {
      background-color: #27ae60;
    }

    .btn-favoriet.toegevoegd:hover {
      background-color: #229954;
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
  </style>
</head>
<body>
  <div class="hoofdcontainer">
    <div class="artikel-header">
      <h1><?= htmlspecialchars($artikel['titel']) ?></h1>
      <div class="artikel-meta">
        <span><strong>Categorie:</strong> <?= htmlspecialchars($artikel['categorie_naam']) ?></span>
        <span><strong>Auteur:</strong> <?= htmlspecialchars($artikel['auteur']) ?></span>
        <span><strong>Gepubliceerd:</strong> <?= date('d-m-Y H:i', strtotime($artikel['aangemaakt'])) ?></span>
        <span><strong>Gelezen:</strong> <?= $artikel['gelezen_aantal'] ?> keer</span>
      </div>
    </div>

    <div class="artikel-inhoud">
      <?= nl2br(htmlspecialchars($artikel['inhoud'])) ?>
    </div>

    <div class="artikel-acties">
      <h3>Artikel Acties</h3>
      <div class="actie-knoppen">
        <a href="tip_vriend.php?id=<?= $artikel['id'] ?>" class="btn">📧 Tip een vriend</a>
        <a href="<?= $is_favoriet ? 'verwijder_favoriet.php' : 'toevoegen_favoriet.php' ?>?id=<?= $artikel['id'] ?>" 
           class="btn btn-favoriet <?= $is_favoriet ? 'toegevoegd' : '' ?>">
          <?= $is_favoriet ? '⭐ Verwijder uit favorieten' : '⭐ Toevoegen aan favorieten' ?>
        </a>
      </div>
    </div>

    <div class="navigatie">
      <a href="index.php">← Terug naar overzicht</a>
      <a href="beheer.php">Beheer panel</a>
    </div>
  </div>
</body>
</html>