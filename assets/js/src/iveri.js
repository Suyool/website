// if (document.getElementById("myFormTopUp")) {
//   document.getElementById("myFormTopUp").submit();
// }

if(document.getElementById("topUpButton")){
  document.getElementById("topUpButton").addEventListener("click", function () {
    // Submit the form
    document.getElementById("myFormRequest").submit();
  });
}

if(document.getElementById("submitTopUp")){
  document.getElementById("submitTopUp").addEventListener("click", function () {
    // Submit the form
    document.getElementById("myFormTopUp").submit();
  });
}


if(document.querySelector(".loaderTopUp")){
  const element = document.getElementById("submitTopUp");
  setInterval(function() {element.style.display = "block"}, 2000);
}
