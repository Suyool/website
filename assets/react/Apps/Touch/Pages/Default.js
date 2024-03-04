import React from "react";
import { useDispatch } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";

const Default = () => {
  const dispatch = useDispatch();
  const { Recharge } = AppAPI();

  return (
    <div id="Default">
      <div className="MainTitle">What do you want to do?</div>

      <div
        className="Cards"
        onClick={() => {
          // dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "PayBill" }));
          dispatch(settingData({
            field: "modalData",
            value: {
              isShow: true,
              name: "ErrorModal",
              img: "/build/images/alfa/error.png",
              title: "Service is not available .",
              desc: "This service is not available at this moment. Kindly try again later.",
              btn: "OK",
              flag: "",
            },
          }))
        }}
      >
        <img className="logoImg" src="/build/images/touch/touchLogo.png" alt="touchLogo" />
        <div className="Text">
          <div className="SubTitle">Pay Mobile Bills</div>
          <div className="description">Settle your Touch bill quickly and securely</div>
        </div>
      </div>

      <div
        className="Cards"
        onClick={() => {
          dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "ReCharge" }));
          Recharge();
        }}
      >
        <img className="logoImg" src="/build/images/touch/touchLogo.png" alt="touchLogo" />
        <div className="Text">
          <div className="SubTitle">Purchase Touch Recharge Code</div>
          <div className="description">Choose your Touch package & buy the recharge code for your prepaid number.</div>
        </div>
      </div>
    </div>
  );
};

export default Default;
