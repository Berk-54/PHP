<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gastenboek";
    //https://www.ictacademie.info/connectpdovoorbeeld.php

   // connectiem maken met de PDO.
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>