<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM fietsen WHERE id = $id");
    $fiets = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $merk = $_POST['merk'];
    $type = $_POST['type'];
    $prijs = $_POST['prijs'];

    $stmt = $conn->prepare("UPDATE fietsen SET merk=?, type=?, prijs=? WHERE id=?");
    $stmt->bind_param("ssdi", $merk, $type, $prijs, $id);
    
    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Fout bij bijwerken.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Fiets wijzigen</title>
</head>
<body>
    <h2>Fiets wijzigen</h2>
    <form method="POST">
        <label>Merk: <input type="text" name="merk" value="<?= htmlspecialchars($fiets['merk']) ?>" required></label><br>
        <label>Type: <input type="text" name="type" value="<?= htmlspecialchars($fiets['type']) ?>" required></label><br>
        <label>Prijs: <input type="number" name="prijs" step="0.01" value="<?= htmlspecialchars($fiets['prijs']) ?>" required></label><br>
        <button type="submit">Bijwerken</button>
    </form>
</body>
</html>
