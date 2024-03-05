import React, { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";

const BottomSlider = () => {
  const dispatch = useDispatch();

  const { BillPay } = AppAPI();
  const bottomSlider = useSelector((state) => state.appData.bottomSlider);
  const parameters = useSelector((state) => state.appData.parameters);
  const mobileResponse = useSelector((state) => state.appData.mobileResponse);

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

  useEffect(() => {
    if (mobileResponse == "success") {
      BillPay(bottomSlider.data.landDataSlider.id);
    } else if (mobileResponse == "failed") {
      dispatch(settingData({ field: "isloading", value: false }));
      dispatch(settingObjectData({ mainField: "bottomSlider", field: "isButtonDisable", value: false }));
      dispatch(settingData({ field: "mobileResponse", value: "" }));
    }
  }, [mobileResponse]);

  return (
    <div id="BottomSliderContainer">
      <div className="topSection">
        <div className="brBoucket"></div>
        <div className="titles">
          <div className="titleGrid">Payment Confirmation</div>
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
        {bottomSlider?.name == "successLandlineSlider" && (
          <div id={bottomSlider?.name}>
            <div className="cardSec">
              <img src="/build/images/Ogero/OgeroLogo.png" alt="flag" />
              <div className="method">Ogero Landline Bill Payment</div>
            </div>

            <div className="MoreInfo">
              <div className="label">Landline Number</div>
              <div className="value">+961 {bottomSlider.data.landlineMobileSlider}</div>
            </div>

            <div className="br"></div>

            <div className="MoreInfo">
              <div className="label">Amount in L.L</div>
              <div className="value1">L.L {parseInt(bottomSlider.data.landDisplayDataSlider.Amount1).toLocaleString()}</div>
            </div>

            <div className="MoreInfo">
              <div className="label">Fees in L.L</div>
              <div className="value1">L.L {parseInt(bottomSlider.data.landDisplayDataSlider.OgeroFees).toLocaleString()}</div>
            </div>

            <div className="br"></div>

            <div className="MoreInfo">
              <div className="label">Total</div>
              <div className="value2">L.L {parseInt(bottomSlider.data.landDisplayDataSlider.TotalAmount).toLocaleString()}</div>
            </div>
            <div className="footSectionPick">
              <button
                onClick={() => {
                  handleConfirmPay();
                }}
                disabled={bottomSlider?.isButtonDisable}
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
