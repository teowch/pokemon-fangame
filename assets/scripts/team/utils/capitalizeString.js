function capitalizeString(mode, inputString) {
    let string;
    let index;

    if (mode === 'normal') {
        string =
            inputString.substring(0, 1).toUpperCase() +
            inputString.substring(1).toLowerCase();

        if (string.indexOf('-') > 0) {
            index = string.indexOf('-');

            do {
                string =
                    string.substring(0, index) +
                    ' ' +
                    string.substring(index + 1, index + 2).toUpperCase() +
                    string.substring(index + 2).toLowerCase();

                index = string.indexOf('-');
            } while (index > 0);
        }
    }

    if (mode === 'reverse') {
        string = inputString.toLowerCase();

        if (string.indexOf(' ') > 0) string = string.replaceAll(' ', '-');
        if (string.indexOf('.') > 0) string = string.replaceAll('.', '-');
    }

    return string;
}

export default capitalizeString;