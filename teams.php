<?php

session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: ./login.php');
  exit;
}

?>

<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Teams</title>

  <link href="./assets/images/favicon.png" rel="shortcut icon">

  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <link href="./assets/styles/global.css" rel="stylesheet">

  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
</head>

<body>
  <?php include './components/navbar.php'; ?>

  <div class="container teams">
    <h1 class="section-title">Your Teams</h1>
    <div class="team-wrapper">
      <?php

      include_once './src/classes/Database/Connection.php';
      include_once './src/classes/Database/Query.php';

      $conn = new Connection('root', '');
      $conn -> start();

      $query = new Query($conn -> connectionString);

      $userTeams = $query -> raw("select team_id from user_team where user_id = '".$_SESSION['user_id']."';");

      if ($userTeams) {
        while ($teams = $userTeams -> fetch_assoc()) {
          foreach ($teams as $teamID) {
            $teamPokemons = $query -> raw("select pokemon_id from team_pokemon where team_id = '".$teamID."';");
            $team = mysqli_fetch_assoc($query -> raw("select name from team where team_id = '".$teamID."';"));

            echo '<div class="team">'.
              '<div class="header">'.
              '<div class="name">'.
              '<p>'.$team['name'].'</p>'.
              '</div>'.
              '<div class="options">'.
              '<a class="remove-button" href="./src/actions/delete/deleteTeam.php?id='.$teamID.'"><i class="material-icons">delete_forever</i></a>'.
              '</div>'.
              '</div>'.
              '<div class="pokemon-wrapper">';

            while ($pokemons = $teamPokemons -> fetch_assoc()) {
              foreach($pokemons as $pokemonID) {
                $pokemon = mysqli_fetch_assoc($query -> raw("select species_id from pokemon where pokemon_id = '".$pokemonID."';"));

                $api = json_decode(file_get_contents('https://pokeapi.co/api/v2/pokemon/'.$pokemon['species_id']));
                $types = $api -> types;
                unset($api);

                $api = json_decode(file_get_contents('https://pokeapi.co/api/v2/pokemon-species/'.$pokemon['species_id']));
                $name = $api -> names[7] -> name;
                unset($api);

                echo '<div class="pokemon">'.
                  '<div class="sprite">'.
                  '<img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/'.$pokemon['species_id'].'.png">'.
                  '</div>'.
                  '<div class="information">'.
                  '<div class="name">'.
                  '<p>'.$name.'</p>'.
                  '</div>'.
                  '<div class="types">';

                foreach($types as $type) {
                  echo '<object class="type pokemon-type '.$type -> type -> name.'" data="http://web.sbs.ifc.edu.br/~pablo.bayerl/unnamedproject/assets/types/icons/'.$type -> type -> name.'.svg" type="image/svg+xml" title="'.$type -> type -> name.'"></object>';
                }

                echo '</div>';
                echo '</div>';
                echo '</div>';
              }
            }

            echo '</div>';
            echo '</div>';
          }
        }
      }

      if (!$userTeams || $userTeams -> num_rows < 3) {
        echo '<div class="create-wrapper">';
        echo '<a href="./create_team"><i class="material-icons">add</i></a>';
        echo '</div>';
      }

      ?>
    </div>
  </div>
</body>

</html>
