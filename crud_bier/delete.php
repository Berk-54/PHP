<?php
include 'functions.php';

if(isset($_GET['biercode'])) {  
    if(deleteRecord($_GET['biercode'])) {
        header("Location: index.php");
        exit;
    } else {
        echo '<script>alert("Bier is NIET verwijderd")</script>';
    }
}
?>