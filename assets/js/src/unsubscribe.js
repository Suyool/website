// Get the query string from the current URL
const queryString = window.location.search;
// Create a new URLSearchParams object from the query string
const queryParams = new URLSearchParams(queryString);

// Get the value of the "uniqueCode" parameter
const uniqueCode = queryParams.get("uniqueCode");

// Get the value of the "Flag" parameter
const flag = queryParams.get("Flag");


// Call the resubscribe function with the uniqueCode and flag values
let btn = document.getElementById("resubscribe");
btn.addEventListener('click', event => {
    resubscribe(uniqueCode,flag);
});

function resubscribe(uniqueCode, flag) {
    jQuery.ajax({
        type: "GET",
        url: '/unsubscribeMarketing/resubscribe?uniqueCode=' + uniqueCode + '&flag=' + flag,
        dataType: 'json',
    });
}


