import React, { useEffect, useState } from "react";
import axios from "axios";

const MyBill = ({ activeButton, setActiveButton, setHeaderTitle, setBackLink }) => {

  useEffect(() => {
    setHeaderTitle("Pay Mobile Bill")
    setBackLink("PayBill")
  }, [])

  const [pinCode, setPinCode] = useState([]);

  const handleNbClick = (num) => {
    if (pinCode.length < 4) {
      setPinCode([...pinCode, num]);
    }
  };

  const handleDelete = () => {
    if (pinCode.length > 0) {
      setPinCode(pinCode.slice(0, -1));
    }
  };

  const handlePayNow = () => {
    if (pinCode.length === 4) {
      // Perform payment or further actions
      console.log("Payment processing...");
      console.log(pinCode);

      // axios
      //   .post("/alfa/bill/pay",
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
    }
  };

  return (
    <div id="MyBill">
      <div className="mainTitle">Insert the PIN you have received by SMS</div>

      <div className="PinSection">
        <div className="Pintitle">PIN</div>
        <div className="Pincode">
          {Array.from({ length: 4 }, (_, index) => (
            <div className="code" key={index}>
              {pinCode[index] !== undefined ? pinCode[index] : ""}
            </div>
          ))}
        </div>
      </div>

      <div className="continueSection">
        <button id="ContinueBtn" className="btnCont" onClick={handlePayNow} disabled={pinCode.length !== 4}>continue</button>

        <div className="keybord">
          <button className="keyBtn" onClick={() => handleNbClick(1)}>1</button>
          <button className="keyBtn" onClick={() => handleNbClick(2)}>2</button>
          <button className="keyBtn" onClick={() => handleNbClick(3)}>3</button>
          <button className="keyBtn" onClick={() => handleNbClick(4)}>4</button>
          <button className="keyBtn" onClick={() => handleNbClick(5)}>5</button>
          <button className="keyBtn" onClick={() => handleNbClick(6)}>6</button>
          <button className="keyBtn" onClick={() => handleNbClick(7)}>7</button>
          <button className="keyBtn" onClick={() => handleNbClick(8)}>8</button>
          <button className="keyBtn" onClick={() => handleNbClick(9)}>9</button>
          <button className="keyBtn"></button>
          <button className="keyBtn" onClick={() => handleNbClick(0)}>0</button>
          <button className="keyBtn" onClick={handleDelete}><img src="/build/images/Alfa/clearNb.png" alt="flag" /></button>
        </div>
      </div>
    </div>
  );
};

export default MyBill;
