import React, { useEffect, useState } from "react";
import axios from "axios";

const PayBill = ({ activeButton, setActiveButton, setHeaderTitle, setBackLink }) => {
  const [mobileNumber, setMobileNumber] = useState("");
  const [currency, setCurrency] = useState("USD");

  useEffect(() => {
    setHeaderTitle("Pay Mobile Bill")
    setBackLink("")
  }, [])

  const handleContinue = () => {
    // console.log("Mobile Number:", mobileNumber);
    // console.log("Currency:", currency);

    // axios
    //   .post("/Ogero/bill",
    //     {
    //       mobileNumber: mobileNumber
    //     }
    //   )
    //   .then((response) => {
    //     console.log(response);
    //   })
    //   .catch((error) => {
    //     console.log(error);
    //   });

    setActiveButton({ name: "MyBill" });

  };

  return (
    <div id="PayBill">
      <div className="mainTitle">Enter your phone number to recharge</div>

      <div className="MobileNbContainer mt-3">
        <div className="place">
          <img src="/build/images/Ogero/flag.png" alt="flag" />
          <div className="code">+961</div>
        </div>
        <input className="nbInput" placeholder="|" value={mobileNumber} onChange={(e) => setMobileNumber(e.target.value)} />
      </div>

      <div className="pCurrency">
        <div className="subTitle">My Payment Currency</div>

      </div>

      <div className="currencies">
        <div className={`${currency === "USD" ? "Currency" : "activeCurrency"}`} onClick={() => setCurrency("USD")}>USD</div>
        <div className={`${currency === "LBP" ? "Currency" : "activeCurrency"}`} onClick={() => setCurrency("LBP")}>LBP</div>
      </div>
      
      {/* {currency == "USD" && <p>USD</p>}
      {currency == "LBP" && <p>LBP</p>} */}

      <button id="ContinueBtn" className="btnCont" onClick={handleContinue}>Continue</button>
    </div>
  );
};

export default PayBill;
