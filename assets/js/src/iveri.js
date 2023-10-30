// if (document.getElementById("myFormTopUp")) {
//   document.getElementById("myFormTopUp").submit();
// }

if (document.getElementById("topUpButtonMob")) {
  document
    .getElementById("topUpButtonMob")
    .addEventListener("click", function () {
      // Submit the form
      document.getElementById("myFormRequest").submit();
    });
}

if (document.getElementById("topUpButtonDesk")) {
  document
    .getElementById("topUpButtonDesk")
    .addEventListener("click", function () {
      // Submit the form
      document.getElementById("myFormRequest").submit();
    });
}

if (document.getElementById("submitTopUp")) {
  document.getElementById("submitTopUp").addEventListener("click", function () {
    // Submit the form
    document.getElementById("myFormTopUp").submit();
  });
}

if (document.querySelector(".loaderTopUp")) {
  const element = document.getElementById("submitTopUp");
  setInterval(function () {
    element.style.display = "block";
  }, 2000);
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

if (document.querySelector(".actionBrowser")) {
  const actionBrowser = document.querySelector(".actionBrowser");
  actionBrowser.addEventListener("click", function () {
    if (navigator.userAgent.match(/Android/i)) {
      window.AndroidInterface.callbackHandler("GoToApp");
    } else {
      window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
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
