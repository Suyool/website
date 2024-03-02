import React, { useEffect } from "react";
import axios from "axios";

const Default = ({
  SetVoucherData,
  setActiveButton,
  setHeaderTitle,
  setBackLink,
  setModalShow,
  setModalName,
  setErrorModal,
}) => {
  useEffect(() => {
    setHeaderTitle("Touch");
    setBackLink("default");
  }, []);

  const handleButtonClick = (name) => {
    setModalName("ErrorModal");
    setErrorModal({
      img: "/build/images/alfa/error.png",
      title: "Services not available",
      desc: `This service is not available at this moment.
                Kindly try again later. `,
      btn: "OK",
    });
    setModalShow(true);
    // setActiveButton({ name: name });
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
        <img
          className="logoImg"
          src="/build/images/touch/touchLogo.png"
          alt="touchLogo"
        />
        <div className="Text">
          <div className="SubTitle">Pay Mobile Bills</div>
          <div className="description">
            Settle your Touch bill quickly and securely
          </div>
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
          <div className="SubTitle">Purchase Touch Recharge Code</div>
          <div className="description">
            Choose your Touch package & buy the recharge code for your prepaid
            number.
          </div>
        </div>
      </div>
    </div>
  );
};

export default Default;
