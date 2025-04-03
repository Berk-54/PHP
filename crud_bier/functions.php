<?php
include_once "config.php";

function connectDb() {
    try {
        $conn = new PDO("mysql:host=".SERVERNAME.";dbname=".DATABASE, USERNAME, PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

function crudMain() {
    echo "<h1>Crud Bieren</h1>
          <nav>
              <a href='insert.php'>Toevoegen nieuw bier</a>
          </nav><br>";

    $result = getData(CRUD_TABLE);
    printCrudTabel($result);
}

function getData($table) {
    $conn = connectDb();
    
    $sql = "SELECT b.biercode, b.naam, b.soort, b.stijl, b.alcohol, 
                   b.brouwcode, br.naam as brouwernaam 
            FROM bier b 
            LEFT JOIN brouwer br ON b.brouwcode = br.brouwcode";

    try {
        $query = $conn->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    } catch(PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}

function getBrouwers() {
    $conn = connectDb();
    $sql = "SELECT brouwcode, naam FROM brouwer";
    $query = $conn->prepare($sql);
    $query->execute();
    return $query->fetchAll();
}

function getRecord($biercode) {
    $conn = connectDb();
    $sql = "SELECT * FROM bier WHERE biercode = :biercode";
    $query = $conn->prepare($sql);
    $query->execute([':biercode' => $biercode]);
    return $query->fetch();
}

function printCrudTabel($result) {
    if(empty($result)) {
        echo "Geen bieren gevonden";
        return;
    }

    $table = "<table>";
    $headers = array_keys($result[0]);
    
    $table .= "<tr>";
    foreach($headers as $header) {
        if(!in_array($header, ['brouwcode', PRIMARY_KEY])) {
            $table .= "<th>" . ucfirst($header) . "</th>";
        }
    }
    $table .= "<th colspan='2'>Actie</th></tr>";

    foreach ($result as $row) {
        $table .= "<tr>";
        foreach ($row as $key => $cell) {
            if(!in_array($key, ['brouwcode', PRIMARY_KEY])) {
                $table .= "<td>" . htmlspecialchars($cell) . "</td>";
            }
        }
        
        $table .= "<td>
            <form method='get' action='update.php'>  <!-- Changed to method='get' -->
                <input type='hidden' name='biercode' value='" . $row['biercode'] . "'>
                <button type='submit'>Wijzig</button>
            </form>
        </td>";

        $table .= "<td>
            <form method='get' action='delete.php'>  <!-- Changed to method='get' -->
                <input type='hidden' name='biercode' value='" . $row['biercode'] . "'>
                <button type='submit'>Verwijder</button>
            </form>
        </td>";

        $table .= "</tr>";
    }
    $table .= "</table>";
    echo $table;
}

function updateRecord($row) {
    $conn = connectDb();
    $sql = "UPDATE bier SET 
                naam = :naam, 
                soort = :soort, 
                stijl = :stijl,
                alcohol = :alcohol,
                brouwcode = :brouwcode
            WHERE biercode = :biercode";

    try {
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            ':naam' => $row['naam'],
            ':soort' => $row['soort'],
            ':stijl' => $row['stijl'],
            ':alcohol' => $row['alcohol'],
            ':brouwcode' => $row['brouwcode'],
            ':biercode' => $row['biercode']
        ]);
    } catch(PDOException $e) {
        die("Update failed: " . $e->getMessage());
    }
}

function insertRecord($post) {
    $conn = connectDb();
    
  
    $required_fields = ['naam', 'soort', 'stijl', 'alcohol', 'brouwcode'];
    foreach ($required_fields as $field) {
        if (!isset($post[$field]) || empty(trim($post[$field]))) {
            die("$field is vereist en mag niet leeg zijn");
        }
    }

    $sql = "INSERT INTO bier (naam, soort, stijl, alcohol, brouwcode)
            VALUES (:naam, :soort, :stijl, :alcohol, :brouwcode)";

    try {
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([
            ':naam' => trim($post['naam']),
            ':soort' => trim($post['soort']),
            ':stijl' => trim($post['stijl']),
            ':alcohol' => (float)$post['alcohol'],
            ':brouwcode' => (int)$post['brouwcode']
        ]);
        
        if ($result) {
            header("Location: index.php");
            exit;
        } else {
            die("Toevoegen van bier is mislukt");
        }
    } catch(PDOException $e) {
        die("Database fout: " . $e->getMessage());
    }
}

function deleteRecord($biercode) {
    $conn = connectDb();
    $sql = "DELETE FROM bier WHERE biercode = :biercode";
    
    try {
        $stmt = $conn->prepare($sql);
        return $stmt->execute([':biercode' => $biercode]);
    } catch(PDOException $e) {
        die("Delete failed: " . $e->getMessage());
    }
}
?>