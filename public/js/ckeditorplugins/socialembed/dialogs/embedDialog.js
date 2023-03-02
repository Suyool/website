/*
*
*   Plugin developed by Dimitri Conejo
*   www.dimitriconejo.com
*
*/

CKEDITOR.dialog.add( 'embedDialog', function( editor ) {
    return {
        title: 'أدخل رابط فيسبوك إنستغرام او تويتر',
        minWidth: 400,
        minHeight: 100,
        contents: [
            {
                id: 'tab-basic',
                label: 'Basic Settings',
                elements: [
                    {
                        type: 'text',
                        id: 'embed_url',
                        label: 'Facebook, Instagram, Twitter URL or embed code',
                        validate: CKEDITOR.dialog.validate.notEmpty( "الرجاء إدخال الرابط" )
                    }
                ]
            }
        ],

        onOk: function(){
            var dialog = this;

            var getDialog     = dialog.parts.dialog;
            var iframeURL           = getDialog.find('input').getItem(0).getValue();

            var iframeCode = "";

            //If the URL contains facebook.com - that means correct URL
            if (iframeURL.indexOf("facebook.com") > 0){
                if (iframeURL.indexOf("video") > 0){
                    var iframeSrc = 'https://www.facebook.com/plugins/video.php?href='+encodeURI(iframeURL);
                }else{
                    var iframeSrc = 'https://www.facebook.com/plugins/post.php?href='+encodeURI(iframeURL);
                }
                iframeCode = '<iframe src="'+iframeSrc+'" class="embed-facebook" width="500" height="280" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" allowFullScreen="true"></iframe>';
                //If the URL doesn't contains facebook.com - that means wrong URL
            }

            //If the URL contains facebook.com - that means correct URL
            if (iframeURL.indexOf("instagram.com") > 0){
                var instaCodeArr = iframeURL.split("p/");
                //If the link doesn't contain "p/" , check the "tv/"
                if(instaCodeArr.length < 2){
                    instaCodeArr = iframeURL.split("tv/");
                }
                if(instaCodeArr.length < 2){
                    instaCodeArr = iframeURL.split("reel/");
                }
                //If one of them works, continue
                if(instaCodeArr.length > 1){
                    var instaCode = instaCodeArr[1].split("/");
                    var instaURL = "https://instagram.com/p/"+instaCode[0]+"/embed";
                    iframeCode = '<iframe src="'+encodeURI(instaURL)+'" class="embed-instagram" width="500" height="580" frameborder="0" scrolling="no" allowtransparency="true"></iframe>';
                }
                //If the URL doesn't contains facebook.com - that means wrong URL
            }

            //If the URL contains facebook.com - that means correct URL
            if (iframeURL.indexOf("twitter.com") > 0){
                iframeCode = '<iframe src="'+document.getElementById("productionUrl").value+'twitterIframe?url='+encodeURI(iframeURL)+'" class="embed-twitter" frameborder="0" width="550" height="800"></iframe>';
                //If the URL doesn't contains facebook.com - that means wrong URL
            }

            editor.insertHtml(iframeCode);
        }
    };
});
