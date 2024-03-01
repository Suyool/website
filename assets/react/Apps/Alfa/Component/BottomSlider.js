import React from "react";
import { useDispatch, useSelector } from "react-redux";
import { handleShare } from "../Utils/functions";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const BottomSlider = () => {
  const dispatch = useDispatch();
  const bottomSlider = useSelector((state) => state.appData.bottomSlider);
  const parameters = useSelector((state) => state.appData.parameters);

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
        {bottomSlider?.name == "successPrepaidSlider" && (
          <div id={bottomSlider?.name}>
            <img className="SuccessImg" src="/build/images/alfa/SuccessImg.png" alt="Bundle" />
            <div className="bigTitle">Payment Successful</div>
            <div className="descriptio">You have successfully purchased the ${bottomSlider?.data?.priceUSD} Alfa recharge card.</div>

            <div className="br"></div>

            <div className="copyTitle">To recharge your prepaid number: </div>
            <div className="copyDesc">Copy the 14-digit secret code below</div>

            <button
              className="copySerialBtn"
              onClick={() => {
                handleShare(bottomSlider?.data?.voucherCodeClipboard, parameters?.deviceType);
              }}
            >
              <div></div>
              <div className="serial">{bottomSlider?.data?.voucherCodeClipboard}</div>
              <img className="copySerial" src="/build/images/alfa/copySerial.png" alt="copySerial" />
            </button>

            <button
              id="ContinueBtn"
              className="mt-3"
              onClick={() => {
                handleShare(bottomSlider?.data?.voucherCodeClipboard, parameters?.deviceType);
              }}
            >
              Share Code
            </button>

            <div className="stepsToRecharge">
              <div className="steps">
                <div className="dot"></div>
                <div className="textStep">Go to your phone tab</div>
              </div>
              <div className="steps">
                <div className="dot"></div>
                <div className="textStep">Paste the code</div>
              </div>
              <div className="steps">
                <div className="dot"></div>
                <div className="textStep">Tap Call</div>
              </div>
              <div className="steps">
                <div className="dot"></div>
                <div className="textStep">Your mobile prepaid line is now recharged</div>
              </div>
            </div>
          </div>
        )}
        {bottomSlider?.name == "successPostpaidSlider" && (
          <div id={bottomSlider?.name}>
            <div className="cardSec">
              <img src="/build/images/alfa/alfaLogo.png" alt="flag" />
              <div className="method">Alfa Bill Payment</div>
            </div>

            <div className="MoreInfo">
              <div className="label">Phone Number</div>
              <div className="value">+961 {localStorage.getItem("billMobileNumber")}</div>
            </div>

            <div className="br"></div>

            <div className="MoreInfo">
              <div className="label">Amount in $</div>
              <div className="value1">$ {bottomSlider.data.displayData.InformativeOriginalWSAmount}</div>
            </div>

            <div className="MoreInfo">
              <div className="label">Amount in L.L (Sayrafa Rate)</div>
              <div className="value1">L.L {parseInt(bottomSlider.data.displayData.Amount).toLocaleString()}</div>
            </div>

            <div className="MoreInfo">
              <div className="label">Fees in L.L (Sayrafa Rate)</div>
              <div className="value1">L.L {parseInt(bottomSlider.data.displayedFees).toLocaleString()}</div>
            </div>
            <div className="br"></div>

            <div className="MoreInfo">
              <div className="label">Total</div>
              <div className="value2">L.L {parseInt(bottomSlider.data.displayData.TotalAmount).toLocaleString()}</div>
            </div>
            <div className="footSectionPick">
              <button
                onClick={() => {
                  handleConfirmPay();
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
