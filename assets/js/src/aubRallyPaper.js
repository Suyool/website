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
        var formData = $(this).serialize();
        var formDataParts = formData.split("=");
        mobileValue = formDataParts[1];

        var codeValue = $('#codeID').val();

        $.ajax({
            type: 'POST',
            url: '/rallypaperinvitation/' + codeValue,
            data: formData,
            success: function(response) {
                var imagePath = response.globalCode === 1 ? 'checkGreen.svg' : 'warning.svg';
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
                flagCode = response.flagCode;
                globalCode = response.globalCode;
                $('#popupModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });
    $(document).on('click', '#popupModalBody .modalPopupBtn button', function() {
        if (flagCode === 5) {
            var codeValue = $('#codeID').val();
            $.ajax({
                type: 'POST',
                url: '/rallypaperinvitation/' + codeValue,
                data: {
                    mobile: mobileValue,
                    switch: 1
                },
                success: function(response) {
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
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }else if (globalCode === 0 && flagCode === 2) {
            console.log('Copying link:', window.location.href);
            const tempInput = document.createElement("input");
            tempInput.value = window.location.href;
            document.body.appendChild(tempInput);
            tempInput.select();
            const copySuccess = document.execCommand("copy");
            document.body.removeChild(tempInput);
            console.log('Link copied successfully:', copySuccess);
        }
        if (globalCode === 1 && flagCode === 2) {
            window.open('https://youtu.be/ccdq3A01Cyw', '_blank');
        }
    });
    $('#popupModal .closeBtn button').on('click', function() {
        $('#popupModal').modal('hide'); // Close the modal
    });
});
