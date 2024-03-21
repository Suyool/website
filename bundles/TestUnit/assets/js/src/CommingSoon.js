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
 