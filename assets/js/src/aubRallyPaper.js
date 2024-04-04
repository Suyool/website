$(document).ready(function() {
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
});
