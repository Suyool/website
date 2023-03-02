function sendAuthCodeBySMS(){
    function enableSmsButtonTimer(){
        $(".resend-counter").show()

        var remaininTime = 20;
        var counterInterval = setInterval(function(){
            remaininTime--;
            if(remaininTime == 0){
                $(".send-code-sms").removeAttr("disabled");
                $(".resend-counter").text(20).addClass("hidden");
                clearInterval(counterInterval);
            }else
                $(".resend-counter").text(remaininTime);
        },1000);
    }

    $(".send-code-sms").click(function (e) {
        e.preventDefault();
        //disable send code by sms button
        $(this).attr("disabled","disabled");
        //show resend sms counter
        $(".resend-counter").removeClass("hidden");
        //start counter for sms button to be enabled and allow resend
        enableSmsButtonTimer();
        var url = location.protocol + "//" + window.location.hostname + window.location.pathname;
        url = url.split('/2fa')[0];
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: url+'/sendAuthCode',
            success: function (data) {
                showAjaxMessage(data)
            }
        });
    })
}

$(function () {
    sendAuthCodeBySMS();
});