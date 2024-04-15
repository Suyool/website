import ClipboardJS from 'clipboard';

$(document).ready(function() {
    var flagCode;
    var globalCode;
    var mobileValue;

    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: '/aub-login',
            type: 'POST',
            data: formData,
            success: function(response) {
                console.log(response);
                if(response.flagCode === 2){
                    $('#loginError').text('Invalid credentials. Please try again.');
                }else {
                    window.location.href = '/aub-rally-paper';
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                $('#loginError').text('An error has occurred.');
            }
        });
    });


    $('#inviteForm').on('submit', function(e) {
        e.preventDefault();
        // var recaptchaResponse = grecaptcha.getResponse();
        // if (recaptchaResponse.length == 0) {
        //     var imageUrl = '/build/images/warning.svg';
        //     $('#popupModalBody .imgTop').attr('src', imageUrl);
        //     $('#popupModalBody .modalPopupTitle').text('Missing Recaptcha');
        //     $('#popupModalBody .modalPopupText').text('Please complete the reCAPTCHA.');
        //     $('#popupModalBody .closeBtn').css('display', 'block');
        //     $('#popupModalBody .modalPopupBtn').css('display', 'none');
        //     $('#popupModalBody .closeBtn').css('display', 'none');
        //     $('#popupModal').modal('show');
        // }else{
            var formData = $(this).serialize();
            var formDataParts = formData.split("=");
            mobileValue = formDataParts[1];

            var codeValue = $('#codeID').val();

            $.ajax({
                type: 'POST',
                url: '/rallypaperinvitation/' + codeValue,
                data: formData,
                success: function(response) {
                    var imagePath = '';

                    if (response.globalCode === 1) {
                        imagePath = 'checkGreen.svg';
                    } else if (response.globalCode === 0 && response.flagCode === 2) {
                        imagePath = 'decline.png';
                    } else {
                        imagePath = 'warning.svg';
                    }
                    var imageUrl = '/build/images/' + imagePath;
                    $('#popupModalBody .imgTop').attr('src', imageUrl);

                    if ((response.globalCode === 1 && response.flagCode !=2 ) || (response.globalCode === 0 && response.flagCode ===4)) {
                        $('#popupModalBody .modalPopupTitle').text(response.title);
                        $('#popupModalBody .modalPopupText').text(response.body);
                        $('.qrSection').css('display', 'block');
                        $('#popupModalBody .modalPopupBtn').css('display', 'none');
                        $('#popupModalBody .closeBtn').css('display', 'none');

                    } else if (response.globalCode === 0 && response.flagCode !=4 || (response.globalCode === 1 && response.flagCode === 2)) {
                        $('#popupModalBody .modalPopupTitle').text(response.title);
                        $('#popupModalBody .modalPopupText').text(response.body);
                        $('#popupModalBody .modalPopupBtn').css('display', 'block');
                        $('#popupModalBody .closeBtn').css('display', 'block');
                        $('.qrSection').css('display', 'none');
                        $('#popupModalBody .modalPopupBtn button').text(response.buttonText);
                    }
                    if (response.globalCode === 0 && response.flagCode === 2) {
                        $('#textToCopy').text(window.location.href);
                    }
                    flagCode = response.flagCode;
                    globalCode = response.globalCode;
                    $('#popupModal').modal('show');
                    // grecaptcha.reset();
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        // }
    });

    $(document).on('click', '#popupModalBody .modalPopupBtn button', function() {
        if (flagCode === 5 || (globalCode === 0 && flagCode === 0) || (globalCode === 0 && flagCode === 1)) {
            $('#popupModal').modal('hide');
        }else if (globalCode === 0 && flagCode === 2) {
            $('#textToCopy').css('display', 'none');
        }
        if (globalCode === 1 && flagCode === 2) {
            window.open('https://youtu.be/ccdq3A01Cyw', '_blank');
        }
    });
    $('#popupModal .closeBtn button').on('click', function() {
        $('#popupModal').modal('hide'); // Close the modal
    });

    if(document.getElementsByName("search")[0]){
        const headerHTML = `
        <div class="desktopMode marginForHeader"></div>
        <div class="desktopMode member-number-name"> 
            <div class="member-number-name-left">
                <div> Member #</div>
                <div> Phone Number</div>
                <div> Full Name</div>
            </div>
            <div class="member-number-name-right">
                <div> Status</div>
            </div>
        </div>
    `;
        var search = document.getElementsByName("search")[0]
       
        search.addEventListener("input",()=>{   
            let postObj = { 
                char : search.value
        }
        document.getElementsByClassName("tab-information")[0].innerHTML = '';
    
        let post = JSON.stringify(postObj)
            $.ajax({
                url: '/aub-search',
                type: 'POST',
                data: post,
                success: function(response) {
                    console.log(response);
                    console.log(response.data.length)
                     // Loop through each element with the class 'tab-information'
                let tabInformations = document.getElementsByClassName("tab-information");
                for (let j = 0; j < tabInformations.length; j++) {
                    // Clear the content of each 'tab-information' element before appending new content
                    tabInformations[j].innerHTML = '';
                    if (j === 0) {
                        tabInformations[j].insertAdjacentHTML('beforeend', headerHTML);
                    }
                    // Append content for each entity to the current 'tab-information' element
                    for (let i = 0; i < response.data.length; i++) {
                        let entity = response.data[i];
                         let html = `
                    <div class=" member-number-name greyBackground">
    
                            <div class="member-number-name-left">
                                <div class="fixedWidthMember">${entity.id}</div>
                                <div class="fixedWidthPhone">+${entity.mobileNo}</div>
                                <div class="fixedWidthName">${entity.fullyname}</div>
                            </div>
                            <div class="member-number-name-right st ${entity.class}">
                                <div>${entity.status}</div>
                            </div>
                            </div>
                        `;
                        // Append 'html' to the current 'tab-information' element
                        tabInformations[j].insertAdjacentHTML('beforeend', html);
                    }
                }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        })
    }
});


