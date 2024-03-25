function updateToCurrency() {
    var fromCurrency = document.getElementById("fromCurrency").value;
    var toCurrency = document.getElementById("toCurrency");

    if (fromCurrency === "LBP") {
        toCurrency.value = "USD";
    } else if (fromCurrency === "USD") {
        toCurrency.value = "LBP";
    }
}

function updateFromCurrency() {
    var fromCurrency = document.getElementById("fromCurrency");
    var toCurrency = document.getElementById("toCurrency").value;

    if (toCurrency === "LBP") {
        fromCurrency.value = "USD";
    } else if (toCurrency === "USD") {
        fromCurrency.value = "LBP";
    }
}

function changeCurrencySymbol() {
    var fromCurrency = document.getElementById("fromCurrency").value;
    var toCurrency = document.getElementById("toCurrency").value;
    var currencySymbol = document.getElementById("currencySymbol");

    if ((fromCurrency === "LBP" && toCurrency === "USD")) {
        currencySymbol.textContent = "L.L";
    } else if (fromCurrency === "USD" && toCurrency === "LBP") {
        currencySymbol.textContent = "$";
    }
}

function swapCurrencies() {
    var fromCurrency = document.getElementById("fromCurrency");
    var toCurrency = document.getElementById("toCurrency");

    // Swap the selected values
    var temp = fromCurrency.value;
    fromCurrency.value = toCurrency.value;
    toCurrency.value = temp;

    // Update currency symbol after swapping
    changeCurrencySymbol();
}

function convert() {
    var buyRateText = document.getElementById('buyAmountDesVal').innerText.trim();
    var sellRateText = document.getElementById('sellAmountDesVal').innerText.trim();

    // Remove non-numeric characters except for the dot (.)
    var buyRate = parseFloat(buyRateText.replace(/[^\d.]/g, ''));
    var sellRate = parseFloat(sellRateText.replace(/[^\d.]/g, ''));
    var amount = parseFloat(document.getElementById('amount').value);
    var fromCurrency = document.getElementById('fromCurrency').value;
    var toCurrency = document.getElementById('toCurrency').value;
    var buyAmountElement = document.getElementById('buyAmount');
    var sellAmountElement = document.getElementById('sellAmount');

    var buyAmount, sellAmount, currency;

    if (fromCurrency === 'USD' && toCurrency === 'LBP') {
        buyAmount = (amount * buyRate).toFixed(2);
        sellAmount = (amount * sellRate).toFixed(2);
        currency = 'LBP';
    } else {
        buyAmount = (amount / buyRate).toFixed(2);
        sellAmount = (amount / sellRate).toFixed(2);
        currency = 'USD';
    }

    document.querySelector(".currencyConverted").textContent = currency;
    buyAmountElement.textContent = numberWithCommas(buyAmount);
    sellAmountElement.textContent = numberWithCommas(sellAmount);
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

document.addEventListener('DOMContentLoaded', function() {
    updateToCurrency();
    updateFromCurrency();
    changeCurrencySymbol();

    document.getElementById('fromCurrency').addEventListener('change', function() {
        updateToCurrency();
        changeCurrencySymbol();
    });

    document.getElementById('toCurrency').addEventListener('change', function() {
        updateFromCurrency();
        changeCurrencySymbol();
    });

    document.getElementById('convertButton').addEventListener('click', function() {
        convert();
    });
});
