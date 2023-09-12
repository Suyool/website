function resubscribe(uniqueCode, flag) {
  jQuery.ajax({
    type: "GET",
    url: "/unsubscribeMarketing/resubscribe?uniqueCode=" + uniqueCode + "&flag=" + flag,
    dataType: "json",
  });
}

