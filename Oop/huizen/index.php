<?php

require_once 'Huis.php';

// Maak 3 huis-objecten aan
$eersteHuis = new Huis(2, 5, 8.5, 6.2, 10.0);
$tweedeHuis = new Huis(3, 7, 10.0, 7.5, 12.0);
$derdeHuis = new Huis(1, 3, 6.0, 3.5, 8.0);

// Toon details van elk huis
$eersteHuis->toonDetails();
$tweedeHuis->toonDetails();
$derdeHuis->toonDetails();
