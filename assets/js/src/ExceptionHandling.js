if(document.querySelector(".close")){

    var button=document.querySelector(".close");
        button.addEventListener("click",()=>{
            if (navigator.userAgent.match(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i)){
                // alert("hi")
                if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
                    window.webkit.messageHandlers.callbackHandler.postMessage(
                        "GoToApp"
                      );
                } else {
                    // alert("hi")
                    window.AndroidInterface.callbackHandler("GoToApp");
                }
                
            }else{
                document.location.href="/";
            }
        })

}