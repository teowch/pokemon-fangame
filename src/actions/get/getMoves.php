<?php

include_once '../../classes/Response.php';

header('Content-Type: application/json');
$res = new Response();

include_once '../../classes/Database/Connection.php';
include_once '../../classes/Database/Query.php';

$conn = new Connection('root', '');
$conn -> start();

$query = new Query($conn -> connectionString);

$storedMoves = $query -> raw('select name from move');
$responseMoves = array();

while ($moves = $storedMoves -> fetch_assoc()) {
  $responseMoves[] = $moves['name'];
}

$res -> setData(array('moves' => $responseMoves));

$conn -> stop();
exit($res -> json());
