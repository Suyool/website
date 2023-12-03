export const formatMobileNumber = (value) => {
    const digitsOnly = value.replace(/\D/g, "");
    const truncatedValue = digitsOnly.slice(0, 8);
    console.log(truncatedValue[0]);
    if (digitsOnly.length === 0) {
        return "";
    }
    if (truncatedValue[0] !== "undefined" && truncatedValue[0] !== "0" && truncatedValue[0] !== "7" && truncatedValue[0] !== "8") {
        return "0" + truncatedValue;
    }
    if (truncatedValue.length > 3) {
        return truncatedValue.replace(/(\d{2})(\d{3})(\d{3})/, "$1 $2 $3");
    }
    return truncatedValue;
};

export const capitalizeFirstLetters = (inputString) => {
    if(["dsl", "4d"].includes(inputString)) return inputString.toUpperCase();
    let words;
    if (inputString) {
        words = inputString.split(' ');

        const capitalizedWords = words.map(word => {
            const firstChar = word.charAt(0).toUpperCase();
            const restOfWord = word.slice(1).toLowerCase();
            return firstChar + restOfWord;
        });

        return capitalizedWords.join(' ');
    } else {
        return inputString;
    }
};