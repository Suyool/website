function getMobileOperatingSystem() {
    var userAgent = navigator.userAgent || navigator.vendor || window.opera;

    // Windows Phone must come first because its UA also contains "Android"
    if (/windows phone/i.test(userAgent)) {
        return "Windows Phone";
    }

    if (/Tablet/i.test(userAgent)) {
        return "unknown";
    }

    if (/android/i.test(userAgent)) {
        return "Android";
    }

    // iOS detection from: http://stackoverflow.com/a/9039885/177710
    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
        return "iOS";
    }

    return "unknown";
}
    //Execute Google Analytics and Facebook Pixel events
    // function googleFacebookEvents(eventNameKey, eventName, eventCategory, eventLabel){
    //     //Set the name of the resolution
    //     var eventResolution = '';
    //     //If Ipad - Add "Ipad - " to the eventLabel
    //     if(window.screen.width > 767 && window.screen.width < 769){
    //         eventResolution = 'Ipad - ';
    //         //If Mobile - Add "Mobile - " to the eventLabel
    //     }else if(window.screen.width < 992){
    //         eventResolution = 'Mobile - ';
    //         //If Desktop - Add "Desktop - " to the eventLabel
    //     }else{
    //         eventResolution = 'Desktop - ';
    //     }
    
    //     //Google Analytics
    //     gtag('event', eventName, {'event_category': eventCategory, 'event_label': eventResolution + eventLabel});
    //     //Facebook Pixel
    //     fbq('trackCustom', eventCategory, {eventNameKey: eventName});
    // }
    function getTheApp(position, downloadAppUrl) {
        //Event label - Menu or Homepage - top
        var eventLabel = "";
        //If the downloadAppUrl isset uset it, otherwise set the default value ''
        var downloadAppUrl = (typeof downloadAppUrl != "undefined") ? downloadAppUrl : "";

        //Call the googleFacebookEvents to execute Google and Facebook events
        // googleFacebookEvents('Action', 'Get The APP', 'App download', eventLabel);

        //If width > 992 - scroll to the bottom section to download the app
        if(window.screen.width > 768){
                // console.log(window.screen.width );
        }else{
            //Get the Mobile Operating System
            var osMobile = getMobileOperatingSystem();

            //If the Download App URL isset use it
            if(downloadAppUrl != ""){
                location = downloadAppUrl;

                //Otherwise check the OS and call the Apple or Play Store URL
            }else{
                location =DefaultDownloadLink;
                // //If IOS
                // if(osMobile == 'iOS'){
                //     location = iosDownloadLink;
                //
                //     //Otherwise
                // }else{
                //     location = androidDownloadLink;
                // }
            }
        }
    }


/** click button once **/
var clicked_once = false;
function clickOne(downloadAppUrl, conversion_code,uri) {

    if (!clicked_once) {
        clicked_once = true;
        console.log("ok");

        $(".conversion-button").css("margin-top","20px");
        // gtag('event', 'conversion', {'send_to': 'AW-799970313/'+conversion_code});
    }else{
        console.log("already clicked");

    }
    getTheApp(uri,downloadAppUrl);

}

if(document.querySelector(".open-suyool-account")){
    const open_suyool_account = document.querySelector(".open-suyool-account");
    
    open_suyool_account.addEventListener("click",function(){
      clickOne("suyoolpay://suyool.com/suyool","","/");
    })
    }