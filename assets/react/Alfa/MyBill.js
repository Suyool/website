import React, { useEffect, useState } from "react";
import axios from "axios";

const MyBill = ({ setModalShow, setModalName, setSuccessModal, setErrorModal, setActiveButton, setHeaderTitle, setBackLink }) => {

  useEffect(() => {
    setHeaderTitle("Pay Mobile Bill")
    setBackLink("PayBill")
  }, [])

  const [pinCode, setPinCode] = useState([]);
  const [getResponseId, setResponseId] = useState(null);
  const [getPaymentConfirmation, setPaymentConfirmation] = useState(false);

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
      setPaymentConfirmation(true);
      axios
        .post("/alfa/bill/RetrieveResults",
          {
            mobileNumber: localStorage.getItem("billMobileNumber"),
            currency: localStorage.getItem("billcurrency"),
            Pin: pinCode,
          }
        )
        .then((response) => {
          console.log(response);
          setResponseId(response?.data?.postpayed);
        })
        .catch((error) => {
          console.log(error);
        });
    }
  };

  const handleConfirmPay = () => {
    console.log(getResponseId)
    axios
      .post("/alfa/bill/pay",
        {
          ResponseId: getResponseId
        }
      )
      .then((response) => {
        console.log(response);
        setModalName("SuccessModal");
        setSuccessModal({
          imgPath: "/build/images/Alfa/SuccessImg.png",
          title: "Alfa Bill Paid Successfully",
          desc: "You have successfully paid your Alfa bill of {currency}{amount}."
        })
        setModalShow(true);
      })
      .catch((error) => {
        console.log(error);
      });
  };

  return (
    <div id="MyBill" className={`${getPaymentConfirmation && "hideBack"}`}>

      {getPaymentConfirmation ?
        <>
          <div id="PaymentConfirmationSection">
            <div className="topSection">
              <div className="brBoucket"></div>
              <div className="titles">
                <div className="titleGrid">Payment Confirmation</div>
                <button onClick={() => { setActiveButton({ name: "PayBill" }); }}>Cancel</button>
              </div>
            </div>

            <div className="bodySection">
              <div className="cardSec">
                <img src="/build/images/Alfa/alfaLogo.png" alt="flag" />
                <div className="method">Alfa Bill Payment</div>
              </div>
              <div className="descSec">Amount</div>
              <div className="Amount">10 $</div>
            </div>

            <div className="footSectionPick">
              <button onClick={handleConfirmPay} >Confirm & Pay</button>
            </div>
          </div>
        </>
        :
        <>
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
        </>
      }


    </div>
  );
};

export default MyBill;
