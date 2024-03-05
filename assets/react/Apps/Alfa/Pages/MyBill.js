import React, { useEffect, useState, useRef } from "react";
import axios from "axios";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";

const MyBill = () => {
  const dispatch = useDispatch();
  const { BillRetrieveResult, BillPay } = AppAPI();
  const parameters = useSelector((state) => state.appData.parameters);
  const getPostpaidData = useSelector((state) => state.appData.postpaidData);
  const mobileResponse = useSelector((state) => state.appData.mobileResponse);

  const PinLength = 4;
  const [getBtnDesign, setBtnDesign] = useState(false);
  const inputRef = useRef(null);

  useEffect(() => {
    dispatch(
      settingData({
        field: "headerData",
        value: {
          title: "Pay Mobile Bill",
          backLink: "PayBill",
          currentPage: "MyBill",
        },
      })
    );
    dispatch(settingObjectData({ mainField: "bottomSlider", field: "isButtonDisable", value: false }));
  }, []);

  const handlePincodeClick = () => {
    inputRef.current.focus();
  };

  const handleInputChange = (event) => {
    const inputValue = event.target.value;
    const newPinCode = inputValue.slice(0, PinLength).split("");
    dispatch(settingObjectData({ mainField: "postpaidData", field: "pinCode", value: newPinCode }));
  };

  const handlePayNow = () => {
    if (getPostpaidData.pinCode.length === PinLength) {
      BillRetrieveResult({ mobileNumber: localStorage.getItem("billMobileNumber").replace(/\s/g, ""), currency: "LBP", Pin: getPostpaidData.pinCode, invoicesId: getPostpaidData.id });
    }
    setBtnDesign(false);
  };

  useEffect(() => {
    if (mobileResponse == "success") {
      BillPay(getPostpaidData.ResponseId);
    } else if (mobileResponse == "failed") {
      dispatch(settingData({ field: "isloading", value: false }));
      dispatch(settingObjectData({ mainField: "bottomSlider", field: "isButtonDisable", value: false }));
      dispatch(settingData({ field: "mobileResponse", value: "" }));
    }
  },[mobileResponse]);

  const handleInputFocus = () => {
    setBtnDesign(true);
  };

  return (
    <>
      <div id="MyBill">
        <div className="mainTitle">Insert the PIN you have received by SMS</div>

        <div className="PinSection" onClick={handlePincodeClick}>
          <div className="Pintitle">PIN</div>
          <div className="Pincode">
            {Array.from({ length: PinLength }, (_, index) => (
              <div className="code" key={index}>
                {getPostpaidData.pinCode[index] !== undefined ? getPostpaidData.pinCode[index] : ""}
              </div>
            ))}
            <input ref={inputRef} type="text" value={getPostpaidData.pinCode ? getPostpaidData.pinCode.join("") : ""} onChange={handleInputChange} onFocus={handleInputFocus} style={{ opacity: 0, position: "absolute", left: "-10000px" }} />
          </div>
        </div>

        <div className="continueSectionFocused">
          <button id="ContinueBtn" className="btnCont" onClick={handlePayNow} disabled={getPostpaidData.pinCode.length !== PinLength}>
            Continue
          </button>
          {getPostpaidData.isPinWrong && <p style={{ color: "red" }}>Unable to proceed, kindly try again.</p>}
        </div>
      </div>
    </>
  );
};

export default MyBill;
