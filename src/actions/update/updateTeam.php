<?php

session_start();

if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
  header('Location: ../../../teams');
  exit;
}

$teamId = $_GET['id'];
$userId = $_SESSION['user_id'];

include_once '../../classes/Database/Connection.php';
include_once '../../classes/Database/Query.php';

$conn = new Connection('root', '');
$conn -> start();

$query = new Query($conn -> connectionString);

$userTeam = $query -> raw("select * from user_team where team_id = '".$teamId."' and user_id = '".$userId."';");

// Verificar se o usuário possui o time
if (!$userTeam) {
  header('Location: ../../../teams');
  exit;
}

$team = $userTeam -> fetch_assoc();

$teamPokemons = $query -> raw("select * from team_pokemon where team_id = '".$teamId."';");

$query -> raw("delete from user_team where team_id = '".$teamId."';");

while ($pokemons = $teamPokemons -> fetch_assoc()) {
  $query -> raw("set foreign_key_checks = 0;");
  $query -> raw("delete from team_pokemon where pokemon_id = ".$pokemons['pokemon_id'].";");
  $query -> raw("delete from pokemon where pokemon_id = ".$pokemons['pokemon_id'].";");
}

$query -> raw("delete from team where team_id = '".$teamId."';");

$conn -> stop();

header('Location: ../../../create_team');
exit;
