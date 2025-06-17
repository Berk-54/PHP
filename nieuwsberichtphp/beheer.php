<?php
require 'verbinding.php';

// Haal alle nieuwsberichten op voor beheer
$nieuwsberichten = $verbinding->query("
    SELECT n.id, n.titel, n.auteur, n.aangemaakt, n.gelezen_aantal, n.actief, c.naam as categorie_naam
    FROM nieuwsberichten n
    JOIN categorieen c ON n.categorie_id = c.id
    ORDER BY n.aangemaakt DESC
");

// Haal categorieën op
$categorieen = $verbinding->query("SELECT * FROM categorieen ORDER BY naam");
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Beheer Panel - NieuwsPortaal</title>
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

    h1 {
      text-align: center;
      color: #2c5aa0;
      margin-bottom: 30px;
      font-size: 28px;
    }

    .beheer-acties {
      background-color: #f9f9f9;
      padding: 20px;
      margin-bottom: 30px;
      border: 1px solid #ddd;
      text-align: center;
    }

    .beheer-acties h3 {
      color: #2c5aa0;
      margin: 0 0 15px 0;
    }

    .btn {
      padding: 12px 25px;
      background-color: #2c5aa0;
      color: white;
      border: none;
      cursor: pointer;
      font-weight: bold;
      text-decoration: none;
      display: inline-block;
      margin: 5px;
    }

    .btn:hover {
      background-color: #1e3f73;
    }

    .btn-success {
      background-color: #27ae60;
    }

    .btn-success:hover {
      background-color: #229954;
    }

    .btn-warning {
      background-color: #f39c12;
    }

    .btn-warning:hover {
      background-color: #e67e22;
    }

    .btn-danger {
      background-color: #e74c3c;
    }

    .btn-danger:hover {
      background-color: #c0392b;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #2c5aa0;
      color: white;
      font-weight: bold;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tr:hover {
      background-color: #e6f3ff;
    }

    .status-actief {
      color: #27ae60;
      font-weight: bold;
    }

    .status-inactief {
      color: #e74c3c;
      font-weight: bold;
    }

    .acties-kolom {
      white-space: nowrap;
    }

    .acties-kolom a {
      margin-right: 5px;
      font-size: 12px;
      padding: 5px 10px;
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
    <h1>Beheer Panel</h1>

    <div class="beheer-acties">
      <h3>Beheer Opties</h3>
      <a href="nieuw_artikel.php" class="btn btn-success">➕ Nieuw Artikel</a>
      <a href="categorie_beheer.php" class="btn">📁 Categorieën Beheren</a>
      <a href="statistieken.php" class="btn">📊 Statistieken</a>
    </div>

    <h2 style="color: #2c5aa0; margin-top: 40px;">Alle Nieuwsberichten</h2>
    
    <table>
      <thead>
        <tr>
          <th>Titel</th>
          <th>Categorie</th>
          <th>Auteur</th>
          <th>Datum</th>
          <th>Gelezen</th>
          <th>Status</th>
          <th>Acties</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($bericht = $nieuwsberichten->fetch_assoc()): ?>
        <tr>
          <td>
            <a href="artikel.php?id=<?= $bericht['id'] ?>" style="color: #2c5aa0; text-decoration: none;">
              <?= htmlspecialchars($bericht['titel']) ?>
            </a>
          </td>
          <td><?= htmlspecialchars($bericht['categorie_naam']) ?></td>
          <td><?= htmlspecialchars($bericht['auteur']) ?></td>
          <td><?= date('d-m-Y H:i', strtotime($bericht['aangemaakt'])) ?></td>
          <td><?= $bericht['gelezen_aantal'] ?></td>
          <td>
            <span class="<?= $bericht['actief'] ? 'status-actief' : 'status-inactief' ?>">
              <?= $bericht['actief'] ? 'Actief' : 'Inactief' ?>
            </span>
          </td>
          <td class="acties-kolom">
            <a href="bewerk_artikel.php?id=<?= $bericht['id'] ?>" class="btn btn-warning">✏️ Bewerken</a>
            <?php if ($bericht['actief']): ?>
              <a href="deactiveer_artikel.php?id=<?= $bericht['id'] ?>" class="btn btn-danger" 
                 onclick="return confirm('Weet je zeker dat je dit artikel wilt deactiveren?')">🚫 Deactiveren</a>
            <?php else: ?>
              <a href="activeer_artikel.php?id=<?= $bericht['id'] ?>" class="btn btn-success">✅ Activeren</a>
            <?php endif; ?>
            <a href="verwijder_artikel.php?id=<?= $bericht['id'] ?>" class="btn btn-danger" 
               onclick="return confirm('Weet je zeker dat je dit artikel permanent wilt verwijderen?')">🗑️ Verwijderen</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody