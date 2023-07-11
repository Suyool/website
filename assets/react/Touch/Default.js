import React, { useEffect, useState } from "react";

const Default = ({ activeButton, setActiveButton, setHeaderTitle, setBackLink }) => {

  useEffect(() => {
    setHeaderTitle("Touch")
    setBackLink("default")
  }, [])

  const handleButtonClick = (name) => {
    setActiveButton({ name: name });
  };

  return (
    <div id="Default">
      <div className="MainTitle">What do you want to do?</div>

      <div className="Cards" onClick={() => { handleButtonClick("PayBill") }}>
        <img src="/build/images/Touch/TouchLogo.png" alt="TouchLogo" />
        <div className="Text">
          <div className="SubTitle">Pay Mobile Bills</div>
          <div className="description">Settle your Touch bill quickly and securely</div>
        </div>
      </div>

      <div className="Cards" onClick={() => { handleButtonClick("ReCharge") }}>
        <img src="/build/images/Touch/TouchLogo.png" alt="TouchLogo" />
        <div className="Text">
          <div className="SubTitle">Re-charge Touch</div>
          <div className="description">Recharge your Touch prepaid number</div>
        </div>
      </div>
    </div>
  );
};

export default Default;
