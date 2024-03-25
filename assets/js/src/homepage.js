$(document).ready(function () {
    function updateToCurrency() {
        var fromCurrency = $('#fromCurrency').val();
        var toCurrency = $('#toCurrency');

        if (fromCurrency === "LBP") {
            toCurrency.val("USD");
        } else if (fromCurrency === "USD") {
            toCurrency.val("LBP");
        }
    }

    function updateFromCurrency() {
        var fromCurrency = $('#fromCurrency');
        var toCurrency = $('#toCurrency').val();

        if (toCurrency === "LBP") {
            fromCurrency.val("USD");
        } else if (toCurrency === "USD") {
            fromCurrency.val("LBP");
        }
    }

    function changeCurrencySymbol() {
        var fromCurrency = $('#fromCurrency').val();
        var toCurrency = $('#toCurrency').val();
        var currencySymbol = $('#currencySymbol');

        if ((fromCurrency === "LBP" && toCurrency === "USD")) {
            currencySymbol.text("L.L");
        } else if (fromCurrency === "USD" && toCurrency === "LBP") {
            currencySymbol.text("$");
        }
    }

    function swapCurrencies() {
        var fromCurrency = $('#fromCurrency');
        var toCurrency = $('#toCurrency');

        // Swap the selected values
        var temp = fromCurrency.val();
        fromCurrency.val(toCurrency.val());
        toCurrency.val(temp);

        // Update currency symbol after swapping
        changeCurrencySymbol();
    }

    function convert() {
        var buyRateText = $('#buyAmountDesVal').text().trim();
        var sellRateText = $('#sellAmountDesVal').text().trim();

        // Remove non-numeric characters except for the dot (.)
        var buyRate = parseFloat(buyRateText.replace(/[^\d.]/g, ''));
        var sellRate = parseFloat(sellRateText.replace(/[^\d.]/g, ''));
        var amount = parseFloat($('#amount').val());
        var fromCurrency = $('#fromCurrency').val();
        var toCurrency = $('#toCurrency').val();
        var buyAmountElement = $('#buyAmount');
        var sellAmountElement = $('#sellAmount');

        var buyAmount, sellAmount, currency;

        if (fromCurrency === 'USD' && toCurrency === 'LBP') {
            buyAmount = (amount * buyRate).toFixed(0);
            sellAmount = (amount * sellRate).toFixed(0);
            currency = 'LBP';
        } else {
            buyAmount = (amount / buyRate).toFixed(0);
            sellAmount = (amount / sellRate).toFixed(0);
            currency = 'USD';
        }

        $('.currencyConverted').text(currency);
        buyAmountElement.text(numberWithCommas(buyAmount));
        sellAmountElement.text(numberWithCommas(sellAmount));
    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Call the initial functions
    updateToCurrency();
    updateFromCurrency();
    changeCurrencySymbol();

    // Attach event handlers using jQuery
    $('#exchangeIcon').click(function () {
        swapCurrencies();
    });

    $('#fromCurrency').change(function () {
        updateToCurrency();
        changeCurrencySymbol();
    });

    $('#toCurrency').change(function () {
        updateFromCurrency();
        changeCurrencySymbol();
    });

    $('#convertButton').click(function () {
        convert();
    });
    // Format the amount input on input change
    // $('#amount').on('input', function () {
    //     var value = $(this).val().replace(/[^\d.]/g, ''); // Remove non-numeric characters except for dot (.)
    //     $(this).val(numberWithCommas(value)); // Format the value with commas
    // });

});
