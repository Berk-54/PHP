<?php
    echo "<h1>Insert Bier</h1>";
    require_once('functions.php');
    
    $brouwers = getBrouwers();
     
    if(isset($_POST['btn_ins'])){
        if(insertRecord($_POST)) {
            echo "<script>alert('Bier is toegevoegd')</script>";
            echo "<script>location.replace('index.php');</script>";
        } else {
            echo '<script>alert("Bier is NIET toegevoegd")</script>';
        }
    }
?>
<html>
    <body>
    <form method="post">
    <label for="naam">Naam:</label>
    <input type="text" id="naam" name="naam" required><br>

    <label for="soort">Soort:</label>
    <input type="text" id="soort" name="soort" required><br>

    <label for="stijl">Stijl:</label>
    <input type="text" id="stijl" name="stijl" required><br> 

    <label for="alcohol">Alcohol %:</label>
    <input type="number" id="alcohol" name="alcohol" step="0.1" required><br>

    <label for="brouwcode">Brouwer:</label>
    <select id="brouwcode" name="brouwcode" required>
        <?php foreach($brouwers as $brouwer): ?>
            <option value="<?= $brouwer['brouwcode'] ?>">
                <?= htmlspecialchars($brouwer['naam']) ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <input type="submit" name="btn_ins" value="Toevoegen">
</form>
        
        <br><br>
        <a href='index.php'>Home</a>
    </body>
</html>