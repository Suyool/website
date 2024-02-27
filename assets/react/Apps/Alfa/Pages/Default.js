import React from "react";
import axios from "axios";
import { useDispatch } from "react-redux";
import { settingObjectData } from "../Redux/Slices/AppSlice";

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
        <img className="logoImg" src="/build/images/alfa/alfaLogo.png" alt="alfaLogo" />
        <div className="Text">
          <div className="SubTitle">Pay Mobile Bills</div>
          <div className="description">Settle your Alfa bill quickly and securely</div>
        </div>
      </div>

      <div
        className="Cards"
        onClick={() => {
          dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "ReCharge" }));

          axios
            .post("/alfa/ReCharge")
            .then((response) => {
              dispatch(settingObjectData({ mainField: "prepaidData", field: "vouchers", value: response?.data?.message }));
            })
            .catch((error) => {
              console.log(error);
            });
        }}
      >
        <img className="logoImg" src="/build/images/alfa/alfaLogo.png" alt="alfaLogo" />
        <div className="Text">
          <div className="SubTitle">Purchase Alfa Recharge Code</div>
          <div className="description">Choose your Alfa package & buy the recharge code for your prepaid number.</div>
        </div>
      </div>
    </div>
  );
};

export default Default;
