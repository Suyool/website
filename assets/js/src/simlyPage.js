$(document).ready(function () {
  const searchInput = $("#searchInput");
  const countries = $(".countriesCont");
  const filterInput = $("#filterInput");

  searchInput.on("input", function () {
    const searchTerm = $(this).val().toLowerCase();
    countries.each(function () {
      const countryName = $(this).find(".countryName").text().toLowerCase();
      if (countryName.includes(searchTerm)) {
        $(this).css("display", "block");
      } else {
        $(this).css("display", "none");
      }
    });
  });

  // filterInput.on("change", function () {
  //   const selectedRegion = filterInput.val();

  //   $.ajax({
  //     type: "POST",
  //     url: "/global-esim",
  //     data: { region: selectedRegion },
  //     success: function (response) {
  //       let html = "";

  //       response.forEach(function (country) {
  //         html += '<div class="countriesCont">';
  //         html +=
  //           '<div class="countryImgCont"><img src="' +
  //           country.countryImageURL +
  //           '" alt="' +
  //           country.name +
  //           '"/></div>';
  //         html += '<div><p class="countryName">' + country.name + "</p></div>";
  //         html += "</div>";
  //       });
  //       $(".flagsbyregion").html(html);
  //     },
  //     error: function () {
  //       console.log("Error fetching filtered countries.");
  //     },
  //   });
  // });
});
