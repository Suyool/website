import React, { useEffect, useState } from "react";
import axios from "axios";

const PayBill = ({ setLandlineMobile, setLandlineDisplayedData , setLandlineData, setActiveButton, setHeaderTitle, setBackLink }) => {
  const [mobileNumber, setMobileNumber] = useState("01123120");

  useEffect(() => {
    setHeaderTitle("Pay Landline Bill")
    setBackLink("")
  }, [])

  const handleContinue = () => {
    // console.log("Mobile Number:", mobileNumber);
    axios
      .post("/ogero/landline",
        {
          mobileNumber: mobileNumber.replace(/\s/g, ''),
        }
      )
      .then((response) => {
        console.log(response);
        setActiveButton({ name: "MyBill" });
        setLandlineData({ id: response?.data?.LandlineReqId })
        setLandlineDisplayedData(response?.data?.message)
        setLandlineMobile(response?.data?.mobileNb)
      })
      .catch((error) => {
        console.log(error);
      });

  };

  const handleMobileNumberChange = (event) => {
    const value = event.target.value;
    const formattedValue = formatMobileNumber(value);
    setMobileNumber(formattedValue);
  };

  const formatMobileNumber = (value) => {
    const digitsOnly = value.replace(/\D/g, "");
    const truncatedValue = digitsOnly.slice(0, 8);
    if (truncatedValue.length > 3) {
      return truncatedValue.replace(/(\d{2})(\d{3})(\d{3})/, "$1 $2 $3");
    }
    return truncatedValue;
  };


  return (
    <div id="PayBill">
      <div className="mainTitle">Enter the landline number</div>

      <div className="MobileNbContainer mt-3">
        <div className="place">
          <img src="/build/images/Ogero/flag.png" alt="flag" />
          <div className="code">+961</div>
        </div>
        <input className="nbInput" placeholder="|" value={mobileNumber} onChange={handleMobileNumberChange} />
      </div>

      <button id="ContinueBtn" className="btnCont" onClick={handleContinue}>Continue</button>
    </div>
  );
};

export default PayBill;
