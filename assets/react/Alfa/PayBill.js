import React, { useEffect, useState } from "react";
import axios from "axios";

const PayBill = ({ setPostpaidData, setModalShow, setModalName, setErrorModal, activeButton, setActiveButton, setHeaderTitle, setBackLink }) => {
  const [mobileNumber, setMobileNumber] = useState("");
  const [mobileNumberNoFormat, setMobileNumberNoFormat] = useState("70102030");
  const [currency, setCurrency] = useState("LBP");
  const [isButtonDisabled, setIsButtonDisabled] = useState(false);

  useEffect(() => {
    setHeaderTitle("Pay Mobile Bill")
    setBackLink("")
  }, [])

  const handleContinue = () => {
    setIsButtonDisabled(true);

    localStorage.setItem("billMobileNumber", mobileNumber);
    localStorage.setItem("billcurrency", currency);

    axios
      .post("/alfa/bill",
        {
          mobileNumber: mobileNumber.replace(/\s/g, ''),
          currency: currency
        }
      )
      .then((response) => {
        console.log(response);
        if (response?.data?.message == "connected") {
          setActiveButton({ name: "MyBill" });
          setPostpaidData({ id: response?.data?.invoicesId })
        } else {
          setModalName("ErrorModal");
          setErrorModal({
            img: "/build/images/alfa/error.png",
            title: "Please Try again",
            desc: `Mobile number not found`,
            // path: response.data.path,
            // btn:'Top up'
          });
          setModalShow(true);
        }
      })
      .catch((error) => {
        console.log(error);
      });
  };

  const handleMobileNumberChange = (event) => {
    setIsButtonDisabled(false);
    const value = event.target.value;
    const formattedValue = formatMobileNumber(value);
    setMobileNumber(formattedValue);
  };

  const formatMobileNumber = (value) => {
    const digitsOnly = value.replace(/\D/g, "");
    const truncatedValue = digitsOnly.slice(0, 8);
    if (truncatedValue.length > 0 && truncatedValue[0] !== '0' && truncatedValue[0] !== '7' && truncatedValue[0] !== '8') {
      return '0' + truncatedValue;
    }
    if (truncatedValue.length > 3) {
      return truncatedValue.replace(/(\d{2})(\d{3})(\d{3})/, "$1 $2 $3");
    }
    return truncatedValue;
  };

  const [getBtnDesign, setBtnDesign] = useState(false);

  const handleInputFocus = () => {
    setBtnDesign(true);
  };

  const handleInputBlur = () => {
    setBtnDesign(false);
  };

  return (
    <div id="PayBill">
      <div className="mainTitle">Enter your phone number to recharge</div>

      <div className="MobileNbContainer mt-3">
        <div className="place">
          <img src="/build/images/alfa/flag.png" alt="flag" />
          <div className="code">+961</div>
        </div>
        <input type="tel" className="nbInput" placeholder="Phone number" value={mobileNumber} onChange={handleMobileNumberChange} onFocus={handleInputFocus} onBlur={handleInputBlur}/>
      </div>

      <button id="ContinueBtn" className={`${!getBtnDesign ? "btnCont" : "btnContFocus"}`} onClick={handleContinue} disabled={mobileNumber.replace(/\s/g, '').length !== 8 || isButtonDisabled}>Continue</button>
    </div>
  );
};

export default PayBill;

