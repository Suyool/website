import React from "react";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const Header = () => {
  const dispatch = useDispatch();
  const parameters = useSelector((state) => state.appData.parameters);
  const headerData = useSelector((state) => state.appData.headerData);
  const isBottomSlider = useSelector((state) => state.appData.bottomSlider.isShow);
  const isModalData = useSelector((state) => state.appData.modalData.isShow);
  const {productInfo, typeID} = useSelector((state)=>state.appData);

  const handleButtonClick = () => {
    if (headerData.currentPage == "" || (typeID ==7 && Object.keys(productInfo)?.length == 0)) {
      if (parameters?.deviceType === "Android") {
        window.AndroidInterface.callbackHandler("GoToApp");
      } else if (parameters?.deviceType === "Iphone") {
        window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
      }
    }
    if (isBottomSlider) {
      dispatch(settingData({ field: "bottomSlider", value: { isShow: false } }));
    } else if (isModalData) {
      dispatch(settingData({ field: "modalData", value: { isShow: false } }));
    } else {
      dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: headerData.backLink }));
    }
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
