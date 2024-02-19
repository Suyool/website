import React from "react";

const Header = ({
  parameters,
  activeButton,
  setActiveButton,
  getHeaderTitle,
  getBackLink,
  getSpinnerLoader
}) => {
  const handleButtonClick = (getBackLink) => {
    if (activeButton.name == "") {
      if (parameters?.deviceType === "Android") {
        window.AndroidInterface.callbackHandler("GoToApp");
      } else if (parameters?.deviceType === "Iphone") {
        window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
      }
    }
    setActiveButton({ name: getBackLink });
  };

  return (
    <div id="MobileHeader">
      <div
        className="back"
        onClick={() => {
          handleButtonClick(getBackLink);
        }}
      >
        <img src="/build/images/alfa/Back.png" alt="Back" />
      </div>
      <div className="headerTitle">{getHeaderTitle}</div>
      <div className="empty"></div>
    </div>
  );
};

export default Header;
