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
          dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "Login" }));
        }}
      >
        <img className="logoImg" src="/build/images/windsl/windsl.png" />
        <div className="Text">
          <div className="SubTitle">Top Up WinDSL Account</div>
          <div className="description">Easily top up your account for high-speed internet</div>
        </div>
      </div>
    </div>
  );
};

export default Default;
