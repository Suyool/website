import React, { useEffect, useState } from "react";
import axios from "axios";
import { Spinner } from "react-bootstrap";

const PayBill = ({
  setPostpaidData,
  setModalShow,
  setModalName,
  setErrorModal,
  activeButton,
  setActiveButton,
  setHeaderTitle,
  setBackLink,
}) => {
  const [ mobileNumber, setMobileNumber ] = useState("");
  const [ currency, setCurrency ] = useState("LBP");
  const [ isButtonDisabled, setIsButtonDisabled ] = useState(false);
  const [ getSpinnerLoader, setSpinnerLoader ] = useState(false);

  useEffect(() => {
    setHeaderTitle("Pay Mobile Bill");
    setBackLink("");
  }, []);

  const handleContinue = () => {
    setIsButtonDisabled(true);
    setSpinnerLoader(true);
    localStorage.setItem("billMobileNumber", mobileNumber);
    localStorage.setItem("billcurrency", currency);
    axios
      .post("/touch/bill", {
        mobileNumber: mobileNumber.replace(/\s/g, ""),
        currency: currency,
      })
      .then((response) => {
        console.log(response);
        if (response?.data?.isSuccess) {
          setActiveButton({ name: "MyBill" });
          setPostpaidData({ id: response?.data?.postpaidRequestId });
        }
        else if (
          response?.data?.message ==
          "Not Enough Balance Amount to be paid"
        ) {
          setSpinnerLoader(false);
          setModalName("ErrorModal");
          setErrorModal({
            img: "/build/images/alfa/error.png",
            title: " No Pending Bill",
            desc: (
              <div>
                There is no pending bill on the mobile number {localStorage.getItem("billMobileNumber")}
                <br/>
                Kindly try again later
              </div>
            ),
            btn: "OK",
          });
          setModalShow(true);
        }
         else {
          setModalName("ErrorModal");
          setErrorModal({
            img: "/build/images/alfa/error.png",
            title: "Number Not Found ",
            desc: (
              <div>
                The number you entered was not found in the system.
                <br />
                Kindly try another number.
              </div>
            ),
            btn: "OK",
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
    if (truncatedValue.length > 3) {
      return truncatedValue.replace(/(\d{2})(\d{3})(\d{3})/, "$1 $2 $3");
    }
    return truncatedValue;
  };

  return (
    <div id="PayBill" className={getSpinnerLoader ? "hideBack" : ""}>
      {getSpinnerLoader && (
        <div id="spinnerLoader">
          <Spinner className="spinner" animation="border" variant="secondary" />
        </div>
      )}
      <div className="mainTitle">Enter your phone number to pay your mobile bill</div>

      <div className="MobileNbContainer mt-3">
        <div className="place">
          <img src="/build/images/touch/flag.png" alt="flag" />
          <div className="code">+961</div>
        </div>
        <input
          type="tel"
          className={getSpinnerLoader ? "nbInputHide" : "nbInput"}
          placeholder="Phone number"
          value={mobileNumber}
          onChange={handleMobileNumberChange}
        />
      </div>

      <button
        id="ContinueBtn"
        className="btnCont"
        onClick={handleContinue}
        disabled={
          mobileNumber.replace(/\s/g, "").length !== 8 || isButtonDisabled
        }
      >
        Continue
      </button>
    </div>
  );
};

export default PayBill;
