import React, { useEffect, useState } from "react";
import axios from "axios";

const Default = ({ SetVoucherData, setActiveButton, setHeaderTitle, setBackLink }) => {

  useEffect(() => {
    setHeaderTitle("Alfa")
    setBackLink("default")
  }, [])

  const handleButtonClick = (name) => {
    setActiveButton({ name: name });
  };

  return (
    <div id="Default">
      <div className="MainTitle">What do you want to do?</div>

      {/* <div className="Cards" onClick={() => { handleButtonClick("PayBill") }}>
        <img className="logoImg" src="/build/images/alfa/alfaLogo.png" alt="alfaLogo" />
        <div className="Text">
          <div className="SubTitle">Pay Mobile Bills</div>
          <div className="description">Settle your Alfa bill quickly and securely</div>
        </div>
      </div> */}

      <div className="Cards"
        onClick={() => {
          handleButtonClick("ReCharge");

          axios
            .post("/alfa/ReCharge")
            .then((response) => {
              // console.log(response?.data?.message);
              SetVoucherData(response?.data?.message);
            })
            .catch((error) => {
              console.log(error);
            });
        }}
      >
        <img className="logoImg" src="/build/images/alfa/alfaLogo.png" alt="alfaLogo" />
        <div className="Text">
          <div className="SubTitle">Re-charge Alfa</div>
          <div className="description">Recharge your Alfa prepaid number</div>
        </div>
      </div>
    </div>
  );
};

export default Default;
