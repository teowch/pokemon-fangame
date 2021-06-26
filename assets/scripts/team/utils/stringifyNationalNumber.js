function stringifyNationalNumber(intNumber) {
    let strNumber = intNumber.toString();
    let string = '';

    if (strNumber.length < 3) {
        for (let i = 0; i < 3 - strNumber.length; i++) {
            string += '0';
        }
    }

    return string += strNumber;
}

export default stringifyNationalNumber;