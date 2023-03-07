$('.submitTextDownloadAppBtn').on('click', function(e) {
    e.preventDefault();

    // Get the mobile number from the input field
    var mobileNumber = $(this).closest('.input-main-cont').find('.mobileTextDownloadApp').val();
    // Send an AJAX request to the server
    $.ajax({
        url: '/invitationCard/submitInvitationCard',
        method: 'POST',
        data: { mobileNumber: mobileNumber,
            code: $(this).attr('code')},
        success: function(response) {
            // Show the response message in the modal
            $('#invitationModal .modal-body').append('<p>' + response.RespDesc + '</p>');
            // Modify the button label based on the RespCode value
            if(response.RespCode == 0 || response.RespCode == -3) {
                $('#closeModelResubscribe').text('OK');
            } else {
                $('#closeModelResubscribe').text('Try Again');
            }
            // Show the modal
            $('#invitationModal').modal('show');
        },
        error: function(xhr, status, error) {
            // Show an error message in the console
            console.error(error);
        }
    });
});

$(document).ready(function() {
    // Add an oninput event listener to the #mobile element
    $('.mobileTextDownloadApp').on('input', function(event) {
        //Parent division of the mobile input
        var parentDivision = $(this).closest('.input-main-cont').parent().attr("class");

        //Main division of the mobile input
        var mainDivision = '';
        //Check if the parent division is skash-more-than-cash-section - home-v3
        if(parentDivision.indexOf("invitation-card-section-bottom") > 0){
            //Main division of the form
            mainDivision = '.invitation-card-section-bottom';

            //Check if the parent division is invitation-card-section-top - invitation-card
        } else if(parentDivision.indexOf("invitation-card-section-top") > 0){
            //Main division of the form
            mainDivision = '.invitation-card-section-top';
        }

        //When entering the mobile number enable the submit button if the input length is > 7
        if($(this).val().length > 6){
            $(mainDivision+' .submitTextDownloadAppBtn').removeAttr("disabled");
        } else {
            $(mainDivision+' .submitTextDownloadAppBtn').attr("disabled","disabled");
        }
    });
});
if(document.querySelector('.copy-to-clipboard')){
  // Get the element with the 'copy-to-clipboard' class
  const copyBtn = document.querySelector('.copy-to-clipboard');

  // Get the value of the 'data-to-copy' attribute
  const copyText = copyBtn.getAttribute('data-to-copy');

  // Add a click event listener to the button
  copyBtn.addEventListener('click', function() {
    // Create a new textarea element to hold the copied text
    const textarea = document.createElement('textarea');
    textarea.value = copyText;
    document.body.appendChild(textarea);

    // Select the text in the textarea and copy it to the clipboard
    textarea.select();
    document.execCommand('copy');

    // Remove the textarea element from the DOM
    document.body.removeChild(textarea);

    // Show a success message to the user
  //   alert('Copied to clipboard: ' + copyText);
  });
}
if(document.querySelector('.open-suyool-account')){
const open_suyool_account = document.querySelector('.open-suyool-account');

open_suyool_account.addEventListener('click',function(){
  if (navigator.userAgent.match(/Android/i)) {
    window.location.href = "https://skashapp.page.link/app_install";
} else if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {
    window.location.href = "https://skashapp.page.link/app_install";
} else{
}
})
}

if(document.querySelector('.generate-code')){
  document.querySelector('.generate-code').addEventListener('click',function(){
    const tag=document.querySelector('.generate-code');
    if(tag.hasAttribute('data-code')){
      window.location.href="/codeGenerated?codeATM="+tag.getAttribute('data-code')
    }else{
      if(document.querySelector('.error')){
    document.querySelector('.error').style.display='block';
      }
    }
  })
}
function resubscribe(uniqueCode, flag) {
    jQuery.ajax({
        type: "GET",
        url: '/unsubscribeMarketing/resubscribe?uniqueCode=' + uniqueCode + '&flag=' + flag,
        dataType: 'json',
    });
}

