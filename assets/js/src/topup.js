if (document.querySelector(".loaderTopUp")) {
  const element = document.getElementById("submitTopUp");
  setInterval(function () {
    var button = document.querySelector('.continueBtn');
    element.style.display = "block";
    button.click();
  }, 2000);
}

if (document.querySelector(".continueBtn")) {
  const element = document.querySelector(".continueBtn");
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
