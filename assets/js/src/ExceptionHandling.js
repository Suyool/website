if(document.querySelector(".close")){

  var button=document.querySelector(".close");
  button.addEventListener("click",()=>{
    if (navigator.userAgent.match(/Android/i)) {
      window.AndroidInterface.callbackHandler("GoToApp");
    } else {
      window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
    }
  });

}