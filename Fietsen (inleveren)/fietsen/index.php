<?php
include 'db.php';

$result = $conn->query("SELECT * FROM fietsen");
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Fietsen CRUD</title>
</head>
<body>
    <h2>Fietsenoverzicht</h2>
    <a href="add.php">Fiets toevoegen</a>
    <table border="1">
        <tr>
            <th>Merk</th>
            <th>Type</th>
            <th>Prijs (€)</th>
            <th>Acties</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['merk']) ?></td>
            <td><?= htmlspecialchars($row['type']) ?></td>
            <td><?= htmlspecialchars($row['prijs']) ?></td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>">Wijzig</a> | 
                <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Weet je zeker dat je deze fiets wilt verwijderen?')">Verwijder</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
