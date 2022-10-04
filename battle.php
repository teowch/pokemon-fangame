<?php

session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['leader']) || !isset($_GET['team'])) {
  header('Location: login');
  exit;
}

$conn = mysqli_connect('localhost:3306', 'root', '', 'dev_stadium');

$leaderName = (mysqli_fetch_assoc($conn -> query("SELECT name FROM leader WHERE leader_id = '".$_GET['leader']."'")))['name'];
$userName = (mysqli_fetch_assoc($conn -> query("SELECT username FROM user WHERE user_id = '".$_SESSION['user_id']."'")))['username'];

$conn -> close();

?>

<!DOCTYPE html>
<html>
  
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Loading</title>
    
    <link href="./assets/images/favicon.png" rel="shortcut icon">
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <link href="./assets/styles/global.css" rel="stylesheet">

    <script type="module">
      import pokeapi from './assets/scripts/team/services/pokeapi.js';
      $(document).ready(async () => {
        let user = await $.getJSON({
          url: '/pokemon-fangame/src/actions/get/getUserTeam.php',
          data: {
            user: <?= $_SESSION['user_id'] ?>,
            team: <?= $_GET['team'] ?>
          }
        });
        
        user.data.pokemons.forEach(async (pokemon, index) => {
          let { types, stats } = await pokeapi('pokemon', pokemon.id);
          let species = await pokeapi('pokemon-species', pokemon.id);
          
          let health = parseInt(Math.trunc((2 * parseInt(stats[0].base_stat) + parseInt(pokemon.health_iv) + (parseInt(pokemon.health_ev) / 4)) + 100 + 10));
          
          let typesElement = '';
          
          types.forEach(type => {
            typesElement += `
            <object
              class="type pokemon-type ${type.type.name}"
              data="http://web.sbs.ifc.edu.br/~pablo.bayerl/unnamedproject/assets/types/icons/${type.type.name}.svg"
              type="image/svg+xml"
              title="${type.type.name}"
              >
              </object>
          `;
        });
        
        let divClass;

        if (index === 0) {
          divClass = 'pokemon active';
        } else {
          divClass = 'pokemon';
        }
        
        $('.user-team .pokemons').append(`
        <div class="${divClass}" pokemon-id="${pokemon.id}">
        <div class="header">
        <div class="name">
        <p>${species.names[7].name}</p>
        </div>
        <div class="types">
        ${typesElement}
            </div>
            </div>
            <div class="sprite">
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemon.id}.png">
            </div>
            <div class="stats">
            <table>
            <tr class="health">
            <th class="label">Health</th>
            <td class="total">
            <span class="var">${health}</span>
            /
            <span class="max">${health}</span>
            </td>
            </tr>
            <tr class="health-bar">
            <td colspan="2">
            <div class="health-wrapper">
            <div class="bar" style="width: 100%"></div>
            </div>
            </td>
            </tr>
            </table>
            </div>
            </div>
            `);
            
        $('.user-team .party').append(`
        <div class="party-${divClass}" pokemon-id="${pokemon.id}">
        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemon.id}.png">
        </div>
        `);
      });
      
      let leader = await $.getJSON({
        url: '/pokemon-fangame/src/actions/get/getLeaderTeam.php',
        data: { leader: <?= $_GET['leader'] ?> }
      });

      leader.data.pokemons.forEach(async (pokemon, index) => {
        let { types, stats } = await pokeapi('pokemon', pokemon.id);
        let species = await pokeapi('pokemon-species', pokemon.id);
        
        let health = parseInt(Math.trunc((2 * parseInt(stats[0].base_stat) + parseInt(pokemon.health_iv) + (parseInt(pokemon.health_ev) / 4)) + 100 + 10));

        let typesElement = '';
        
        types.forEach(type => {
          typesElement += `
            <object
              class="type pokemon-type ${type.type.name}"
              data="http://web.sbs.ifc.edu.br/~pablo.bayerl/unnamedproject/assets/types/icons/${type.type.name}.svg"
              type="image/svg+xml"
              title="${type.type.name}"
            >
            </object>
          `;
        });

        let divClass;

        if (index === 0) {
          divClass = 'pokemon active';
        } else {
          divClass = 'pokemon';
        }

        $('.leader-team .pokemons').append(`
        <div class="${divClass}" pokemon-id="${pokemon.id}">
          <div class="header">
            <div class="name">
              <p>${species.names[7].name}</p>
            </div>
            <div class="types">
              ${typesElement}
            </div>
          </div>
          <div class="sprite">
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemon.id}.png">
          </div>
          <div class="stats">
            <table>
              <tr class="health">
                <th class="label">Health</th>
                <td class="total">
                  <span class="var">${health}</span>
                  /
                  <span class="max">${health}</span>
                </td>
              </tr>
              <tr class="health-bar">
                <td colspan="2">
                  <div class="health-wrapper">
                    <div class="bar" style="width: 100%"></div>
                  </div>
                </td>
              </tr>
            </table>
          </div>
        </div>
        `);

        $('.leader-team .party').append(`
          <div class="party-${divClass}" pokemon-id="${pokemon.id}">
            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemon.id}.png">
          </div>
        `);
      });


      $('#handler').on('click', async e => {
        $('.handler').empty().remove();

        await $.ajax({
          url: '/pokemon-fangame/src/actions/battle.php',
          method: 'get',
          data: {
            user: <?= $_SESSION['user_id']; ?>,
            leader: <?= $_GET['leader']; ?>,
            team: <?= $_GET['team']; ?>
          },
          success: response => {
            var turn = 1;
            response.log.forEach((rowLog, i) => {
              setTimeout(() => {
                if (i > 0) {
                  // Increment turn
                  ++turn;
                  $('.battle-log').scrollTop($('.battle-log .turn:last').position().top);
                }

                console.log(turn);

                if (turn === response.log.length) {
                  // Check if last turn
                  $('.battle-log').append(`<div class="turn" turn-order="${turn}"><div class="turn-header">Match result</div></div>`);

                  // Append LOG lines
                  let lines = '';

                  rowLog[0].strings.forEach(string => {
                    lines += `<span>${string.replace('GYM_LEADER', '<?= $leaderName ?>')}</span>`;
                  });

                  $(`.battle-log .turn[turn-order=${turn}]`).append(`<div class="half">${lines}</div>`);

                  let selector;

                  rowLog[0].strings[0].indexOf('GYM_LEADER') === 0 ? selector = '.user-team' : selector = '.leader-team';

                  $(`${selector} .party .party-pokemon.active`).removeClass('active').addClass('faint');
                  $(`${selector} .pokemons .pokemon.active`).removeClass('active');
                } else {
                  // Not last turn
                  $('.battle-log').append(`<div class="turn" turn-order="${turn}"><div class="turn-header">Turn ${turn}</div></div>`);

                  rowLog.forEach((row, index) => {
                    // Append LOG lines
                    let lines = '';

                    row.strings.forEach(string => {
                      lines += `<span>${string.replace('GYM_LEADER', '<?= $leaderName ?>')}</span>`;
                    });

                    $(`.battle-log .turn[turn-order=${turn}]`).append(`<div class="half">${lines}</div>`);

                    setTimeout(() => {
                      let switched = row.switched;
                      let damage = row.damage;
                      let heal = row.heal;

                      for (let i = 1; i <= 2; i++) {
                        setTimeout(() => {
                          if (heal[i - 1]) {
                            let selector;

                            heal[i - 1].user !== 'GYM_LEADER' ? selector = '.user-team' : selector = '.leader-team';

                            let currentHealth = parseInt($(`${selector} .pokemons .pokemon[pokemon-id=${parseInt(heal[i - 1].pokemon)}] .health .total .var`).text());
                            let maxHealth = parseInt($(`${selector} .pokemons .pokemon[pokemon-id=${parseInt(heal[i - 1].pokemon)}] .health .total .max`).text());
                            let newHealth = currentHealth + heal[i - 1].heal_amount;

                            newHealth > maxHealth ? newHealth = maxHealth : newHealth = newHealth;

                            let barWidth = (newHealth * 100) / maxHealth;

                            $(`${selector} .pokemons .pokemon[pokemon-id=${parseInt(heal[i - 1].pokemon)}] .health .var`).text(newHealth);
                            $(`${selector} .pokemons .pokemon[pokemon-id=${parseInt(heal[i - 1].pokemon)}] .health-bar .bar`).width(`${barWidth}%`);
                          }

                          var faint;
                          var faintedPokemon;
                          var selector;

                          if (damage[i - 1]) {
                            damage[i - 1].user !== 'GYM_LEADER' ? selector = '.user-team' : selector = '.leader-team';

                            let currentHealth = parseInt($(`${selector} .pokemons .pokemon[pokemon-id=${parseInt(damage[i - 1].pokemon)}] .health .total .var`).text());
                            let maxHealth = parseInt($(`${selector} .pokemons .pokemon[pokemon-id=${parseInt(damage[i - 1].pokemon)}] .health .total .max`).text());
                            let newHealth = currentHealth - damage[i - 1].damage_took;

                            if (newHealth < 0) {
                              newHealth = 0;
                            }

                            let barWidth = (newHealth * 100) / maxHealth;

                            setTimeout(() => {
                              if (newHealth > 0 && newHealth !== currentHealth) {
                                $(`${selector} .pokemons .pokemon[pokemon-id=${parseInt(damage[i - 1].pokemon)}] .sprite img`).effect('bounce', { distance: 10 }, 750);
                              } else if (newHealth === 0) {
                                $(`${selector} .pokemons .pokemon[pokemon-id=${parseInt(damage[i - 1].pokemon)}] .sprite img`).animate({ opacity: 0.01 }, 750);
                              }

                              $(`${selector} .pokemons .pokemon[pokemon-id=${parseInt(damage[i - 1].pokemon)}] .health .var`).text(newHealth);
                              $(`${selector} .pokemons .pokemon[pokemon-id=${parseInt(damage[i - 1].pokemon)}] .health-bar .bar`).width(`${barWidth}%`);
                            }, 1000);

                            if (newHealth === 0) {
                              faint = true;
                              faintedPokemon = parseInt(damage[i - 1].pokemon);
                            }
                          }

                          if (switched && !faint) {
                            $(`${selector} .pokemons .pokemon.active`).removeClass('active');
                            $(`${selector} .pokemons .pokemon[pokemon-id=${parseInt(switched)}]`).addClass('active');

                            $(`${selector} .party .party-pokemon.active`).removeClass('active');
                            $(`${selector} .party .party-pokemon[pokemon-id=${parseInt(switched)}]`).addClass('active');
                          }

                          if (faint) {
                            setTimeout(() => {
                              $(`${selector} .party .party-pokemon[pokemon-id=${faintedPokemon}]`).addClass('faint');

                              $(`${selector} .pokemons .pokemon.active`).removeClass('active');
                              $(`${selector} .pokemons .pokemon[pokemon-id=${parseInt(switched)}]`).addClass('active');

                              $(`${selector} .party .party-pokemon.active`).removeClass('active');
                              $(`${selector} .party .party-pokemon[pokemon-id=${parseInt(switched)}]`).addClass('active');
                            }, 2000);
                          }
                        }, i * 2000);
                      }
                    }, index * 1000);
                  });
                }

                $('.battle-log').scrollTop($('.battle-log .turn:last .half:last span:last').position().top);
              }, i * 6000);
            });
          }
        });
      });

      $('.loading').remove();
    });
  </script>
</head>

<body>
  <div class="loading"></div>
  <div class="container">
    <div class="battle-wrapper">
      <div class="user-team">
        <div class="section-title"><?= $userName ?></div>
        <div class="pokemons"></div>
        <div class="party"></div>
      </div>
      <div class="battle-log">
        <div class="handler">
          <button type="button" id="handler">
            <i class="material-icons">play_arrow</i>
          </button>
        </div>
      </div>
      <div class="leader-team">
        <div class="section-title"><?= $leaderName ?></div>
        <div class="pokemons"></div>
        <div class="party"></div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(() => {
      document.title = '<?= $userName ?> vs. <?= $leaderName ?>'
    });
  </script>
</body>

</html>
