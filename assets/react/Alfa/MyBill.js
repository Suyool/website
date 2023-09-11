import React, { useEffect, useState, useRef } from "react";
import axios from "axios";
import { Spinner } from "react-bootstrap";

const MyBill = ({
  setDataGetting,
  getDataGetting,
  parameters,
  getPostpaidData,
  setModalShow,
  setModalName,
  setSuccessModal,
  setErrorModal,
  setActiveButton,
  setHeaderTitle,
  setBackLink,
}) => {
  useEffect(() => {
    setHeaderTitle("Pay Mobile Bill");
    setBackLink("PayBill");
    setIsButtonDisabled(false);
  }, []);

  const [pinCode, setPinCode] = useState([]);
  const [getResponseId, setResponseId] = useState(null);
  const [getSpinnerLoader, setSpinnerLoader] = useState(false);
  const [getDisplayData, setDisplayData] = useState([]);
  const [getdisplayedFees, setdisplayedFees] = useState("");
  const [getPaymentConfirmation, setPaymentConfirmation] = useState(false);
  const [isButtonDisabled, setIsButtonDisabled] = useState(false);
  const [getPinWrong, setPinWrong] = useState(false);

  const inputRef = useRef(null);

  const handlePincodeClick = () => {
    inputRef.current.focus();
  };

  const handleInputChange = (event) => {
    const inputValue = event.target.value;
    const newPinCode = inputValue.slice(0, 6).split("");
    setPinCode(newPinCode);
  };

  const handlePayNow = () => {
    if (pinCode.length === 6) {
      setSpinnerLoader(true);
      axios
        .post("/alfa/bill/RetrieveResults", {
          mobileNumber: localStorage
            .getItem("billMobileNumber")
            .replace(/\s/g, ""),
          currency: "LBP",
          Pin: pinCode,
          invoicesId: getPostpaidData.id,
        })
        .then((response) => {
          console.log(response);
          if (response.data.message == "connected") {
            setSpinnerLoader(false);
            setDisplayData(response?.data?.displayData);
            setdisplayedFees(response?.data?.displayedFees);
            setPaymentConfirmation(true);
            setResponseId(response?.data?.postpayed);
          } else if (response.data.message == "213") {
            setPinWrong(true);
            setPinCode("");
            setSpinnerLoader(false);
          } else {
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/alfa/error.png",
              title: "No Available Bill",
              desc: `There is no available bill for ${localStorage.getItem(
                "billMobileNumber"
              )} at the moment. 
                Kindly try again later. `,
              btn: "OK",
            });
            setModalShow(true);
            setPinCode("");
          }
        })
        .catch((error) => {
          console.log(error);
        });
    }
    setBtnDesign(false);
  };

  const handleConfirmPay = () => {
    setIsButtonDisabled(true);
    setSpinnerLoader(true);
    if (parameters?.deviceType === "Android") {
      setTimeout(() => {
        window.AndroidInterface.callbackHandler("message");
      }, 2000);
    } else if (parameters?.deviceType === "Iphone") {
      setTimeout(() => {
        window.webkit.messageHandlers.callbackHandler.postMessage(
          "fingerprint"
        );
      }, 2000);
    }
  };

  useEffect(() => {
    if (getDataGetting == "success") {
      axios
        .post("/alfa/bill/pay", {
          ResponseId: getResponseId,
        })
        .then((response) => {
          console.log(response.data);
          const jsonResponse = response?.data?.message;
          setSpinnerLoader(false);
          if (response.data?.IsSuccess) {
            setModalName("SuccessModal");
            setSuccessModal({
              imgPath: "/build/images/alfa/SuccessImg.png",
              title: "Alfa Bill Paid Successfully",
              desc: `You have successfully paid your Alfa bill of L.L ${" "} ${parseInt(
                response.data?.data.amount
              ).toLocaleString()}.`,
            });
            setModalShow(true);
          } else {
            console.log(response.data.flagCode);
            if (
              response.data.IsSuccess == false &&
              response.data.flagCode == 10
            ) {
              setModalName("ErrorModal");
              setErrorModal({
                img: "/build/images/alfa/error.png",
                title: jsonResponse.Title,
                desc: jsonResponse.SubTitle,
                path: jsonResponse.ButtonOne.Flag,
                btn: jsonResponse.ButtonOne.Text,
              });
              setModalShow(true);
            } else if (
              !response.data.IsSuccess &&
              response.data.flagCode == 11
            ) {
              setModalName("ErrorModal");
              setErrorModal({
                img: "/build/images/alfa/error.png",
                title: jsonResponse.Title,
                desc: jsonResponse.SubTitle,
                path: jsonResponse.ButtonOne.Flag,
                btn: jsonResponse.ButtonOne.Text,
              });
              setModalShow(true);
            } else {
              setModalName("ErrorModal");
              setErrorModal({
                img: "/build/images/alfa/error.png",
                title: "Please Try again",
                desc: `You cannot purchase now`,
                // path: response.data.path,
                btn: "OK",
              });
              setModalShow(true);
            }
          }
        })
        .catch((error) => {
          console.log(error);
          setSpinnerLoader(false);
        });
    } else if (getDataGetting == "failed") {
      setSpinnerLoader(false);
      setIsButtonDisabled(false);
      setDataGetting("");
    }
  });

  const [getBtnDesign, setBtnDesign] = useState(false);

  const handleInputFocus = () => {
    setBtnDesign(true);
  };

  const handleInputBlur = () => {
    console.log("hi");
    setBtnDesign(false);
  };

  return (
    <>
      {getPaymentConfirmation && (
        <div
          id="PaymentConfirmationSection"
          className={`${getSpinnerLoader ? "opacityNone" : ""}`}
        >
          <div className="topSection">
            <div className="brBoucket"></div>
            <div className="titles">
              <div className="titleGrid">Payment Confirmation</div>
              <button
                onClick={() => {
                  setActiveButton({ name: "" });
                }}
              >
                Cancel
              </button>
            </div>
          </div>

          <div className="bodySection">
            <div className="cardSec">
              <img src="/build/images/alfa/alfaLogo.png" alt="flag" />
              <div className="method">Alfa Bill Payment</div>
            </div>

            <div className="MoreInfo">
              <div className="label">Phone Number</div>
              <div className="value">
                +961 {localStorage.getItem("billMobileNumber")}
              </div>
            </div>

            <div className="br"></div>

            <div className="MoreInfo">
              <div className="label">Amount in $</div>
              <div className="value1">
                $ {getDisplayData.InformativeOriginalWSAmount}
              </div>
            </div>

            <div className="MoreInfo">
              <div className="label">Amount in L.L (Sayrafa Rate)</div>
              <div className="value1">
                L.L {parseInt(getDisplayData.Amount).toLocaleString()}
              </div>
            </div>

            <div className="MoreInfo">
              <div className="label">Fees in L.L (Sayrafa Rate)</div>
              <div className="value1">
                L.L {parseInt(getdisplayedFees).toLocaleString()}
              </div>
            </div>
            <div className="br"></div>

            <div className="MoreInfo">
              <div className="label">Total</div>
              <div className="value2">
                L.L {parseInt(getDisplayData.TotalAmount).toLocaleString()}
              </div>
            </div>
          </div>

          <div className="footSectionPick">
            <button onClick={handleConfirmPay} disabled={isButtonDisabled}>
              Confirm & Pay
            </button>
          </div>
        </div>
      )}

      <div
        id={`MyBill`}
        className={`${
          getPaymentConfirmation || getSpinnerLoader ? "hideBackk" : ""
        }`}
      >
        {getSpinnerLoader && (
          <div id="spinnerLoader">
            <Spinner
              className="spinner"
              animation="border"
              variant="secondary"
            />
          </div>
        )}

        <div className="mainTitle">Insert the PIN you have received by SMS</div>

        <div className="PinSection" onClick={handlePincodeClick}>
          <div className="Pintitle">PIN</div>
          <div className="Pincode">
            {Array.from({ length: 6 }, (_, index) => (
              <div className="code" key={index}>
                {pinCode[index] !== undefined ? pinCode[index] : ""}
              </div>
            ))}
            <input
              ref={inputRef}
              type="text"
              value={pinCode ? pinCode.join("") : ""}
              onChange={handleInputChange}
              onFocus={handleInputFocus}
              // onBlur={handleInputBlur}
              style={{ opacity: 0, position: "absolute", left: "-10000px" }}
            />
          </div>
        </div>

        <div
          id={`${getSpinnerLoader ? "opacityNone" : ""}`}
          className={`${
            !getBtnDesign ? "continueSection" : "continueSectionFocused"
          }`}
        >
          <button
            id="ContinueBtn"
            className="btnCont"
            onClick={handlePayNow}
            disabled={pinCode.length !== 6}
          >
            Continue
          </button>
          {getPinWrong && (
            <p style={{ color: "red" }}>Unable to proceed, kindly try again.</p>
          )}
        </div>
      </div>
    </>
  );
};

export default MyBill;
