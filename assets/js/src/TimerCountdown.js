// Function to pad numbers with a leading zero if they are single digits
var countDownDate = new Date("April 21, 2024 16:00:00").getTime();

function padNumber(num) {
    return (num < 10 ? '0' : '') + num;
}

// Update the count down every 1 second
var x = setInterval(function() {
    // Get today's date and time
    var now = new Date().getTime();

    // Find the distance between now and the count down date
    var distance = countDownDate - now;

    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Pad single-digit numbers with leading zeros
    days = padNumber(days);
    hours = padNumber(hours);
    minutes = padNumber(minutes);
    seconds = padNumber(seconds);

    // Output the result in an element with id="countdownRallyPaper"
    var countdownElement = document.getElementById("countdownRallyPaper");
    if (countdownElement) {
        countdownElement.innerHTML = days + ":" + hours + ":" + minutes + ":" + seconds;

        // If the count down is over, write some text
        if (distance < 0) {
            clearInterval(x);
            countdownElement.innerHTML = "EXPIRED";
        }
    }
}, 1000);
