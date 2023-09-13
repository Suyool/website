import React, { useEffect, useState } from "react";
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

  const [ pinCode, setPinCode ] = useState([]);
  const [ getResponseId, setResponseId ] = useState(null);
  const [ getSpinnerLoader, setSpinnerLoader ] = useState(false);
  const [ getDisplayData, setDisplayData ] = useState([]);
  const [ getdisplayedFees, setdisplayedFees ] = useState("");
  const [ getPaymentConfirmation, setPaymentConfirmation ] = useState(false);
  const [ isButtonDisabled, setIsButtonDisabled ] = useState(false);
  const [ getPinWrong, setPinWrong ] = useState(false);

  const handleNbClick = (num) => {
    if (pinCode.length < 4) {
      setPinCode([ ...pinCode, num ]);
    }
  };

  const handleDelete = () => {
    if (pinCode.length > 0) {
      setPinCode(pinCode.slice(0, -1));
    }
  };

  const handlePayNow = () => {
    if (pinCode.length === 4) {
      setSpinnerLoader(true);
      axios
        .post("/touch/bill/RetrieveResults", {
          mobileNumber: localStorage
            .getItem("billMobileNumber")
            .replace(/\s/g, ""),
          // currency: localStorage.getItem("billcurrency"),
          currency: "LBP",
          Pin: pinCode,
          invoicesId: getPostpaidData.id,
        })
        .then((response) => {
          console.log(response);
          if (response.data?.isSuccess) {
            setSpinnerLoader(false);
            setDisplayData(response?.data?.displayData);
            setdisplayedFees(response?.data?.displayedFees);
            setPaymentConfirmation(true);
            setResponseId(response?.data?.postpayed);
          } else if (response.data?.errorCode == "213") {
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
          }
        })
        .catch((error) => {
          console.log(error);
        });
    }
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
        .post("/touch/bill/pay", {
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
              title: "Touch Bill Paid Successfully",
              desc: `You have successfully paid your Touch bill of L.L ${parseInt(
                response.data?.data.amount
              ).toLocaleString()}.`,
            });
            setModalShow(true);
          } else {
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
  }, [ getDataGetting ]);

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
              <img src="/build/images/touch/touchLogo.png" alt="flag" />
              <div className="method">Touch Bill Payment</div>
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

            {/* <div className="taxes">*All taxes included</div> */}

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
        id="MyBill"
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

        <div
          id={`${getSpinnerLoader ? "opacityNone" : ""}`}
          className="continueSection"
        >
          <button
            id="ContinueBtn"
            className="btnCont"
            onClick={handlePayNow}
            disabled={pinCode.length !== 4}
          >
            Continue
          </button>
          {getPinWrong && (
            <p style={{ color: "red" }}>Unable to proceed, kindly try again.</p>
          )}

          <div className="keybord">
            <button className="keyBtn" onClick={() => handleNbClick(1)}>
              1
            </button>
            <button className="keyBtn" onClick={() => handleNbClick(2)}>
              2
            </button>
            <button className="keyBtn" onClick={() => handleNbClick(3)}>
              3
            </button>
            <button className="keyBtn" onClick={() => handleNbClick(4)}>
              4
            </button>
            <button className="keyBtn" onClick={() => handleNbClick(5)}>
              5
            </button>
            <button className="keyBtn" onClick={() => handleNbClick(6)}>
              6
            </button>
            <button className="keyBtn" onClick={() => handleNbClick(7)}>
              7
            </button>
            <button className="keyBtn" onClick={() => handleNbClick(8)}>
              8
            </button>
            <button className="keyBtn" onClick={() => handleNbClick(9)}>
              9
            </button>
            <button className="keyBtn"></button>
            <button className="keyBtn" onClick={() => handleNbClick(0)}>
              0
            </button>
            <button className="keyBtn" onClick={handleDelete}>
              <img src="/build/images/touch/clearNb.png" alt="flag" />
            </button>
          </div>
        </div>
      </div>
    </>
  );
};

export default MyBill;
