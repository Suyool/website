$(".submitTextDownloadAppBtn").on("click", function(e) {
  // console.log( $(this).attr('code'));
  e.preventDefault();

  // Get the mobile number from the input field
  var mobileNumber = $(this).closest(".input-main-cont").find(".mobileTextDownloadApp").val();
  // Send an AJAX request to the server
  $.ajax({
    url: "/invitationCard/submitInvitationCard",
    method: "POST",
    data: { mobileNumber: mobileNumber,
      code: $(this).attr("code") },
    success: function(response) {
      // Show the response message in the modal
      $("#invitationModal .modal-body").append("<p>" + response.RespDesc + "</p>");
      // Modify the button label based on the RespCode value
      if(response.RespCode == 0 || response.RespCode == -3) {
        $("#closeModelResubscribe").text("OK");
      } else {
        $("#closeModelResubscribe").text("Try Again");
      }
      // Show the modal
      $("#invitationModal").modal("show");
    },
    error: function(xhr, status, error) {
      // Show an error message in the console
      console.error(error);
    }
  });
});

$(document).ready(function() {
  // Add an oninput event listener to the #mobile element
  $(".mobileTextDownloadApp").on("input", function(event) {
    //Parent division of the mobile input
    var parentDivision = $(this).closest(".input-main-cont").parent().attr("class");

    //Main division of the mobile input
    var mainDivision = "";
    //Check if the parent division is skash-more-than-cash-section - home-v3
    if(parentDivision.indexOf("invitation-card-section-bottom") > 0){
      //Main division of the form
      mainDivision = ".invitation-card-section-bottom";

      //Check if the parent division is invitation-card-section-top - invitation-card
    } else if(parentDivision.indexOf("invitation-card-section-top") > 0){
      //Main division of the form
      mainDivision = ".invitation-card-section-top";
    }

    //When entering the mobile number enable the submit button if the input length is > 7
    if($(this).val().length > 6){
      $(mainDivision+" .submitTextDownloadAppBtn").removeAttr("disabled");
    } else {
      $(mainDivision+" .submitTextDownloadAppBtn").attr("disabled","disabled");
    }
  });
});