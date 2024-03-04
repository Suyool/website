import React from "react";
import { useDispatch } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const Default = () => {
  const dispatch = useDispatch();

  return (
    <div id="Default">
      <div className="MainTitle">What do you want to do?</div>

      <div
        className="Cards"
        onClick={() => {
          dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "PayBill" }));
        }}
      >
        <img className="logoImg" src="/build/images/Ogero/OgeroLogo.png" alt="OgeroLogo" />
        <div className="Text">
          <div className="SubTitle">Pay Landline Bills</div>
          <div className="description">Settle your Ogero bill quickly and securely</div>
        </div>
      </div>
    </div>
  );
};

export default Default;
