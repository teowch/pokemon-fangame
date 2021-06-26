function renderStats() {
  let stats = [
    "health",
    "attack",
    "defense",
    "special-attack",
    "special-defense",
    "speed"
  ];

  let labels = [
    "HP",
    "Attack",
    "Defense",
    "SP. Attack",
    "SP. Defense",
    "Speed"
  ]

  let element = '';

  stats.forEach((stat, index) => {
    element +=
    `
    <tr class="${stats[index]}">
    <th class="label">
    <span class="value">
    ${labels[index]}
    </span>
    </td>
    <td class="base">
    <span class="value">0</span>
    </td>
    <td class="iv input-cell">
    <input name="${stats[index]}-iv" type="number" min="0" max="31" value="31">
    </td>
    <td class="ev input-cell">
    <input name="${stats[index]}-ev" type="number" min="0" max="252" value="0">
    </td>
    <td class="chart">
    <progress value="0" max="100"></progress>
    </td>
    <td class="total">
    <span class="value">0</span>
    </td>
    </tr>
    `;
  });

  return element;
}

async function renderAllMoves() {
  let { data: { moves } } = await $.getJSON('./src/actions/get/getMoves.php');

  let element = '';

  moves.forEach(name => {
    name = name.split('-');
    name = name.map(nameVal => {
      nameVal = nameVal.charAt(0).toUpperCase() + nameVal.slice(1);
      return nameVal;
    })
    name = name.join(' ');
    element += `<option value="${name}">`;
  });

  return element;
}

async function renderMoves() {
  let element = '';

  for (let index = 1; index <= 4; index++) {
    element +=
    `
    <tr class="move${index}">
    <td class="move input-cell">
    <input name="move" type="text" placeholder="..." list="pokemon-moves">
    <datalist id="pokemon-moves">
    ${await renderAllMoves()}
    </datalist>
    </td>
    <td class="move-type">
    <div class="wrapper"></div>
    </td>
    <td class="power">
    <span class="value"></span>
    </td>
    <td class="category">
    <span class="value"></span>
    </td>
    <td class="accuracy">
    <span class="value"></span>
    </td>
    </tr>
    <tr class="move${index} move-description">
    <td colspan="5" class="description">
    <span class="value"></span>
    </td>
    </tr>
    `;
  }

  return element;
}

async function renderSpecies() {
  let { data: { species } } = await $.getJSON('./src/actions/get/getSpecies.php');

  let element = '';

  species.forEach(name => {
    name = name.split('-');
    name = name.map(nameVal => {
      nameVal = nameVal.charAt(0).toUpperCase() + nameVal.slice(1);
      return nameVal;
    })
    name = name.join(' ');
    element += `<option value="${name}">`;
  });

  return element;
}

async function renderBuilder() {
  let element =
  `
  <div class="pokemon-display">
  <div class="table-wrapper">
  <table class="vertical-table pokemon-table">
  <tr class="header">
  <th>Pokémon</th>
  </tr>
  <tr>
  <td class="species">
  <div class="wrapper">
  <div class="national-number">
  <span class="label">
  No.
  </span>
  <span class="value">
  000
  </span>
  </div>
  <div class="name input-cell">
  <input name="species" type="text" placeholder="..." list="pokemon-species">
  <datalist id="pokemon-species">
  ${await renderSpecies()}
  </datalist>
  </div>
  </div>
  </td>
  </tr>
  <tr>
  <td class="sprite">
  <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/0.png">
  </td>
  </tr>
  <tr class="header">
  <th>Types</th>
  </tr>
  <tr>
  <td class="types">
  <div class="wrapper">
  <img src="https://via.placeholder.com/40" class="placeholder">
  </div>
  </td>
  </tr>
  </table>
  </div>
  </div>
  <div class="pokemon-data">
  <div class="table-wrapper">
  <table class="stats-table">
  <colgroup>
  <col class="label-col">
  <col class="base-stat-col">
  <col class="iv-col">
  <col class="ev-col">
  <col class="chart-col">
  <col class="total-stat-col">
  </colgroup>
  <thead class="header">
  <tr>
  <th colspan="2">Base Stats</th>
  <th>IVs</th>
  <th>EVs</th>
  <th colspan="2">Total</th>
  </tr>
  </thead>
  <tbody class="body">
  ${renderStats()}
  </tbody>
  </table>
  </div>
  <div class="table-wrapper">
  <table class="moves-table">
  <colgroup>
  <col class="name-col">
  <col class="type-col">
  <col class="power-col">
  <col class="category-col">
  <col class="accuracy-col">
  </colgroup>
  <thead class="header">
  <tr>
  <th>Move</th>
  <th>Type</th>
  <th>Pow.</th>
  <th>Cat.</th>
  <th>Acc.</th>
  </tr>
  </thead>
  <tbody class="body">
  ${await renderMoves()}
  </tbody>
  </table>
  </div>
  </div>
  `;

  return element;
}

export default renderBuilder;
