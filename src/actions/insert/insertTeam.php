<?php

include_once '../../classes/Database/Connection.php';
include_once '../../classes/Database/Query.php';

$conn = new Connection('root', '');
$conn->start();

$query = new Query($conn->connectionString);

$team = json_decode($_REQUEST['team']);

$userId = $team -> user_id;

$teamCount = intval(mysqli_fetch_assoc($query->raw("select COUNT(team_id) as teams from user_team where user_id = '".$userId."'"))['teams']);

var_dump($teamCount);

if ($teamCount >= 3) {
  header("Location: ../../../teams.php");
  exit;
}

$query->raw("insert into team(name) values" .
  "('" . $team -> name . "')" .
";");

$teamId = (mysqli_fetch_assoc($query->raw("select LAST_INSERT_ID();")))['LAST_INSERT_ID()'];

$pokemons = $team -> pokemons;

foreach ($pokemons as $pokemon) {

  $species = (mysqli_fetch_assoc($query->raw("select species_id from species where name='" . $pokemon -> species . "';")))['species_id'];

  $evs = array();
  $ivs = array();
  foreach ($pokemon -> evs as $ev) {
    array_push($evs, $ev);
  }
  foreach ($pokemon -> ivs as $iv) {
    array_push($ivs, $iv);
  }
  $evsStr = join(',', $evs);
  $ivsStr = join(',', $ivs);

  $query->raw("insert into pokemon(species_id, ivs, evs) values" .
    "('" . $species . "', '" . $ivsStr . "', '" . $evsStr . "')" .
    ";");

  $pokeId = (mysqli_fetch_assoc($query->raw("select LAST_INSERT_ID();")))['LAST_INSERT_ID()'];

  $pokeMoves = $pokemon -> moves;

  foreach ($pokeMoves as $move) {

    $moveId = (mysqli_fetch_assoc($query->raw("select move_id from move where name='" . $move . "';")))['move_id'];

    $query->raw("insert into pokemon_move(pokemon_id, move_id) values" .
      "('" . $pokeId . "', '" . $moveId . "')" .
    ";");
  }

  $query->raw("insert into team_pokemon(team_id, pokemon_id) values" .
    "('" . $teamId . "', '" . $pokeId . "')" .
  ";");
}

$query->raw("insert into user_team(user_id, team_id) values" .
  "('" . $userId . "', '" . $teamId . "')" .
";");

