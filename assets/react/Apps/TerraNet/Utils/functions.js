export const handleShare = (shareCode, deviceType) => {
  let object = [
    {
      Share: {
        share: "share",
        text: shareCode,
      },
    },
  ];
  if (deviceType === "Android") {
    window.AndroidInterface.callbackHandler(JSON.stringify(object));
  } else if (deviceType === "Iphone") {
    window.webkit.messageHandlers.callbackHandler.postMessage(object);
  }
};

export const formatPhoneNumber = (number) => {
  // If the number is 6 digits and starts with 3, add 0 at the beginning
  if (number.length === 7 && number.startsWith("3")) {
    return "0" + number;
  }

  // If the number is greater than 8 digits and starts with 961, remove the 961
  if (number.length > 8 && number.startsWith("961")) {
    // Remove 961
    number = number.substring(3);

    // If it starts with 3, add 0 at the beginning
    if (number.startsWith("3")) {
      number = "0" + number;
    }
  }
  localStorage.setItem(
      "UserAccount",
      number
  );
  localStorage.setItem(
      "Type",
      "Landline"
  );
  return number;
};