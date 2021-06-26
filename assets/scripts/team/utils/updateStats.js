function updateStats(element, pokemon) {
  let stats = [
    'health',
    'attack',
    'defense',
    'special-attack',
    'special-defense',
    'speed'
  ];

  let maxStats = [];

  stats.forEach((stat, index) => {
    let total;

    if (stat === 'health') {
      total = Math.trunc((2 * pokemon[index].base_stat + 31 + (0 / 4)) + 100 + 10);

      maxStats.push({
        label: stats[index],
        total: Math.trunc((2 * pokemon[index].base_stat + 31 + (252 / 4)) + 100 + 10)
      });
    } else {
      total = Math.trunc((2 * pokemon[index].base_stat + 31 + (0 / 4)) + 5);

      maxStats.push({
        label: stats[index],
        total: Math.trunc(((2 * pokemon[index].base_stat + 31 + (252 / 4)) + 5) * 1.1)
      });
    }

    $(`${element} .body .${stats[index]} .base .value`)
    .text(pokemon[index].base_stat);

    $(`${element} .body .${stats[index]} .iv input`)
    .val(31);
    
    $(`${element} .body .${stats[index]} .ev input`)
    .val(0);

    $(`${element} .body .${stats[index]} .total .value`)
    .text(total);

    $(`${element} .body .${stats[index]} .chart progress`)
    .val(total);
  });

  maxStats.forEach(stat => {
    $(`${element} .chart progress`)
    .attr('max', Math.max.apply(Math, maxStats.map(stat => stat.total)));
  });
}

export default updateStats;
