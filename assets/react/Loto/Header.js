import React, { useEffect, useState } from "react";
const Header = ({
  activeButton,
  setActiveButton,
  getHeaderTitle,
  getBackLink,
  setBackLink
}) => {
  if (activeButton.name == "LLDJ") {
  }

  const handleButtonClick = (getBackLink) => {
    if (activeButton.name == "LLDJ") {
         setBackLink(window.webkit.messageHandlers.callbackHandler.postMessage(
      "GoToApp"
    ));
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
        <img src="/build/images/Loto/Back.png" alt="Back" />
      </div>
      <div className="headerTitle">{getHeaderTitle}</div>
      <div className="empty"></div>
    </div>
  );
};
export default Header;
