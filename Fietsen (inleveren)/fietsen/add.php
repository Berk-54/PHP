<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $merk = $_POST['merk'];
    $type = $_POST['type'];
    $prijs = $_POST['prijs'];

    $stmt = $conn->prepare("INSERT INTO fietsen (merk, type, prijs) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $merk, $type, $prijs);
    
    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Fout bij toevoegen.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Fiets toevoegen</title>
</head>
<body>
    <h2>Fiets toevoegen</h2>
    <form method="POST">
        <label>Merk: <input type="text" name="merk" required></label><br>
        <label>Type: <input type="text" name="type" required></label><br>
        <label>Prijs: <input type="number" name="prijs" step="0.01" required></label><br>
        <button type="submit">Toevoegen</button>
    </form>
</body>
</html>
