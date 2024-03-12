import React from "react";
import { useDispatch, useSelector } from "react-redux";
import { handleShare } from "../Utils/functions";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const BottomSlider = () => {
  const dispatch = useDispatch();
  const bottomSlider = useSelector((state) => state.appData.bottomSlider);
  const parameters = useSelector((state) => state.appData.parameters);
  const storedData = useSelector((state) => state.appData.StoredData);

  const handleConfirmPay = () => {
    dispatch(
      settingObjectData({
        mainField: "bottomSlider",
        field: "isButtonDisable",
        value: true,
      })
    );
    dispatch(settingData({ field: "isloading", value: true }));

    if (parameters?.deviceType === "Android") {
      setTimeout(() => {
        window.AndroidInterface.callbackHandler("message");
      }, 2000);
    } else if (parameters?.deviceType === "Iphone") {
      setTimeout(() => {
        window.webkit.messageHandlers.callbackHandler.postMessage(
          "fingerprint"
        );
      }, 2000);
    }
  };
  const Topup = () => {
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
          <div className="titleGrid">Payment Confirmation</div>
          <button
            onClick={() => {
              dispatch(
                settingData({
                  field: "bottomSlider",
                  value: { isShow: false, name: "", data: {} },
                })
              );
            }}
          >
            Cancel
          </button>
        </div>
      </div>

      <div className="bodySection">
        {bottomSlider?.name == "SliderTopup" && (
          <div id={bottomSlider?.name}>
            <div className="cardSec">
              <img src="/build/images/windsl/windsl.png" />
              <span>Top Up WinDSL</span>
              <div className="method">
                <div className="bodyToTopup">
                  <div className="flex">
                    <div className="country">WinDSL Username</div>
                    <div className="data">{storedData?.username}</div>
                  </div>

                  <div className="line"></div>
                  <div className="flex">
                    <div className="country">Amount in USD</div>
                    <div className="data">$ {Number(storedData?.amount).toFixed(2)}</div>
                  </div>

                  <div className="line"></div>
                  <div className="flex">
                    <div className="country">Total</div>
                    <div className="data2">$ {Number(storedData?.amount).toFixed(2)}</div>
                  </div>

                  <div className="line"></div>
                </div>
              </div>
            </div>
            <div className="footSectionPick" style={{ color: "#00ADFF" }}>
              <button
                onClick={() => {
                  Topup();
                }}
                disabled={bottomSlider.isButtonDisable}
              >
                Confirm & Pay
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default BottomSlider;
