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

                if (response.globalCode === 1 || (response.globalCode === 0 && response.flagCode ===4)) {
                    $('#popupModalBody .modalPopupTitle').text(response.title);
                    $('#popupModalBody .modalPopupText').text(response.body);
                    $('.qrSection').css('display', 'block');
                    $('#popupModalBody .modalPopupBtn').css('display', 'none');
                    $('#popupModalBody .closeBtn').css('display', 'none');

                } else if (response.globalCode === 0 && response.flagCode !=4) {
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
    $('#popupModalBody .modalPopupBtn').on('click', function() {
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
                    if (response.globalCode === 1 || (response.globalCode === 0 && response.flagCode ===4)) {
                        $('#popupModalBody .modalPopupTitle').text(response.title);
                        $('#popupModalBody .modalPopupText').text(response.body);
                        $('.qrSection').css('display', 'block');
                        $('#popupModalBody .modalPopupBtn').css('display', 'none');
                        $('#popupModalBody .closeBtn').css('display', 'none');

                    } else if (response.globalCode === 0 && response.flagCode !=4) {
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
        }else if (flagCode === 2) {
            var linkToCopy = window.location.href;
            navigator.clipboard.writeText(linkToCopy)
                .then(function() {
                    console.log('Link copied successfully!');
                })
                .catch(function(error) {
                    console.error('Error copying link: ', error);
                });
        }
        if (globalCode === 1 && flagCode === 2) {
            window.location.href = '';
        }
    });
});
