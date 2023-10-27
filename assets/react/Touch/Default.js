import React, { useEffect } from "react";
import axios from "axios";

const Default = ({
  SetVoucherData,
  setActiveButton,
  setHeaderTitle,
  setBackLink,
}) => {
  useEffect(() => {
    setHeaderTitle("Touch");
    setBackLink("default");
  }, []);

  const handleButtonClick = (name) => {
    setActiveButton({ name: name });
  };

  return (
    <div id="Default">
      <div className="MainTitle">What do you want to do?</div>

      <div className="Cards" onClick={() => { handleButtonClick("PayBill") }}>
        <img className="logoImg" src="/build/images/touch/touchLogo.png" alt="touchLogo" />
        <div className="Text">
          <div className="SubTitle">Pay Mobile Bills</div>
          <div className="description">Settle your Touch bill quickly and securely</div>
        </div>
      </div>

      <div
        className="Cards"
        onClick={() => {
          handleButtonClick("ReCharge");

          axios
            .post("/touch/ReCharge")
            .then((response) => {
              SetVoucherData(response?.data?.message);
            })
            .catch((error) => {
              console.log(error);
            });
        }}
      >
        <img
          className="logoImg"
          src="/build/images/touch/touchLogo.png"
          alt="touchLogo"
        />
        <div className="Text">
          <div className="SubTitle">Buy Code To Re-charge</div>
          <div className="description">Recharge your Touch prepaid number</div>
        </div>
      </div>
    </div>
  );
};

export default Default;
