$(document).ready(function() {
  $('#resubscribe').on('click', function() {
    var uniqueCode = $(this).data('uniqueCode');
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

