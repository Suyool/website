if(document.getElementById("emailForm")){
// Get the form element
  const form = document.getElementById("emailForm");
          
  // Get the email status element
  const emailStatus = document.getElementById("emailStatus");

  const emailTitle = document.getElementById("emailTitle");

  const emailBtn = document.getElementById("emailBtn");

  // Add an event listener to the form submission
  form.addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent the default form submission

    fetch(form.action, {
      method: form.method,
      body: new FormData(form)
    })
      .then(function(response) {
        return response.json();
      })
      .then(function(data) {
        // Handle the response accordingly (e.g., show success message, update UI, etc.)
        console.log(data);
        if(data.success == "Invalid Email"){
          emailStatus.textContent = "Invalid Email";
          emailTitle.textContent = "Rejected";
          emailBtn.textContent="Cancel";
        }else{
          if (data.success) {
            emailStatus.textContent = "You will be the first one to know once the Suyool app is launched.";
            emailTitle.textContent = "You Are On The Waiting List";
            emailBtn.textContent="Youpi!";
          } else {
            emailStatus.textContent = "Email exist";
            emailTitle.textContent = "Rejected";
            emailBtn.textContent="Cancel";
          }
        }
    

        // Show the modal
        $("#emailModal").modal("show");
      })
      .catch(function(error) {
        // Handle any errors that occurred during form submission
        console.error("Error submitting forms:", error);
      });
  });
}
 
function getMobileOperatingSystem() {
  var userAgent = navigator.userAgent || navigator.vendor || window.opera;

  // Windows Phone must come first because its UA also contains "Android"
  if (/windows phone/i.test(userAgent)) {
    return "Windows Phone";
  }

  if (/Tablet/i.test(userAgent)) {
    return "unknown";
  }

  if (/android/i.test(userAgent)) {
    return "Android";
  }

  // iOS detection from: http://stackoverflow.com/a/9039885/177710
  if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
    return "iOS";
  }

  return "unknown";
}
//Execute Google Analytics and Facebook Pixel events
// function googleFacebookEvents(eventNameKey, eventName, eventCategory, eventLabel){
//     //Set the name of the resolution
//     var eventResolution = '';
//     //If Ipad - Add "Ipad - " to the eventLabel
//     if(window.screen.width > 767 && window.screen.width < 769){
//         eventResolution = 'Ipad - ';
//         //If Mobile - Add "Mobile - " to the eventLabel
//     }else if(window.screen.width < 992){
//         eventResolution = 'Mobile - ';
//         //If Desktop - Add "Desktop - " to the eventLabel
//     }else{
//         eventResolution = 'Desktop - ';
//     }

//     //Google Analytics
//     gtag('event', eventName, {'event_category': eventCategory, 'event_label': eventResolution + eventLabel});
//     //Facebook Pixel
//     fbq('trackCustom', eventCategory, {eventNameKey: eventName});
// }
function getTheApp(position, downloadAppUrl) {
  //Event label - Menu or Homepage - top
  var eventLabel = "";
  //If the downloadAppUrl isset uset it, otherwise set the default value ''
  var downloadAppUrl =
    typeof downloadAppUrl != "undefined" ? downloadAppUrl : "";

  //Call the googleFacebookEvents to execute Google and Facebook events
  // googleFacebookEvents('Action', 'Get The APP', 'App download', eventLabel);

  //If width > 992 - scroll to the bottom section to download the app
  if (window.screen.width > 768) {
    // console.log(window.screen.width );
  } else {
    //Get the Mobile Operating System
    var osMobile = getMobileOperatingSystem();

    //If the Download App URL isset use it
    if (downloadAppUrl != "") {
      location = downloadAppUrl;

      //Otherwise check the OS and call the Apple or Play Store URL
    } else {
      location = DefaultDownloadLink;
      // //If IOS
      // if(osMobile == 'iOS'){
      //     location = iosDownloadLink;
      //
      //     //Otherwise
      // }else{
      //     location = androidDownloadLink;
      // }
    }
  }
}

/** click button once **/
var clicked_once = false;
function clickOne(downloadAppUrl, conversion_code, uri) {
  if (!clicked_once) {
    clicked_once = true;
    console.log("ok");

    $(".conversion-button").css("margin-top", "20px");
    // gtag('event', 'conversion', {'send_to': 'AW-799970313/'+conversion_code});
  } else {
    console.log("already clicked");
  }
  getTheApp(uri, downloadAppUrl);
}

if (document.querySelector(".open-suyool-account")) {
  const open_suyool_account = document.querySelectorAll(".open-suyool-account");
  open_suyool_account.forEach(function (element) {
    element.addEventListener("click", function () {
    clickOne("https://suyoolapp.page.link/app", "", "/");
  });
});
}

if (document.querySelector(".open-suyool-account-rtp")) {
  const open_suyool_account = document.querySelectorAll(".open-suyool-account-rtp");
  open_suyool_account.forEach(function (element) {
    element.addEventListener("click", function () {
    clickOne("https://suyoolapp.page.link/app", "", "/");
  });
});
}

if (document.querySelector(".get-the-card")) {

  const open_suyool_account = document.querySelectorAll(".get-the-card");
  open_suyool_account.forEach(function (element) {
    element.addEventListener("click", function () {
      console.log("requestcard")
    clickOne("https://suyoolapp.page.link/requestcard", "", "/");
  });
});
}


if (document.querySelector(".OpenYourSuyoolAccountCard")) {
  const open_suyool_account = document.querySelectorAll(".OpenYourSuyoolAccountCard");
  open_suyool_account.forEach(function (element) {
    element.addEventListener("click", function () {
      console.log("requestcard")
    clickOne("https://suyoolapp.page.link/requestcard", "", "/");
  });
});
}

if (document.querySelector(".OpenYourSuyoolAccount")) {
  const open_suyool_account = document.querySelectorAll(".OpenYourSuyoolAccount");
  open_suyool_account.forEach(function (element) {
    element.addEventListener("click", function () {
      console.log("requestcard")
    clickOne("https://suyoolapp.page.link/app", "", "/");
  });
});
}

// if (document.querySelector(".download .playstore")) {
//   const open_suyool_account = document.querySelector(".download .playstore");

//   open_suyool_account.addEventListener("click", function () {
//     clickOne("https://suyoolapp.page.link/app", "", "/");
//   });
// }

// if (document.querySelector(".download .appstore")) {
//   const open_suyool_account = document.querySelector(".download .appstore");

//   open_suyool_account.addEventListener("click", function () {
//     clickOne("https://suyoolapp.page.link/app", "", "/");
//   });
// }

document.addEventListener("DOMContentLoaded", function () {
  var openSuyoolAccountButton = document.querySelectorAll("#openSuyoolAccount");
  openSuyoolAccountButton.forEach(function (element) {
    element.addEventListener("click", function () {
    if (/Mobi|Android/i.test(navigator.userAgent)) {
      clickOne("https://suyoolapp.page.link/app", "", "/");
    } else {
      window.location.href = "#qrGetTheApp";
    }
    });
  });
});

if(document.getElementById("chatwithteam")){
// Check if the user is on a mobile device
function isMobileDevice() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

// Get a reference to the link
var salesLink = document.getElementById("chatwithteam");

// Define the mobile and desktop URLs
var mobileUrl = "tel:9611290900"; // Replace with your phone number
var desktopUrl = "/contact-us"; // Replace with your desktop URL

// Update the link's href based on the device
if (isMobileDevice()) {
    salesLink.href = mobileUrl;
} else {
    salesLink.href = desktopUrl;
}
}

if(document.querySelector(".close")){

  var button=document.querySelector(".close");
  button.addEventListener("click",()=>{
    if (navigator.userAgent.match(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i)){
      // alert("hi")
      if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
        window.webkit.messageHandlers.callbackHandler.postMessage(
          "GoToApp"
        );
      } else {
        // alert("hi")
        window.AndroidInterface.callbackHandler("GoToApp");
      }
                
    }else{
      document.location.href="/";
    }
  });

}
//Execute Google Analytics and Facebook Pixel events
function googleFacebookEvents(eventCategory, eventName,  position){
    //Google Analytics
    gtag('event', eventName, {'event_category': eventCategory, 'event_label': position});
    //Facebook Pixel
    // fbq('trackCustom', eventCategory, {eventNameKey: eventName});
}


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

$(document).ready(function () {
    document.getElementById("amount").addEventListener("keyup", function() {
        var nameInput = document.getElementById('amount').value;
        if (nameInput != "") {
            document.getElementById('convertButton').removeAttribute("disabled");
        } else {
            document.getElementById('convertButton').setAttribute("disabled", null);
        }
    });
    function updateToCurrency() {
        var fromCurrency = $('#fromCurrency').val();
        var toCurrency = $('#toCurrency');

        if (fromCurrency === "LBP") {
            toCurrency.val("USD");
        } else if (fromCurrency === "USD") {
            toCurrency.val("LBP");
        }
    }

    function updateFromCurrency() {
        var fromCurrency = $('#fromCurrency');
        var toCurrency = $('#toCurrency').val();

        if (toCurrency === "LBP") {
            fromCurrency.val("USD");
        } else if (toCurrency === "USD") {
            fromCurrency.val("LBP");
        }
    }

    function changeCurrencySymbol() {
        var fromCurrency = $('#fromCurrency').val();
        var toCurrency = $('#toCurrency').val();
        var currencySymbol = $('#currencySymbol');

        if ((fromCurrency === "LBP" && toCurrency === "USD")) {
            currencySymbol.text("L.L");
        } else if (fromCurrency === "USD" && toCurrency === "LBP") {
            currencySymbol.text("$");
        }
    }

    function swapCurrencies() {
        var fromCurrency = $('#fromCurrency');
        var toCurrency = $('#toCurrency');

        // Swap the selected values
        var temp = fromCurrency.val();
        fromCurrency.val(toCurrency.val());
        toCurrency.val(temp);

        // Update currency symbol after swapping
        changeCurrencySymbol();
    }

    function convert() {
        var buyRateText = $('#buyAmountDesVal').text().trim();
        var sellRateText = $('#sellAmountDesVal').text().trim();

        // Remove non-numeric characters except for the dot (.)
        var buyRate = parseFloat(buyRateText.replace(/[^\d.]/g, ''));
        var sellRate = parseFloat(sellRateText.replace(/[^\d.]/g, ''));
        var amount = parseFloat($('#amount').val());
        var fromCurrency = $('#fromCurrency').val();
        var toCurrency = $('#toCurrency').val();
        var buyAmountElement = $('#buyAmount');
        var sellAmountElement = $('#sellAmount');
        var currencylbp = $('.currencyConvertedLBP');
        var currencyusd = $('.currencyConvertedUSD');


        var buyAmount, sellAmount, currency;

        if (fromCurrency === 'USD' && toCurrency === 'LBP') {
            buyAmount = (amount * buyRate).toFixed(0);
            sellAmount = (amount * sellRate).toFixed( 0);
            currency = 'LBP';
        } else {
            buyAmount = (amount / buyRate).toFixed(2);
            sellAmount = (amount / sellRate).toFixed(2);
            currency = 'USD';
        }

        $('.currencyConverted').text(currency);
        buyAmountElement.text(numberWithCommas(buyAmount));
        buyAmountElement.css("color","#376c92");
        sellAmountElement.css("color","#376c92");
        buyAmountElement.css("font-weight","bolder");
        sellAmountElement.css("font-weight","bolder");
        currencylbp.css("font-weight","bolder");
        currencylbp.css("color","#376c92");
        currencyusd.css("font-weight","bolder");
        currencyusd.css("color","#376c92");
        sellAmountElement.text(numberWithCommas(sellAmount));
    }

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Call the initial functions
    updateToCurrency();
    updateFromCurrency();
    changeCurrencySymbol();

   
    // Attach event handlers using jQuery
    $('#exchangeIcon').click(function () {
        swapCurrencies();
    });

    $('#fromCurrency').change(function () {
        updateToCurrency();
        changeCurrencySymbol();
    });

    $('#toCurrency').change(function () {
        updateFromCurrency();
        changeCurrencySymbol();
    });

    $('#convertButton').click(function () {
        convert();
    });
    // Format the amount input on input change
    // $('#amount').on('input', function () {
    //     var value = $(this).val().replace(/[^\d.]/g, ''); // Remove non-numeric characters except for dot (.)
    //     $(this).val(numberWithCommas(value)); // Format the value with commas
    // });
});


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
if (document.querySelector(".copy-to-clipboard")) {
  // Get the element with the 'copy-to-clipboard' class
  const copyBtn = document.querySelector(".copy-to-clipboard");

  // Get the value of the 'data-to-copy' attribute
  const copyText = copyBtn.getAttribute("data-to-copy");

  // Add a click event listener to the button
  copyBtn.addEventListener("click", function () {
    // Create a new textarea element to hold the copied text
    const textarea = document.createElement("textarea");
    textarea.value = copyText;
    document.body.appendChild(textarea);

    // Select the text in the textarea and copy it to the clipboard
    textarea.select();
    document.execCommand("copy");

    // Remove the textarea element from the DOM
    document.body.removeChild(textarea);

    // Show a success message to the user
    //   alert('Copied to clipboard: ' + copyText);
  });
}

if (document.querySelector(".generate-code")) {
  document
    .querySelector(".generate-code")
    .addEventListener("click", function () {
      const tag = document.querySelector(".generate-code");
      if (tag.hasAttribute("data-code")) {
        window.location.href =
          "/codeGenerated?codeATM=" + tag.getAttribute("data-code");
      } else {
        if (document.querySelector(".error")) {
          document.querySelector(".error").style.display = "block";
        }
      }
    });
}

// if(document.getElementById('formDetails')){
//   const form =  document.getElementById('formDetails')
//   form.addEventListener("submit",function(event){
//     event.preventDefault(); // Prevent the default form submission
//     const btn= document.getElementById("submit");
//     btn.disabled = true;

//   })

// }
if (document.getElementById("submit")) {
  var submitButton = document.getElementById("submit");
  submitButton.addEventListener("click", function () {
    setTimeout(function () {
      submitButton.disabled = true;
    }, 1); // Delay in milliseconds (adjust as needed)
  });
}

if(document.getElementById("contactusForm")){
      const form = document.getElementById("contactusForm");
      form.addEventListener("submit", function(event) {
        event.preventDefault();
    
        fetch(form.action, {
          method: form.method,
          body: new FormData(form)
        })
          .then(function(response) {
            return response.json();
          })
          .then(function(data) {
            console.log(data);
            // if(data.success == "Invalid Email"){
            //   emailStatus.textContent = "Invalid Email";
            //   emailTitle.textContent = "Rejected";
            //   emailBtn.textContent="Cancel";
            // }else{
            //   if (data.success) {
            //     emailStatus.textContent = "You will be the first one to know once the Suyool app is launched.";
            //     emailTitle.textContent = "You Are On The Waiting List";
            //     emailBtn.textContent="Youpi!";
            //   } else {
            //     emailStatus.textContent = "Email exist";
            //     emailTitle.textContent = "Rejected";
            //     emailBtn.textContent="Cancel";
            //   }
            // }
            $("#myModal").modal("show")
          })
          .catch(function(error) {
            console.error("Error submitting forms:", error);
          });
      });
    }
     
if(document.getElementById("termsPdfDownloadButton")){
    document.getElementById("termsPdfDownloadButton").addEventListener("click", function () {
        // Redirect to the Symfony route that triggers the download
        // window.location.href = "{{ path('download_pdf') }}";
        window.location.href = "/download-pdf";
    });
}

// if (document.querySelector(".loaderTopUp")) {
//   setInterval(function () {
//     document.getElementById("submitTopUp").style.display = "block";
//   }, 2000);
// }
// if (document.querySelector(".loaderTopUp")) {
//   const element = document.getElementById("submitTopUp");
//   setInterval(function () {
//     var button = document.querySelector('.continueBtn');
//     element.style.display = "block";
//     // button.click();
//   }, 2000);
// }

if (document.querySelector(".continueBtn")) {
  const element = document.querySelector(".continueBtn");
  var loader = document.getElementsByClassName("loaderTopUp2")[0];
  element.addEventListener("click", function () {
    loader.style.display="none";
    document.getElementById("embed-target").style.display="block";
  });
}

if (document.querySelector(".continueBtnrtp")) {
  const element = document.querySelector(".continueBtnrtp");
  var loader = document.getElementsByClassName("loaderTopUp2")[0];
  element.addEventListener("click", function () {
    loader.style.display="none";
    document.getElementById("embed-target").style.display="block";
  });
}

if (document.querySelector(".actionAppSuccess")) {
  const actionAppSuccess = document.querySelector(".actionAppSuccess");
  actionAppSuccess.addEventListener("click", function () {
    if (navigator.userAgent.match(/Android/i)) {
      window.AndroidInterface.callbackHandler("topupSuccess");
    } else {
      window.webkit.messageHandlers.callbackHandler.postMessage("topupSuccess");
    }
  });
}

if (document.querySelector(".actionAppFailed")) {
  const actionAppFailed = document.querySelector(".actionAppFailed");
  actionAppFailed.addEventListener("click", function () {
    if (navigator.userAgent.match(/Android/i)) {
      window.AndroidInterface.callbackHandler("topupFailed");
    } else {
      window.webkit.messageHandlers.callbackHandler.postMessage("topupFailed");
    }
  });
}


if (document.querySelector(".back")) {
  const action = document.querySelector(".back");
  action.addEventListener("click", function () {
    // Submit the form
    if (navigator.userAgent.match(/Android/i)) {
      window.AndroidInterface.callbackHandler("GoToApp");
    } else {
      window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
    }
  });
}
if (document.querySelector(".actionBrowser")) {
  const action = document.querySelector(".actionBrowser");
  action.addEventListener("click", function () {
    // Submit the form
    const myCookieValue = getCookie('card_payment_url');

    window.location.href = decodeURIComponent(myCookieValue)
  });
}
// Function to get a specific cookie value by name
function getCookie(cookieName) {
  // Split all cookies into an array
  const cookies = document.cookie.split(';');

  // Loop through each cookie to find the one with the provided name
  for (let i = 0; i < cookies.length; i++) {
    let cookie = cookies[i].trim();

    // Check if this is the cookie you're looking for
    if (cookie.startsWith(cookieName + '=')) {
      // Extract and return the cookie value
      return cookie.substring(cookieName.length + 1);
    }
  }

  // Return null if the cookie is not found
  return null;
}

// Usage example



$(document).ready(function() {
  $('#resubscribe').on('click', function() {
    var uniqueCode = $(this).data('code');
    var flag = $(this).data('flag');
    resubscribe(uniqueCode, flag);
  });
});

function resubscribe(uniqueCode, flag) {
  jQuery.ajax({
    type: "GET",
    url: "/unsubscribeMarketing/resubscribe?uniqueCode=" + uniqueCode + "&flag=" + flag,
    dataType: "json",
  });
}

