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
     