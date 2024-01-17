import React from "react";

const Header = ({
  parameters,
  activeButton,
  setActiveButton,
  getHeaderTitle,
  getBackLink,
}) => {
  const handleButtonClick = (getBackLink) => {
    if (activeButton.name == "") {
      if (parameters?.deviceType === "Android") {
        window.AndroidInterface.callbackHandler("GoToApp");
      } else if (parameters?.deviceType === "Iphone") {
        window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
      }else if(parameters?.deviceType === "CORPORATE"){
        window.parent.postMessage("default", "http://localhost:3000/bills");
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
