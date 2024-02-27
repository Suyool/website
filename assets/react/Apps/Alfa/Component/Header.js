import React from "react";
import { useDispatch, useSelector } from "react-redux";
import { settingObjectData } from "../Redux/Slices/AppSlice";

const Header = () => {
  const dispatch = useDispatch();
  const parameters = useSelector((state) => state.appData.parameters);
  const headerData = useSelector((state) => state.appData.headerData);

  const handleButtonClick = () => {
    if (headerData.currentPage == "") {
      if (parameters?.deviceType === "Android") {
        window.AndroidInterface.callbackHandler("GoToApp");
      } else if (parameters?.deviceType === "Iphone") {
        window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
      }
    }
    dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: headerData.backLink }));
  };

  return (
    <div id="MobileHeader">
      <div
        className="back"
        onClick={() => {
          handleButtonClick();
        }}
      >
        <img src="/build/images/alfa/Back.png" alt="Back" />
      </div>
      <div className="headerTitle">{headerData.title}</div>
      <div className="empty"></div>
    </div>
  );
};

export default Header;
