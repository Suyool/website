import React, { useEffect, useState } from "react";
import axios from "axios";
import { Spinner } from "react-bootstrap";

const PayBill = ({ setModalShow, setErrorModal, setModalName, setLandlineMobile, setLandlineDisplayedData, setLandlineData, setActiveButton, setHeaderTitle, setBackLink }) => {
  const [mobileNumber, setMobileNumber] = useState("");
  const [getSpinnerLoader, setSpinnerLoader] = useState(false);

  useEffect(() => {
    setHeaderTitle("Pay Landline Bill")
    setBackLink("")
  }, [])

  const handleContinue = () => {
    if (mobileNumber == "") {
      setModalName("ErrorModal");
      setErrorModal({
        imgPath: "/build/images/alfa/error.png",
        title: "Please Try again",
        desc: `Mobile number required`,
        // path: response.data.path,
        // btn:'Top up'
      });
      setModalShow(true);
    } else {
      setSpinnerLoader(true);
      axios
        .post("/ogero/landline",
          {
            mobileNumber: mobileNumber.replace(/\s/g, ''),
          }
        )
        .then((response) => {
          if (response?.data?.LandlineReqId != -1) {
            setActiveButton({ name: "MyBill" });
            setLandlineData({ id: response?.data?.LandlineReqId })
            setLandlineDisplayedData(response?.data?.message)
            setLandlineMobile(response?.data?.mobileNb)
          } else {
            setSpinnerLoader(false);
            setModalName("ErrorModal");
            setErrorModal({
              imgPath: "/build/images/alfa/error.png",
              title: "Number Not Found",
              desc: (
                <div>
                  The number you entered was not found in the system.
                  <br />
                  Kindly try another number.
                </div>
              ),
              btn: 'OK'
            });
            setModalShow(true);

          }

        })
        .catch((error) => {
          console.log(error);
        });

    }

  };

  const handleMobileNumberChange = (event) => {
    const value = event.target.value;
    const formattedValue = formatMobileNumber(value);
    setMobileNumber(formattedValue);
  };

  const formatMobileNumber = (value) => {
    const digitsOnly = value.replace(/\D/g, "");
    const truncatedValue = digitsOnly.slice(0, 8);
    if (truncatedValue[0] !== '0') {
      return '0' + truncatedValue;
    }
    if (truncatedValue.length > 3) {
      return truncatedValue.replace(/(\d{2})(\d{3})(\d{3})/, "$1 $2 $3");
    }
    return truncatedValue;
  };


  return (
    <div id="PayBill" className={getSpinnerLoader ? "hideBack" : ""}>
      {getSpinnerLoader && <div id="spinnerLoader"><Spinner className="spinner" animation="border" variant="secondary" /></div>}
      <div className="mainTitle">Enter the landline number</div>

      <div className="MobileNbContainer mt-3">
        <div className="place">
          <img src="/build/images/Ogero/flag.png" alt="flag" />
          <div className="code">+961</div>
        </div>
        <input type="tel" className={getSpinnerLoader ? "nbInputHide" : "nbInput"} placeholder="Phone number" value={mobileNumber} onChange={handleMobileNumberChange} required />
      </div>

      <button id="ContinueBtn" className="btnCont" onClick={handleContinue}>Continue</button>
    </div>
  );
};

export default PayBill;
