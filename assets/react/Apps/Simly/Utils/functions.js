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

export const formatMobileNumber = (value) => {
  const digitsOnly = value.replace(/\D/g, "");
  const truncatedValue = digitsOnly.slice(0, 8);
  if (digitsOnly.length === 0) {
    return "";
  }
  if (truncatedValue[0] !== "undefined" && truncatedValue[0] !== "0" && truncatedValue[0] !== "7" && truncatedValue[0] !== "8") {
    return "0" + truncatedValue;
  }
  if (truncatedValue.length > 3) {
    return truncatedValue.replace(/(\d{2})(\d{3})(\d{3})/, "$1 $2 $3");
  }
  return truncatedValue;
};

export const handleDownload = async (qrString,deviceType) => {
  let object = [
    {
      QR: {
        share: "qr",
        text: qrString,
      },
    },
  ];
  console.log(JSON.stringify(object));
  if (deviceType === "Android") {
    window.AndroidInterface.callbackHandler(JSON.stringify(object));
  } else if (deviceType === "Iphone") {
    window.webkit.messageHandlers.callbackHandler.postMessage(object);
  }
};
