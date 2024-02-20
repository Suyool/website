import React, { useEffect, useState } from "react";
import axios from "axios";
import { Spinner } from "react-bootstrap";

const MyBill = ({
  parameters,
  getLandlineMobile,
  getLandlineDisplayedData,
  getLandlineData,
  setModalShow,
  setModalName,
  setSuccessModal,
  setErrorModal,
  setActiveButton,
  setHeaderTitle,
  setBackLink,
  getDataGetting,
}) => {
  console.log(parameters?.deviceType);
  console.log(getLandlineDisplayedData)
  const [ isButtonDisabled, setIsButtonDisabled ] = useState(false);
  const [ getSpinnerLoader, setSpinnerLoader ] = useState(false);
  const [ getdisplayedFees, setdisplayedFees ] = useState("");

  useEffect(() => {
    setHeaderTitle("Pay Landline Bill");
    setBackLink("PayBill");
    setIsButtonDisabled(false);
  }, []);

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
        .post("/ogero/landline/pay", {
          LandlineId: getLandlineData.id,
        })
        .then((response) => {
          console.log(response);
          const jsonResponse = response?.data?.message;
          setdisplayedFees(response?.data?.displayedFees);
          console.log(getdisplayedFees);
          setSpinnerLoader(false);
          if (response.data?.IsSuccess) {
            var TotalAmount = parseInt(response.data?.data.amount)+parseInt(response.data?.data.fees)
            setModalName("SuccessModal");
            setSuccessModal({
              imgPath: "/build/images/Ogero/SuccessImg.png",
              title: "Ogero Landline Bill Paid Successfully",
              desc: `You have successfully paid your Ogero Landline bill of L.L ${parseInt(
                TotalAmount
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
                imgPath: "/build/images/Ogero/ErrorImg.png",
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
                imgPath: "/build/images/Ogero/ErrorImg.png",
                title: jsonResponse.Title,
                desc: jsonResponse.SubTitle,
                path: jsonResponse.ButtonOne.Flag,
                btn: jsonResponse.ButtonOne.Text,
              });
              setModalShow(true);
            } else {
              setModalName("ErrorModal");
              setErrorModal({
                imgPath: "/build/images/alfa/error.png",
                title: "Please Try again",
                desc: `You cannot purchase now`,
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
    }
  }, [ getDataGetting ]);

  return (
    <div id="MyBill" className="hideBack">
      {getSpinnerLoader && (
        <div id="spinnerLoader">
          <Spinner className="spinner" animation="border" variant="secondary" />
        </div>
      )}

      {getSpinnerLoader ? (
        <></>
      ) : (
        <div id="PaymentConfirmationSection">
          <div className="topSection">
            <div className="brBoucket"></div>
            <div className="titles">
              <div className="titleGrid">Payment Confirmation</div>
              <button
                onClick={() => {
                  setActiveButton({ name: "PayBill" });
                }}
              >
                Cancel
              </button>
            </div>
          </div>

          <div className="bodySection">
            <div className="cardSec">
              <img src="/build/images/Ogero/OgeroLogo.png" alt="flag" />
              <div className="method">Ogero Landline Bill Payment</div>
            </div>

            <div className="MoreInfo">
              <div className="label">Landline Number</div>
              <div className="value">+961 {getLandlineMobile}</div>
            </div>

            <div className="br"></div>

            <div className="MoreInfo">
              <div className="label">Amount in L.L</div>
              <div className="value1">
                L.L{" "}
                {parseInt(
                  getLandlineDisplayedData.Amount1
                ).toLocaleString()}
              </div>
            </div>

            <div className="MoreInfo">
              <div className="label">Fees in L.L</div>
              <div className="value1">
                L.L {parseInt(getLandlineDisplayedData.OgeroFees).toLocaleString()}
              </div>
            </div>

            <div className="br"></div>

            <div className="MoreInfo">
              <div className="label">Total</div>
              <div className="value2">
                L.L{" "}
                {parseInt(
                  getLandlineDisplayedData.TotalAmount
                ).toLocaleString()}
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
    </div>
  );
};

export default MyBill;
