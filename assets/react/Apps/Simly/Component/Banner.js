import React from "react";
import { useDispatch, useSelector } from "react-redux";
import { settingObjectData } from "../Redux/Slices/AppSlice";

const Banner = ({ havingCard }) => {
  const headerData = useSelector((state) => state.appData.headerData);
  const parameters = useSelector((state) => state.appData.parameters);
  const dispatch = useDispatch();

  const requestCard = () => {
    let object = [
      {
        exchange: {
          flag: 93,
          url: "",
        },
      },
    ];
    if (parameters?.deviceType === "Android") {
      window.AndroidInterface.callbackHandler(JSON.stringify(object));
    } else if (parameters?.deviceType === "Iphone") {
      window.webkit.messageHandlers.callbackHandler.postMessage(object);
    }
  };

  const activateEsim = () => {
    dispatch(
      settingObjectData({
        mainField: "headerData",
        field: "currentPage",
        value: "Offers",
      })
    );
  };

  return (
    <div className="banner" style={{ textAlign: "center", display: "none" }}>
      {havingCard ? (
        <img
          src="build/images/simly/activate1.png"
          alt="Activate"
          style={{ cursor: "pointer", width: "100%" }}
          onClick={() => activateEsim()}
        />
      ) : (
        <img
          src="build/images/simly/request1.png"
          alt="Request"
          onClick={() => requestCard()}
          style={{ cursor: "pointer", width: "100%" }}
        />
      )}
    </div>
  );
};
export default Banner;
