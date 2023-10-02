
// $(document).ready(function() {
//     // Hide all answers initially
//     $('.category-answers .answer').hide();
//
//     // Get the question param
//     var urlParams = new URLSearchParams(window.location.search);
//     var questionId = urlParams.get('question');
//
//
//     // Show the answer for the first question by default
//     var firstQuestion = $('.category-questions .question:first');
//     var answerId = firstQuestion.data('answer');
//
//     if (questionId){
//         var elementId = '#' + questionId;
//         $(elementId).addClass('active-question');
//
//         $('#answer-' + questionId).show();
//     }else {
//         $('#answer-' + answerId).show();
//
//         // Add a class to the active question
//         firstQuestion.addClass('active-question');
//     }
//
//     // Attach click event to questions
//     $('.category-questions .question').click(function() {
//         // Get the data-answer attribute to identify the answer
//         var answerId = $(this).data('answer');
//
//         // Hide all answers
//         $('.category-answers .answer').hide();
//
//         // Show the selected answer
//         $('#answer-' + answerId).show();
//
//         // Remove the 'active-question' class from all questions
//         $('.category-questions .question').removeClass('active-question');
//
//         // Add the 'active-question' class to the clicked question
//         $(this).addClass('active-question');
//
//         // Get the text of the clicked question
//         var questionText = $(this).text();
//
//         // Put the question text in the span with id "navigator-question"
//         $('#navigator-question').text(questionText);
//         $('#answer-question').text(questionText);
//     });
// });

// //this is the live searching function
$(document).ready(function () {
    $("#search-input").on('input', function () {
        var query = $(this).val();
        if (query){
            $.ajax({
                type: "POST",
                url: "/questions/search",
                data: { query: query },
                success: function (response) {
                    $("#search-results").html(response);
                },
                error: function (error) {
                    console.error(error);
                },
            });
        }
    });
});
