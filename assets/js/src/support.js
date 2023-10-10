$(document).ready(function () {
    // Handle form submission using AJAX
    $('#myForm').submit(function (e) {
        e.preventDefault(); // Prevent the default form submission

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function (response) {
                // Display the Bootstrap modal
                $('#myModal').modal('show');
            },
            error: function () {
                // Handle errors if needed
                console.error('Error occurred during form submission.');
            }
        });
    });
});
