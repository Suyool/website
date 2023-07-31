import React, { useEffect, useState } from "react";

const Header = ({ activeButton, setActiveButton, getHeaderTitle, getBackLink }) => {

  const handleButtonClick = (getBackLink) => {
    setActiveButton({ name: getBackLink });
  };

  return (
    <div id="MobileHeader">
      <div className="back" onClick={() => { handleButtonClick(getBackLink) }}><img src="/build/images/Ogero/Back.png" alt="Back" /></div>
      <div className="headerTitle">{getHeaderTitle}</div>
      <div className="empty"></div>
    </div>
  );
};

export default Header;
