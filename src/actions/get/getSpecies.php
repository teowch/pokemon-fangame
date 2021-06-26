<?php

include_once '../../classes/Response.php';

header('Content-Type: application/json');
$res = new Response();

include_once '../../classes/Database/Connection.php';
include_once '../../classes/Database/Query.php';

$conn = new Connection('root', '');
$conn -> start();

$query = new Query($conn -> connectionString);

$storedSpecies = $query -> raw('select name from species');
$responseSpecies = array();

while ($species = $storedSpecies -> fetch_assoc()) {
  $responseSpecies[] = $species['name'];
}

$res -> setData(array('species' => $responseSpecies));

$conn -> stop();
exit($res -> json());
