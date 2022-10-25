import capitalize from './capitalizeString';
import stringify from './stringifyNationalNumber';

function updateDisplay(element, pokemon) {
    $(`${element} .species .national-number .value`)
        .text(stringify(pokemon.id));

    $(`${element} .species .name input[name=species]`)
        .val(capitalize('normal', pokemon.name));

    $(`${element} .sprite img`)
        .attr('src', `https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/${pokemon.id}.png`);

    let types = '';

    pokemon.types.forEach(type => {
        types += `
            <object
                class="type ${type.type.name}"
                data="/pokemon-fangame/assets/images/types/${type.type.name}.svg"
                type="image/svg+xml"
                title="${type.type.name}"
            >
            </object>
        `;
    });

    $(`${element} .types`)
        .empty()
        .append(types);
}

export default updateDisplay;
