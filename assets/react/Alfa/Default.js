import React, { useEffect, useState } from "react";
import axios from "axios";

const Default = ({ activeButton, setActiveButton, setHeaderTitle, setBackLink }) => {

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

      <div className="Cards" onClick={() => { handleButtonClick("PayBill") }}>
        <img src="/build/images/Alfa/alfaLogo.png" alt="alfaLogo" />
        <div className="Text">
          <div className="SubTitle">Pay Mobile Bills</div>
          <div className="description">Settle alfa bills for postpaid cards</div>
        </div>
      </div>

      <div className="Cards"
        onClick={() => {
          handleButtonClick("ReCharge");

          axios
            .post("/alfa/ReCharge")
            .then((response) => {
              console.log(response?.data?.message?.d?.ppavouchertypes);
            })
            .catch((error) => {
              console.log(error);
            });
        }}
      >
        <img src="/build/images/Alfa/alfaLogo.png" alt="alfaLogo" />
        <div className="Text">
          <div className="SubTitle">Re-charge Alfa</div>
          <div className="description">Recharge prepaid mobile lines</div>
        </div>
      </div>
    </div>
  );
};

export default Default;
