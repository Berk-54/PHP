<?php
include "connectpdo.php";

if(isset($_GET['id']))
{
$id = $_GET['id'];
echo "$id";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname",$username,$password);

    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $sql = "DELETE FROM berichten WHERE id='$id'";

    $conn->exec($sql);
    echo "Record deleted succesfully";

    header('Location: index.php');
}
catch(PDOException $e)
{
    
}

}
$conn = null;


