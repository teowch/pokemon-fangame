<?php

if (!isset($_GET['user']) || !isset($_GET['leader']) || !isset($_GET['team'])) {
    exit;
}

include_once '../classes/Database/Connection.php';
include_once '../classes/Database/Query.php';

$conn = new Connection('root', '');
$conn -> start();

$query = new Query($conn -> connectionString);

$order = $_GET['team'] - 1;

$teamID = $query -> raw ("SELECT team_id FROM user_team WHERE user_id = '".$_GET['user']."' LIMIT ".$order.", 1;");

$teamPokemons = $query -> raw("SELECT pokemon_id FROM team_pokemon WHERE team_id = '".($teamID -> fetch_assoc())['team_id']."';");

$username = (mysqli_fetch_assoc($query -> raw("SELECT username FROM user WHERE user_id = '".$_GET['user']."';")))['username'];

$userTeam = array(
  'trainer' => $username,
  'pokemon' => array()
);

while ($pokemon = $teamPokemons -> fetch_assoc()) {
  $storedPokemon = mysqli_fetch_assoc($query -> raw("SELECT * FROM pokemon WHERE pokemon_id = '".$pokemon['pokemon_id']."';"));
  $api = json_decode(file_get_contents('https://pokeapi.co/api/v2/pokemon/'.$storedPokemon['species_id']));

  $ivs = explode(',', $storedPokemon['ivs']);
  $evs = explode(',', $storedPokemon['evs']);

  $moves = $query->raw("select move_id from pokemon_move where pokemon_id = '".$pokemon['pokemon_id']."';");
  $movesIds = array();
  if ($moves) {
    if ($moves -> num_rows == 4) {
      while ($move = mysqli_fetch_array($moves)) {
        array_push($movesIds, $move['move_id']);
      }
    }
    else unset($movesIds);
  }
  else unset($movesIds);

  $userTeam['pokemon'][] = array(
    'id' => $storedPokemon['species_id'],
    'stats' => array(
      'base' => array(
        'hp' => floor((2 * $api -> stats[0] -> base_stat + $ivs[0] + ($evs[0] / 4)) + 100 + 10),
        'attack' => floor((2 * $api -> stats[1] -> base_stat + $ivs[1] + ($evs[1] / 4)) + 5),
        'defense' => floor((2 * $api -> stats[2] -> base_stat + $ivs[2] + ($evs[2] / 4)) + 5),
        'spattack' => floor((2 * $api -> stats[3] -> base_stat + $ivs[3] + ($evs[3] / 4)) + 5),
        'spdefense' => floor((2 * $api -> stats[4] -> base_stat + $ivs[4] + ($evs[4] / 4)) + 5),
        'speed' => floor((2 * $api -> stats[5] -> base_stat + $ivs[5] + ($evs[5] / 4)) + 5)
      ),
      'stage' => array(
        'attack' => 0,
        'defense' => 0,
        'spattack' => 0,
        'spdefense' => 0,
        'speed' => 0
      )
    ),
    'hp' => floor((2 * $api -> stats[0] -> base_stat + $ivs[0] + ($evs[0] / 4)) + 100 + 10)
  );

  if (isset($movesIds)) {
    $userTeam['pokemon'][sizeof($userTeam['pokemon']) - 1]['moves'] = $movesIds;
  }
}

unset($teamID, $teamPokemons, $storedPokemon);

$teamID = $query -> raw ("SELECT team_id FROM leader WHERE leader_id = '".$_GET['leader']."';");

$teamPokemons = $query -> raw("SELECT pokemon_id FROM team_pokemon WHERE team_id = '".($teamID -> fetch_assoc())['team_id']."';");

$leaderTeam = array(
  'trainer' => 'GYM_LEADER',
  'pokemon' => array()
);

while ($pokemon = $teamPokemons -> fetch_assoc()) {
  $storedPokemon = mysqli_fetch_assoc($query -> raw("SELECT * FROM pokemon WHERE pokemon_id = '".$pokemon['pokemon_id']."';"));
  $api = json_decode(file_get_contents('https://pokeapi.co/api/v2/pokemon/'.$storedPokemon['species_id']));

  $ivs = explode(',', $storedPokemon['ivs']);
  $evs = explode(',', $storedPokemon['evs']);

  $leaderTeam['pokemon'][] = array(
    'id' => $storedPokemon['species_id'],
    'stats' => array(
      'base' => array(
        'hp' => floor((2 * $api -> stats[0] -> base_stat + $ivs[0] + ($evs[0] / 4)) + 100 + 10),
        'attack' => floor((2 * $api -> stats[1] -> base_stat + $ivs[1] + ($evs[1] / 4)) + 5),
        'defense' => floor((2 * $api -> stats[2] -> base_stat + $ivs[2] + ($evs[2] / 4)) + 5),
        'spattack' => floor((2 * $api -> stats[3] -> base_stat + $ivs[3] + ($evs[3] / 4)) + 5),
        'spdefense' => floor((2 * $api -> stats[4] -> base_stat + $ivs[4] + ($evs[4] / 4)) + 5),
        'speed' => floor((2 * $api -> stats[5] -> base_stat + $ivs[5] + ($evs[5] / 4)) + 5)
      ),
      'stage' => array(
        'attack' => 0,
        'defense' => 0,
        'spattack' => 0,
        'spdefense' => 0,
        'speed' => 0
      )
    ),
    'hp' => floor((2 * $api -> stats[0] -> base_stat + $ivs[0] + ($evs[0] / 4)) + 100 + 10)
  );
}

$battleResponse = json_decode(file_get_contents('http://poke-battle-ifc.herokuapp.com/?user='.json_encode($userTeam).'&leader='.json_encode($leaderTeam)));

header('Content-Type: application/json');
echo json_encode($battleResponse);

$conn -> stop();
