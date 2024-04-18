$(document).ready(function () {
  var flagCode;
  var globalCode;
  var mobileValue;
  localStorage.setItem("status", "pending");
  currentStatus = localStorage.getItem("status");
  // callingpagination();
  //   console.log("current status outside", currentStatus);
  $("#loginForm").on("submit", function (e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
      url: "/aub-login",
      type: "POST",
      data: formData,
      success: function (response) {
        console.log(response);
        if (response.flagCode === 2) {
          $("#loginError").text("Invalid credentials. Please try again.");
        } else {
          window.location.href = "/aub-rally-paper";
        }
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
        $("#loginError").text("An error has occurred.");
      },
    });
  });

  $("#inviteForm").on("submit", function (e) {
    e.preventDefault();
    var recaptchaResponse = grecaptcha.getResponse();
    if (recaptchaResponse.length == 0) {
      var imageUrl = "/build/images/decline.png";
      $("#popupModalBody .imgTop").attr("src", imageUrl);
      $("#popupModalBody .modalPopupTitle").text("Missing Recaptcha");
      $("#popupModalBody .modalPopupText").text(
        "Please complete the reCAPTCHA."
      );
      $("#popupModalBody .closeBtn").css("display", "block");
      $("#popupModalBody .modalPopupBtn").css("display", "none");
      $("#popupModalBody .closeBtn").css("display", "none");
      $("#popupModal").modal("show");
    } else {
      var formData = $(this).serialize();
      var formDataParts = formData.split("=");
      mobileValue = formDataParts[1];

      var codeValue = $("#codeID").val();

      $.ajax({
        type: "POST",
        url: "/rallypaperinvitation/" + codeValue,
        data: formData,
        success: function (response) {
          var imagePath = "";

          if (response.globalCode === 1) {
            imagePath = "checkGreen.svg";
          } else if (response.globalCode === 0 && response.flagCode === 2) {
            imagePath = "decline.png";
          } else {
            imagePath = "warning.svg";
          }
          var imageUrl = "/build/images/" + imagePath;
          $("#popupModalBody .imgTop").attr("src", imageUrl);

          if (
            (response.globalCode === 1 && response.flagCode != 2) ||
            (response.globalCode === 0 && response.flagCode === 4)
          ) {
            $("#popupModalBody .modalPopupTitle").text(response.title);
            $("#popupModalBody .modalPopupText").text(response.body);
            $(".qrSection").css("display", "block");
            $("#popupModalBody .modalPopupBtn").css("display", "none");
            $("#popupModalBody .closeBtn").css("display", "none");
          } else if (
            (response.globalCode === 0 && response.flagCode != 4) ||
            (response.globalCode === 1 && response.flagCode === 2)
          ) {
            $("#popupModalBody .modalPopupTitle").text(response.title);
            $("#popupModalBody .modalPopupText").text(response.body);
            $("#popupModalBody .modalPopupBtn").css("display", "block");
            $("#popupModalBody .closeBtn").css("display", "block");
            $(".qrSection").css("display", "none");
            $("#popupModalBody .modalPopupBtn button").text(
              response.buttonText
            );
          }
          if (response.globalCode === 0 && response.flagCode === 2) {
            $("#textToCopy").text(window.location.href);
          }
          flagCode = response.flagCode;
          globalCode = response.globalCode;
          $("#popupModal").modal("show");
          grecaptcha.reset();
        },
        error: function (xhr, status, error) {
          console.error(error);
        },
      });
    }
  });

  $(document).on("click", "#popupModalBody .modalPopupBtn button", function () {
    if (
      flagCode === 5 ||
      (globalCode === 0 && flagCode === 0) ||
      (globalCode === 0 && flagCode === 1)
    ) {
      $("#popupModal").modal("hide");
    } else if (globalCode === 0 && flagCode === 2) {
      $("#textToCopy").css("display", "none");
    }
    if (globalCode === 1 && flagCode === 2) {
      window.open("https://youtu.be/ccdq3A01Cyw", "_blank");
    }
  });
  $("#popupModal .closeBtn button").on("click", function () {
    $("#popupModal").modal("hide"); // Close the modal
  });
  if (document.getElementsByName("search")[0]) {
    const headerHTML = `
        <div class="desktopMode marginForHeader"></div>
        <div class="desktopMode member-number-name"> 
            <div class="member-number-name-left">
                <div> Member #</div>
                <div> Phone Number</div>
                <div> Full Name</div>
            </div>
            <div class="member-number-name-right">
                <div> Status</div>
            </div>
        </div>
        <div class="desktopMode marginForHeader"></div>
        <input type='hidden' id='current_page'/>
				<input type='hidden' id='show_per_page'/>
        
    `;
    var search = document.getElementsByName("search")[0];

    search.addEventListener("input", () => {
      let postObj = {
        char: search.value,
        status: localStorage.getItem("status"),
      };

      let post = JSON.stringify(postObj);
      $.ajax({
        url: "/aub-search",
        type: "POST",
        data: post,
        success: function (response) {

          let tabInformations = document.getElementById("pagingBox");
          tabInformations.innerHTML = "";


          if (search.value == "") {
            currentStatus = localStorage.getItem("status");
            console.log("current status in search", currentStatus);

            handleChangeStatus(currentStatus);
            return 0;
          }
          for (let i = 0; i < response.data.length; i++) {
            let entity = response.data[i];
            let html = `
                    <div class=" member-number-name greyBackground">
    
                            <div class="member-number-name-left">
                                <div class="fixedWidthMember">${entity.id}</div>
                                <div class="fixedWidthPhone">+${entity.mobileNo}</div>
                                <div class="fixedWidthName">${entity.fullyname}</div>
                            </div>
                            <div class="member-number-name-right st ${entity.class}">
                                <div>${entity.status}</div>
                            </div>
                            </div>
                        `;
            tabInformations.innerHTML += html;
          }

        },
        error: function (xhr, status, error) {
          console.error(xhr.responseText);
        },
      });
    });
  }
  handleStatus();
  callingpagination();
});

function handleStatus(currentStatus = null) {
  $(".theborder").on("click", function () {
    $(".theborder").removeAttr("id");

    $(this).attr("id", "active-tab");

    if (document.getElementsByClassName("theborder")[3].hasAttribute("id")) {
      document.getElementsByClassName("tab-information")[0].style.borderRadius =
        "0px 0px 43px 43px";
    }

    const headerHTML = `
        <div class="desktopMode marginForHeader"></div>
        <div class="desktopMode member-number-name"> 
            <div class="member-number-name-left">
                <div> Member #</div>
                <div> Phone Number</div>
                <div> Full Name</div>
            </div>
            <div class="member-number-name-right">
                <div> Status</div>
            </div>
        </div>
        <div class="marginForHeader"></div>
        
    `;

    let status = $(this).find(".info1").attr("id");
    localStorage.setItem("status", status);

    $.ajax({
      url: `/aub-rally-paper?status=${status}`,
      type: "GET",
      success: function (response) {
        rowlength = response?.response?.toBeDisplayed2?.length;
        console.log("ELIOOOOO", response?.response?.toBeDisplayed2);
        let tabInformations = document.getElementById("pagingBox");

        let childrenDivs = tabInformations?.children.length;
        tabInformations.innerHTML = "";

        let countText = $("#active-tab .count").text().trim();
        console.log("--------------------------------", countText);

        for (let i = 0; i < rowlength; i++) {
          let entity = response?.response?.toBeDisplayed2[i];
          let html = `
          <div class="member-number-name">
                        <div class="member-number-name-left">
                            <div class="fixedWidthMember">${entity.id}</div>
                            <div class="fixedWidthPhone">+${entity.mobileNo}</div>
                            <div class="fixedWidthName">${entity.fullyname}</div>
                        </div>
                        <div class="member-number-name-right st ${entity.class}">
                            <div>${entity.status}</div>
                        </div>
                        </div>
                `;
          tabInformations.innerHTML += html; 
        }
        localStorage.setItem("count", response.response.count[status]);
        callingpagination(response.response.count[status]);
      },

      error: function (xhr, status, error) {
        console.error(xhr.responseText);
      },
    });
  });
}

function handleChangeStatus(currentStatus) {
  console.log("inside change", currentStatus);
  let childrenDivs;
  if (document.getElementsByClassName("theborder")[3].hasAttribute("id")) {
    document.getElementsByClassName("tab-information")[0].style.borderRadius =
      "0px 0px 43px 43px";
  }
  var normal = document.querySelectorAll("#normal");
  normal.forEach((n) => {
    n.style.display = "none";
  });
  const headerHTML = `
          <div class="desktopMode marginForHeader"></div>
           
              <div class="member-number-name-left">
                  <div> Member #</div>
                  <div> Phone Number</div>
                  <div> Full Name</div>
              </div>
              <div class="member-number-name-right">
                  <div> Status</div>
              </div>
          
          <div class="marginForHeader"></div>
          
      `;

  $.ajax({
    url: `/aub-rally-paper?status=${currentStatus}`,
    type: "GET",
    success: function (response) {
      rowlength = response?.response?.toBeDisplayed2?.length;
      console.log("row length again -----------------", rowlength);
      let tabInformations = document.getElementById("pagingBox");
 
      if (tabInformations.children.length === 0) {
        console.warn("Element with id 'pagingBox' has no children.");
        let html = "";
        for (let i = 0; i < rowlength; i++) {
          let entity = response?.response?.toBeDisplayed2[i];
          html += `
                        <div class=" member-number-name">
                            <div class="member-number-name-left">
                                <div class="fixedWidthMember">${entity.id}</div>
                                <div class="fixedWidthPhone">+${entity.mobileNo}</div>
                                <div class="fixedWidthName">${entity.fullyname}</div>
                            </div>
                            <div class="member-number-name-right st ${entity.class}">
                                <div>${entity.status}</div>
                            </div>
                            </div>
                    `;

        }
        html += "<div id='page_navigation'></div>";
        tabInformations.innerHTML = html;
        // callingpagination(localStorage.getItem("count"));
        return 0; 
      }
      for (let j = 0; j < rowlength; j++) {
        tabInformations.children[j].innerHTML = "";

        if (j === 0) {
          tabInformations.children[j].insertAdjacentHTML(
            "beforeend",
            headerHTML
          );
          tabInformations[j].innerHTML = "";
        }
      }
      for (let i = 0; i < rowlength; i++) {
        let entity = response?.response?.toBeDisplayed2[i];
        let html = `
                      <div class=" member-number-name">
                          <div class="member-number-name-left">
                              <div class="fixedWidthMember">${entity.id}</div>
                              <div class="fixedWidthPhone">+${entity.mobileNo}</div>
                              <div class="fixedWidthName">${entity.fullyname}</div>
                          </div>
                          <div class="member-number-name-right st ${entity.class}">
                              <div>${entity.status}</div>
                          </div>
                      </div>
                  `;

        tabInformations.children[i].insertAdjacentHTML("beforeend", html);
      }
      callingpagination(response.count[currentStatus]);
    },
    error: function (xhr, status, error) {
      console.error(xhr.responseText);
    },
  });
}

function callingpagination(number_of_items = null) {
  var show_per_page = 15;

  if (number_of_items == null) {
    var number_of_items = $("#pagingBox").children().length;
  }

  var number_of_pages = Math.ceil(number_of_items / show_per_page);

  $("#current_page").val(0);
  $("#show_per_page").val(show_per_page);
  console.log("number of items", number_of_items);
  console.log("number of pages", number_of_pages);

  var navigation_html =
    '<a class="previous_link astylefixing" href="javascript:previous();" >Prev</a>';
  var current_link = 0;
  while (number_of_pages > current_link) {
    navigation_html +=
      '<a class="page_link astylefixing" href="javascript:gotopage(' +
      current_link +
      ')" longdesc="' +
      current_link +
      '">' +
      (current_link + 1) +
      "</a>";
    current_link++;
  }
  navigation_html +=
    '<a class="next_link astylefixing" href="javascript:next();"  >Next</a>';
  $("#page_navigation").html(navigation_html);

  $("#page_navigation .page_link:first").addClass("active_page");

  $("#pagingBox").children().css("display", "none");

  //and show the first n (show_per_page) elements
  $("#pagingBox").children().slice(0, show_per_page).css("display", "flex");
}
//Pagination JS
