if(document.getElementById("chatwithteam")){
// Check if the user is on a mobile device
function isMobileDevice() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

// Get a reference to the link
var salesLink = document.getElementById("chatwithteam");

// Define the mobile and desktop URLs
var mobileUrl = "tel:9611290900"; // Replace with your phone number
var desktopUrl = "/contact-us"; // Replace with your desktop URL

// Update the link's href based on the device
if (isMobileDevice()) {
    salesLink.href = mobileUrl;
} else {
    salesLink.href = desktopUrl;
}
}
