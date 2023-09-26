$(document).ready(function() {
    // Hide all answers initially
    $('.category-answers .answer').hide();

    // Show the answer for the first question by default
    var firstQuestion = $('.category-questions .question:first');
    var answerId = firstQuestion.data('answer');
    $('#answer-' + answerId).show();

    // Add a class to the active question
    firstQuestion.addClass('active-question');

    // Attach click event to questions
    $('.category-questions .question').click(function() {
        // Get the data-answer attribute to identify the answer
        var answerId = $(this).data('answer');

        // Hide all answers
        $('.category-answers .answer').hide();

        // Show the selected answer
        $('#answer-' + answerId).show();

        // Remove the 'active-question' class from all questions
        $('.category-questions .question').removeClass('active-question');

        // Add the 'active-question' class to the clicked question
        $(this).addClass('active-question');
    });
});