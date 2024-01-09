// if (document.querySelector(".loaderTopUp")) {
//   setInterval(function () {
//     document.getElementById("submitTopUp").style.display = "block";
//   }, 2000);
// }
// if (document.querySelector(".loaderTopUp")) {
//   const element = document.getElementById("submitTopUp");
//   setInterval(function () {
//     var button = document.querySelector('.continueBtn');
//     element.style.display = "block";
//     // button.click();
//   }, 2000);
// }

if (document.querySelector(".continueBtn")) {
  const element = document.querySelector(".continueBtn");
  var loader = document.getElementsByClassName("loaderTopUp2")[0];
  element.addEventListener("click", function () {
    loader.style.display="none";
    document.getElementById("embed-target").style.display="block";
  });
}

if (document.querySelector(".continueBtnrtp")) {
  const element = document.querySelector(".continueBtnrtp");
  var loader = document.getElementsByClassName("loaderTopUp2")[0];
  element.addEventListener("click", function () {
    loader.style.display="none";
    document.getElementById("embed-target").style.display="block";
  });
}

if (document.querySelector(".actionAppSuccess")) {
  const actionAppSuccess = document.querySelector(".actionAppSuccess");
  actionAppSuccess.addEventListener("click", function () {
    if (navigator.userAgent.match(/Android/i)) {
      window.AndroidInterface.callbackHandler("topupSuccess");
    } else {
      window.webkit.messageHandlers.callbackHandler.postMessage("topupSuccess");
    }
  });
}

if (document.querySelector(".actionAppFailed")) {
  const actionAppFailed = document.querySelector(".actionAppFailed");
  actionAppFailed.addEventListener("click", function () {
    if (navigator.userAgent.match(/Android/i)) {
      window.AndroidInterface.callbackHandler("topupFailed");
    } else {
      window.webkit.messageHandlers.callbackHandler.postMessage("topupFailed");
    }
  });
}


if (document.querySelector(".back")) {
  const action = document.querySelector(".back");
  action.addEventListener("click", function () {
    // Submit the form
    if (navigator.userAgent.match(/Android/i)) {
      window.AndroidInterface.callbackHandler("GoToApp");
    } else {
      window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
    }
  });
}
if (document.querySelector(".actionBrowser")) {
  const action = document.querySelector(".actionBrowser");
  action.addEventListener("click", function () {
    // Submit the form
    const myCookieValue = getCookie('card_payment_url');

    window.location.href = decodeURIComponent(myCookieValue)
  });
}
// Function to get a specific cookie value by name
function getCookie(cookieName) {
  // Split all cookies into an array
  const cookies = document.cookie.split(';');

  // Loop through each cookie to find the one with the provided name
  for (let i = 0; i < cookies.length; i++) {
    let cookie = cookies[i].trim();

    // Check if this is the cookie you're looking for
    if (cookie.startsWith(cookieName + '=')) {
      // Extract and return the cookie value
      return cookie.substring(cookieName.length + 1);
    }
  }

  // Return null if the cookie is not found
  return null;
}

// Usage example


