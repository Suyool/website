import React from "react";
import { useDispatch, useSelector } from "react-redux";
import { handleShare } from "../Utils/functions";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";
import {capitalizeFirstLetters} from "../../../functions";

const BottomSlider = () => {
  const dispatch = useDispatch();
  const bottomSlider = useSelector((state) => state.appData.bottomSlider);
  const {bundle, planData, parameters} = useSelector((state) => state.appData);

  const handleConfirmPay = () => {
    dispatch(settingObjectData({ mainField: "bottomSlider", field: "isButtonDisable", value: true }));
    dispatch(settingData({ field: "isloading", value: true }));

    if (parameters?.deviceType === "Android") {
      setTimeout(() => {
        window.AndroidInterface.callbackHandler("message");
      }, 2000);
    } else if (parameters?.deviceType === "Iphone") {
      setTimeout(() => {
        window.webkit.messageHandlers.callbackHandler.postMessage("fingerprint");
      }, 2000);
    }
  };
  return (
    <div id="BottomSliderContainer">
      <div className="topSection">
        <div className="brBoucket"></div>
        <div className="titles">
          <div className="titleGrid"></div>
          <button
            onClick={() => {
              dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: bottomSlider?.backPage }));
              dispatch(settingData({ field: "bottomSlider", value: { isShow: false, name: "", data: {} } }));
            }}
          >
            Cancel
          </button>
        </div>
      </div>

      <div className="bodySection">
        {bottomSlider?.name === "successSlider" && (
          <div id={bottomSlider?.name}>
            <img className="SuccessImg" src="/build/images/alfa/SuccessImg.png" alt="Bundle" />
            <div className="bigTitle">Payment Successful</div>
            <div className="descriptio">
              You have successfully purchased the {capitalizeFirstLetters(bundle)} {planData.plandescription} package.
            </div>

            <div className="br"></div>

            <div className="copyTitle">To recharge your package:</div>
            <div className="copyDesc">Use the credentials below</div>

            <button
              className="copySerialBtn"
              onClick={() => {
                handleShare(bottomSlider?.data?.id, parameters?.deviceType);
              }}
            >
              <div></div>
              <div className="serial">{bottomSlider?.data?.id}</div>
              <img className="copySerial" src="/build/images/alfa/copySerial.png" alt="copySerial" />
            </button>

            <button
                className="copySerialBtn"
                onClick={() => {
                  handleShare(bottomSlider?.data?.password, parameters?.deviceType);
                }}
            >
              <div></div>
              <div className="serial">{bottomSlider?.data?.password}</div>
              <img className="copySerial" src="/build/images/alfa/copySerial.png" alt="copySerial" />
            </button>

          </div>
        )}
      </div>
    </div>
  );
};

export default BottomSlider;
