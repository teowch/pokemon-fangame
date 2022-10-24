import pokeapi from './services/pokeapi';

import capitalize from './utils/capitalizeString';

import renderBuilder from './utils/renderBuilder';

import updateDisplay from './utils/updateDisplay';
import updateMove from './utils/updateMove';
import updateStats from './utils/updateStats';

async function insertDisplay(e, slot) {
  $(`#${slot}-content .pokemon-table input[name=species]`)
  .blur();

  $('body')
  .append('<div class="loading"></div>');

  try {
    let pokemon = await pokeapi('pokemon', capitalize('reverse', $(e.currentTarget).val()));

    $(`.tab-nav #${slot}-tab .sprite img`)
    .attr('src', `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemon.id}.png`);

    $(`.tab-nav #${slot}-tab .species`)
    .text(capitalize('normal', pokemon.name));

    updateDisplay(
      `#${slot}-content .pokemon-display .pokemon-table`,
      {
        id: pokemon.id,
        name: pokemon.name,
        types: pokemon.types
      }
    );

    updateStats(
      `#${slot}-content .pokemon-data .stats-table`,
      pokemon.stats
    );
  } catch (error) {
    alert('Pokémon not found');
  }

  $('.loading')
  .remove();
}

$(document).ready(() => {
  $('.tab-nav .tab').on('click', async e => {
    e.preventDefault();

    if ($(e.currentTarget).hasClass('selected')) return;
    if ($(e.currentTarget).hasClass('empty')) return;

    if ($(e.currentTarget).hasClass('new')) {
      let order = $('.tab-content .slot').length + 1;
      let slot = `slot${order}`;

      $(e.currentTarget)
      .removeClass('new')
      .attr('id', `${slot}-tab`)
      .attr('href', `#${slot}-content`);

      $(e.currentTarget)
      .empty()
      .append('<div class="sprite"><img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/0.png"></div><div class="species">...</div>');

      $('.tab-content').append(`<div id="${slot}-content" class="pane slot selected">${await renderBuilder()}</div>`);

      let statsTableRows = `.tab-content #${slot}-content .pokemon-data .stats-table tbody.body tr`;

      $(statsTableRows).each(index => {
        let row = `${statsTableRows}:eq(${index})`;

        $(`${row} .ev input`).on('input', event => {
          let evsSum =
          parseInt($(`${statsTableRows}.health .ev input`).val()) +
          parseInt($(`${statsTableRows}.attack .ev input`).val()) +
          parseInt($(`${statsTableRows}.defense .ev input`).val()) +
          parseInt($(`${statsTableRows}.special-attack .ev input`).val()) +
          parseInt($(`${statsTableRows}.special-defense .ev input`).val()) +
          parseInt($(`${statsTableRows}.speed .ev input`).val());

          if (evsSum > 508) {
            $(event.currentTarget).val(parseInt($(event.currentTarget).val()) - Math.abs((evsSum - 508))).blur();
          }
        });

        $(`${row} .iv input, ${row} .ev input`).on('input', event => {
          if ($(event.currentTarget).val() == '') {
            $(event.currentTarget).val(0);
          }

          if ($(row).hasClass('health')) {
            let newTotal = parseInt(Math.trunc((2 * parseInt($(`${row} .base .value`).text()) + parseInt($(`${row} .iv input`).val()) + (parseInt($(`${row} .ev input`).val()) / 4)) + 100 + 10));

            $(`${row} .chart progress`).attr('value', newTotal);
            $(`${row} .total .value`).text(newTotal);
          } else {
            let newTotal = parseInt(Math.trunc((2 * parseInt($(`${row} .base .value`).text()) + parseInt($(`${row} .iv input`).val()) + (parseInt($(`${row} .ev input`).val()) / 4)) + 5));

            $(`${row} .chart progress`).attr('value', newTotal);
            $(`${row} .total .value`).text(newTotal);
          }

          $(event.currentTarget).val(parseInt($(event.currentTarget).val()));
        });
      });

      if ($('.container.create-team button#submit-team').length < 1) {
        $('.container.create-team').append(`<div class="submit"><button type="button" class="submit-button" id="submit-team">Submit</button></div>`);
      }

      /**
      *  Update display
      */

      $(`#${slot}-content .pokemon-table input[name=species]`).on('input', async e => {
        if (e.originalEvent.inputType != 'insertReplacementText') return;
        await insertDisplay(e, slot);
      });

      $(`#${slot}-content .pokemon-table input[name=species]`).on('change', async e => {
        await insertDisplay(e, slot);
      });

      /**
      *  Update moves
      */

      for (let index = 1; index <= 4; index++) {
        $(`#${slot}-content .moves-table .move${index} input[name=move]`).on('change', async e => {

          $(`#${slot}-content .moves-table .move${index} input[name=move]`)
          .blur();

          $('body')
          .append('<div class="loading"></div>');

          try {
            let move = await pokeapi('move', capitalize('reverse', $(e.currentTarget).val()));

            updateMove(
              `#${slot}-content .pokemon-data .moves-table .move${index}`,
              {
                name: move.names[7].name,
                description: move.effect_entries,
                chance: move.effect_chance,
                power: move.power,
                accuracy: move.accuracy,
                category: move.damage_class.name,
                type: move.type.name
              }
            );
          } catch (error) {
            alert('Move not found');
          }

          $('.loading')
          .remove();
        });
      }

      if (order < 7) {
        $('.tab-nav .tab').eq(order)
        .removeClass('empty')
        .addClass('new')
        .append('<i class="material-icons">add</i>');
      }
    }

    $('.submit button#submit-team').unbind('click').on('click', async event => {
      event.preventDefault();

      try {
        await pokeapi('pokemon', capitalize('reverse', $(e.currentTarget).val()));

        let teamName;

        if ($('.team-name input').val().length < 1) {
          teamName = [...Array(12)].map(() => Math.floor(Math.random() * 16).toString(16)).join('');
        } else {
          teamName = $('.team-name input').val();
        }

        let userId;

        await $.get({
          url: '/pokemon-fangame/src/actions/get/getSession.php',
          success: data => {
            userId = data;
          }
        });

        let userTeam = {
          name: teamName,
          pokemons: [],
          user_id: userId
        };

        $('.tab-content .slot').each(async index => {
          if ($(`.tab-content .slot:eq(${index}) .pokemon-display .pokemon-table .species input`).val() == null) return;
          if ($(`.tab-content .slot:eq(${index}) .pokemon-display .pokemon-table .species input`).val() == '...') return;

          userTeam.pokemons.push({
            species: capitalize('reverse', $(`.tab-content .slot:eq(${index}) .pokemon-display .pokemon-table .species input`).val()),
            ivs: [
              $(`.tab-content .slot:eq(${index}) .pokemon-data .stats-table .health .iv input`).val(),
              $(`.tab-content .slot:eq(${index}) .pokemon-data .stats-table .attack .iv input`).val(),
              $(`.tab-content .slot:eq(${index}) .pokemon-data .stats-table .defense .iv input`).val(),
              $(`.tab-content .slot:eq(${index}) .pokemon-data .stats-table .special-attack .iv input`).val(),
              $(`.tab-content .slot:eq(${index}) .pokemon-data .stats-table .special-defense .iv input`).val(),
              $(`.tab-content .slot:eq(${index}) .pokemon-data .stats-table .speed .iv input`).val()
            ],
            evs: [
              $(`.tab-content .slot:eq(${index}) .pokemon-data .stats-table .health .ev input`).val(),
              $(`.tab-content .slot:eq(${index}) .pokemon-data .stats-table .attack .ev input`).val(),
              $(`.tab-content .slot:eq(${index}) .pokemon-data .stats-table .defense .ev input`).val(),
              $(`.tab-content .slot:eq(${index}) .pokemon-data .stats-table .special-attack .ev input`).val(),
              $(`.tab-content .slot:eq(${index}) .pokemon-data .stats-table .special-defense .ev input`).val(),
              $(`.tab-content .slot:eq(${index}) .pokemon-data .stats-table .speed .ev input`).val()
            ],
            moves: [
              capitalize('reverse', $(`.tab-content .slot:eq(${index}) .pokemon-data .moves-table .move1 .move input`).val()),
              capitalize('reverse', $(`.tab-content .slot:eq(${index}) .pokemon-data .moves-table .move2 .move input`).val()),
              capitalize('reverse', $(`.tab-content .slot:eq(${index}) .pokemon-data .moves-table .move3 .move input`).val()),
              capitalize('reverse', $(`.tab-content .slot:eq(${index}) .pokemon-data .moves-table .move4 .move input`).val())
            ]
          });
        });

        await $.ajax({
          url: '/pokemon-fangame/src/actions/insert/insertTeam.php',
          method: 'post',
          data: {
            team: JSON.stringify(userTeam)
          },
          success: function(status) {
            console.log(document.location);
            location.href = "/pokemon-fangame/teams.php";
          }
        });

      } catch (error) {
        console.error(error);
      }
    });

    let slot =
    $(e.currentTarget)
    .attr('id')
    .substring(
      0,
      $(e.currentTarget)
      .attr('id')
      .indexOf('-')
    );

    $('.tab-nav .tab').removeClass('selected');
    $(e.currentTarget).addClass('selected')

    $('.tab-content .pane.selected').removeClass('selected');
    $(`.tab-content #${slot}-content`).addClass('selected');
  });
});
