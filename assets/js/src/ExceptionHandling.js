if(document.querySelector(".close")){

    var button=document.querySelector(".close");
        button.addEventListener("click",()=>{
            if (navigator.userAgent.match(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i)){
                window.webkit.messageHandlers.callbackHandler.postMessage(
                    "GoToApp"
                  );
            }else{
                document.location.href="/";
            }
        })

}