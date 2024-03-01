import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { settingData } from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";
import { formatMobileNumber } from "../Utils/functions";

const PayBill = () => {
  const dispatch = useDispatch();
  const { Bill } = AppAPI();
  const isLoading = useSelector((state) => state.appData.isloading);
  const [mobileNumber, setMobileNumber] = useState("");
  const [isButtonDisabled, setIsButtonDisabled] = useState(false);
  const [getBtnDesign, setBtnDesign] = useState(false);

  useEffect(() => {
    dispatch(settingData({ field: "headerData", value: { title: "Pay Mobile Bill", backLink: "", currentPage: "PayBill" } }));
  }, []);

  const handleContinue = () => {
    setIsButtonDisabled(true);
    localStorage.setItem("billMobileNumber", mobileNumber);
    Bill({ mobileNumber: mobileNumber.replace(/\s/g, ""), currency: "LBP" });
    setBtnDesign(false);
  };

  const handleMobileNumberChange = (event) => {
    setIsButtonDisabled(false);
    const value = event.target.value;
    const formattedValue = formatMobileNumber(value);
    setMobileNumber(formattedValue);
  };

  return (
    <div id="PayBill">
      <div className="mainTitle">Enter your phone number to recharge</div>

      <div className="MobileNbContainer mt-3">
        <div className="place">
          <img src="/build/images/touch/flag.png" alt="flag" />
          <div className="code">+961</div>
        </div>
        <input
          type="tel"
          className={isLoading ? "nbInputHide" : "nbInput"}
          placeholder="Phone number"
          value={mobileNumber}
          onChange={handleMobileNumberChange}
          onFocus={() => {
            setBtnDesign(true);
          }}
        />
      </div>

      <button id="ContinueBtn" className={`${!getBtnDesign ? "btnCont" : "btnContFocus"}`} onClick={handleContinue} disabled={mobileNumber.replace(/\s/g, "").length !== 8 || isButtonDisabled}>
        Continue
      </button>
    </div>
  );
};

export default PayBill;
