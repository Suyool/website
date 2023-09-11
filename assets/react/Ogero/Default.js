import React, { useEffect } from "react";

const Default = ({
  activeButton,
  setActiveButton,
  setHeaderTitle,
  setBackLink,
}) => {
  useEffect(() => {
    setHeaderTitle("Ogero");
    setBackLink("default");
  }, []);

  const handleButtonClick = (name) => {
    setActiveButton({ name: name });
  };

  return (
    <div id="Default">
      <div className="MainTitle">What do you want to do?</div>

      <div
        className="Cards"
        onClick={() => {
          handleButtonClick("PayBill");
        }}
      >
        <img src="/build/images/Ogero/OgeroLogo.png" alt="OgeroLogo" />
        <div className="Text">
          <div className="SubTitle">Pay Landline Bills</div>
          <div className="description">
            Settle your Ogero bill quickly and securely
          </div>
        </div>
      </div>
    </div>
  );
};

export default Default;
