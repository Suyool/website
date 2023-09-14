if (document.getElementById("myFormTopUp")) {
  document.getElementById("myFormTopUp").submit();
}

document.getElementById("topUpButton").addEventListener("click", function () {
  // Submit the form
  document.getElementById("myFormRequest").submit();
});
