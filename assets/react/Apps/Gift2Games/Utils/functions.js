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
