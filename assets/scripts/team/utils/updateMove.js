function updateMove(element, move) {
    $(`${element} .move input[name=move]`)
        .val(move.name);

    $(`${element} .move-type .wrapper`)
        .empty()
        .append(`
            <object
                class="type ${move.type}"
                data="http://web.sbs.ifc.edu.br/~pablo.bayerl/unnamedproject/assets/types/icons/${move.type}.svg"
                type="image/svg+xml"
                title="${move.type}"
            >
            </object>
        `);

    $(`${element} .power .value`)
        .text(
            move.power
                ? move.power
                : '-'
        );

    $(`${element} .category .value`)
        .text(`${move.category.substring(0, 1).toUpperCase()}${move.category.substring(1).toLowerCase()}`);

    $(`${element} .accuracy .value`)
        .text(
            move.accuracy
                ? move.accuracy
                : '-'
        );

    $(`${element}.move-description .description .value`)
        .text(
            move.description[0].language.name === 'en'
                ? move.description[0].short_effect.replace('$effect_chance', move.chance)
                : move.description[1].short_effect.replace('$effect_chance', move.chance)
        );
}

export default updateMove;
