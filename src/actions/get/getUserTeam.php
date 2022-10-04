<?php

if (!isset($_GET['user']) || !isset($_GET['team'])) {
    exit;
}

include_once '../../classes/Response.php';

header('Content-Type: application/json');
$res = new Response();

include_once '../../classes/Database/Connection.php';
include_once '../../classes/Database/Query.php';

$conn = new Connection('root', '');
$conn -> start();

$query = new Query($conn -> connectionString);

$order = $_GET['team'] - 1;

$team_id = (mysqli_fetch_assoc($query -> raw("SELECT team_id FROM user_team WHERE user_id = '".$_GET['user']."' LIMIT ".$order.", 1;")))['team_id'];

if (!$team_id) {
    $res -> setStatus(400);
    $res -> setError('Team not found');

    exit($res -> json());
}

$team_pokemon = $query -> raw("SELECT pokemon_id FROM team_pokemon WHERE team_id = '".$team_id."';");

$team = array('pokemons' => array());
while ($pokemon_id = $team_pokemon -> fetch_assoc()) {
    $pokemon_id = $pokemon_id['pokemon_id'];
    $pokemon = mysqli_fetch_assoc($query -> raw("SELECT species_id, ivs, evs FROM pokemon WHERE pokemon_id = '".$pokemon_id."';"));
    // $species = (mysqli_fetch_assoc($query -> raw("SELECT name FROM species WHERE species_id = '".$pokemon['species_id']."';"))['name'];
    
    $ivs = explode(',', $pokemon['ivs']);
    $evs = explode(',', $pokemon['evs']);
    
    $team['pokemons'][] = array(
        'id' => $pokemon['species_id'],
        'health_iv' => $ivs[0],
        'health_ev' => $evs[0]
    );
}

$res -> setData($team);

$conn -> stop();
exit($res -> json());
