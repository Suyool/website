$(document).ready(function () {
    var cachedDataInput;
    var amountInput = document.getElementById("amount");
    var convertButton = document.getElementById("convertButton");

    if (amountInput && convertButton) {
        amountInput.addEventListener("change", function() {
            var nameInput = amountInput.value;
            if (nameInput.trim() !== "") {
                convertButton.removeAttribute("disabled");
            } else {
                convertButton.setAttribute("disabled", true);
            }
        });
    }
    const cachedData = document.getElementById('cachedData');
    if (cachedData){
         cachedDataInput = cachedData.value;

        updateTimeDifference();
        const currentTime = new Date().getTime();
        const lastUpdateTime = new Date(cachedDataInput).getTime();
        const timeDifference = currentTime - lastUpdateTime;

        let intervalTime;
        if (timeDifference < 60000) { // Less than 1 minute
            intervalTime = 1000; // Set interval to 1 second
        } else if (timeDifference < 3600000) { // Less than 1 hour
            intervalTime = 60000; // Set interval to 1 minute
        } else { // More than 1 hour
            intervalTime = 3600000; // Set interval to 1 hour
        }

        setInterval(updateTimeDifference, intervalTime);
    }


    function updateTimeDifference() {
        if (cachedDataInput ) {
            const currentTime = new Date().getTime();
            const lastUpdateTime = new Date(cachedDataInput).getTime();
            const timeDifference = currentTime - lastUpdateTime;

            const seconds = Math.floor(timeDifference / 1000) % 60;
            const minutes = Math.floor(timeDifference / (1000 * 60)) % 60;
            const hours = Math.floor(timeDifference / (1000 * 60 * 60)) % 24;
            const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));

            let timeDifferenceString;

            if (days > 0) {
                timeDifferenceString = `Updated ${days} day${days !== 1 ? 's' : ''} ago`;
            } else if (hours > 0) {
                timeDifferenceString = `Updated ${hours} hr${hours !== 1 ? 's' : ''} ago`;
            } else if (minutes > 0) {
                timeDifferenceString = `Updated ${minutes} min ago`;
            } else {
                timeDifferenceString = `Updated ${seconds} sec ago`;
            }
            document.querySelector('.timeCont').textContent = timeDifferenceString;

        }
    }

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
        var currencylbp = $('.currencyConvertedLBP');
        var currencyusd = $('.currencyConvertedUSD');


        var buyAmount, sellAmount, currency;

        if (fromCurrency === 'USD' && toCurrency === 'LBP') {
            buyAmount = (amount * buyRate).toFixed(0);
            sellAmount = (amount * sellRate).toFixed( 0);
            currency = 'LBP';
        } else {
            buyAmount = (amount / buyRate).toFixed(2);
            sellAmount = (amount / sellRate).toFixed(2);
            currency = 'USD';
        }

        $('.currencyConvertedLBP').text(currency);
        $('.currencyConvertedUSD').text(currency);
        buyAmountElement.text(numberWithCommas(buyAmount));
        buyAmountElement.css("color","#376c92");
        sellAmountElement.css("color","#376c92");
        buyAmountElement.css("font-weight","bolder");
        sellAmountElement.css("font-weight","bolder");
        currencylbp.css("font-weight","bolder");
        currencylbp.css("color","#376c92");
        currencyusd.css("font-weight","bolder");
        currencyusd.css("color","#376c92");
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

