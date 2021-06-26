<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: ./login');
  exit;
}

include_once './src/classes/Database/Connection.php';
include_once './src/classes/Database/Query.php';

$conn = new Connection('root', '');
$conn -> start();

$query = new Query($conn -> connectionString);

$storedLeaders = $query -> raw(
  "select leader.*
  from leader;"
);

$leaders = array();

while ($storedLeader = $storedLeaders -> fetch_assoc()) {
  $leaderPokemons = $query -> raw(
    "select team_pokemon.pokemon_id
    from team_pokemon
    where team_pokemon.team_id = '".$storedLeader['team_id']."';"
  );

  $pokemons = array();

  foreach ($leaderPokemons as $pokemonID) {
    $storedPokemon = mysqli_fetch_assoc(
      $query -> raw(
        "select pokemon.*
        from pokemon
        where pokemon.pokemon_id = '".$pokemonID['pokemon_id']."';"
        )
      );

      $pokemons[] = array(
        'pokemon_id' => $storedPokemon['pokemon_id'],
        'species_id' => $storedPokemon['species_id']
      );
    }

    $leaders[] = array(
      'leader_id' => $storedLeader['leader_id'],
      'leader_name' => $storedLeader['name'],
      'gym_type' => $storedLeader['type'],
      'team' => array(
        'team_id' => $storedLeader['team_id'],
        'pokemons' => $pokemons
      )
    );
  }

  $userStoredTeams = $query -> raw("SELECT team_id FROM user_team WHERE user_id = '".$_SESSION['user_id']."';");

  $userTeams = array();

  while ($userTeamID = $userStoredTeams -> fetch_assoc()) {
    $teamName = (mysqli_fetch_assoc($query -> raw("SELECT name FROM team WHERE team_id = '".$userTeamID['team_id']."';")))['name'];

    $userTeams[] = $teamName;
  }

  $conn -> stop();
  unset($conn);
  ?>

  <!DOCTYPE html>
  <html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Loading</title>

    <link href="./assets/images/favicon.png" rel="shortcut icon">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link href="./assets/styles/global.css" rel="stylesheet">
  </head>

  <body>
    <?php include './components/navbar.php'; ?>
    <?php echo '<div class="loading"></div>'; ?>

    <div class="container leaders">
      <h1 class="section-title">Leaders</h1>
      <div class="leaders-wrapper">
        <?php
        foreach ($leaders as $key => $leader) {
          $type = strtolower($leader['gym_type']);

          $order = $key + 1;

          if ($key == 0) {
            echo '<div class="leader selected" leader-order="'.$order.'">';
          } else {
            echo '<div class="leader" leader-order="'.$order.'">';
          }

          echo    '<div class="hover-effect"></div>';

          echo    '<div class="avatar">'.
          '<img src="http://web.sbs.ifc.edu.br/~pablo.bayerl/unnamedproject/assets/leaders/'.strtolower($leader['leader_name']).'.png">'.
          '<object class="type leader-type '.$type.'" data="http://web.sbs.ifc.edu.br/~pablo.bayerl/unnamedproject/assets/types/icons/'.$type.'.svg" type="image/svg+xml" title="'.$type.'"></object>'.
          '</div>'.
          '<div class="name">'.
          '<p>'.$leader['leader_name'].'</p>'.
          '</div>'.
          '</div>';
        }
        ?>
      </div>
      <h1 class="section-title">Teams</h1>
      <div class="teams-wrapper">
        <?php
        foreach ($leaders as $key => $leader) {
          $order = $key + 1;

          if ($key == 0) {
            echo '<div class="team selected" leader-order="'.$order.'">';
          } else {
            echo '<div class="team" leader-order="'.$order.'">';
          }

          foreach ($leader['team']['pokemons'] as $pokemon) {
            $species = json_decode(file_get_contents('https://pokeapi.co/api/v2/pokemon-species/'.$pokemon['species_id']), true);
            $name = $species['names'][7]['name'];

            $api = json_decode(file_get_contents('https://pokeapi.co/api/v2/pokemon/'.$pokemon['species_id']), true);
            $types = $api['types'];

            echo '<div class="pokemon">'.
            '<div class="sprite">'.
            '<img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/'.$pokemon['species_id'].'.png">'.
            '</div>'.
            '<div class="info">'.
            '<div class="name">'.
            '<p>'.$name.'</p>'.
            '</div>'.
            '<div class="types">';

            foreach ($types as $type) {
              echo '<object class="type pokemon-type '.$type['type']['name'].'" data="http://web.sbs.ifc.edu.br/~pablo.bayerl/unnamedproject/assets/types/icons/'.$type['type']['name'].'.svg" type="image/svg+xml" title="'.$type['type']['name'].'"></object>';
            }

            echo        '</div>'.
            '</div>'.
            '</div>';
          }

          echo '</div>';
        }
        ?>
      </div>
      <div class="select-team">
        <div class="team-list-wrapper">
          <div class="header">
            <h1>Select your team</h1>
            <span class="close-button"><i class="material-icons">close</i></span>
          </div>
          <div class="buttons">
            <?php
              foreach ($userTeams as $key => $team) {
                echo '<button type="button" team-order="'.$key.'">'.$team.'</button>';
              }
            ?>
          </div>
        </div>
      </div>
    </div>

    <script>
    $(document).ready(() => {
      document.title = 'Leaders';

      $('.loading').remove();

      $('.select-team .close-button').on('click', () => {
        if ($('.select-team').css('display') != 'none') {
          $('.select-team').css('display', 'none');
        }
      });

      $('.select-team .buttons button').each(index => {
        $(`.select-team .buttons button:eq(${index})`).on('click', () => {
          let selectedTeam = ++index;
          let selectedLeader = $('.leaders-wrapper .leader.selected').attr('leader-order');

          $('body').append(`<a href="<?= basename(__DIR__) ?>/../battle?leader=${selectedLeader}&team=${selectedTeam}" style="display: none" id="battleAnchor"></a>`);

          $('#battleAnchor')[0].click();
        });
      });

      $('.leaders-wrapper .leader').on('click', e => {
        if ($(e.currentTarget).hasClass('selected')) {
          $('.select-team').css('display', 'flex');
        }

        $('.leaders-wrapper .leader.selected')
        .removeClass('selected');

        $(e.currentTarget)
        .addClass('selected');

        let leaderOrder = $(e.currentTarget).attr('leader-order');

        $(`.teams-wrapper .team.selected`)
        .removeClass('selected');

        $(`.teams-wrapper .team[leader-order=${leaderOrder}]`)
        .addClass('selected');
      });
    });
    </script>
  </body>

  </html>
