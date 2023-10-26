import axios from "axios";
import React, { useEffect, useState } from "react";
import { Spinner } from "react-bootstrap";

const MyBundle = ({
  setDataGetting,
  parameters,
  getDataGetting,
  getPrepaidVoucher,
  setModalShow,
  setModalName,
  setSuccessModal,
  setErrorModal,
  setActiveButton,
  setHeaderTitle,
  setBackLink,
}) => {
  const [getPaymentConfirmation, setPaymentConfirmation] = useState(false);
  const [getSerialToClipboard, setSerialToClipboard] = useState("");
  const [isButtonDisabled, setIsButtonDisabled] = useState(false);
  const [getSpinnerLoader, setSpinnerLoader] = useState(false);

  useEffect(() => {
    setHeaderTitle("Re-charge Touch");
    setBackLink("ReCharge");
    setIsButtonDisabled(false);
  }, []);

  const handleShare = (shareCode) => {
    let object = [
      {
        Share: {
          share: "share",
          text: shareCode,
        },
      },
    ];
    if (parameters?.deviceType === "Android") {
      setTimeout(() => {
        window.AndroidInterface.callbackHandler(JSON.stringify(object));
      }, 2000);
    } else if (parameters?.deviceType === "Iphone") {
      setTimeout(() => {
        window.webkit.messageHandlers.callbackHandler.postMessage(object);
      }, 2000);
    }
  };

  const handleConfirmPay = () => {
    setSpinnerLoader(true);
    setIsButtonDisabled(true);
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
        .post("/touch/BuyPrePaid", {
          Token: "",
          category: "MTC",
          desc: getPrepaidVoucher.desc,
          type: getPrepaidVoucher.vouchertype,
          amountLBP: getPrepaidVoucher.priceLBP,
          amountUSD: getPrepaidVoucher.priceUSD,
        })
        .then((response) => {
          setSpinnerLoader(false);
          const jsonResponse = response?.data?.message;
          console.log(jsonResponse);
          if (response?.data.IsSuccess) {
            setPaymentConfirmation(true);
            setSerialToClipboard(
              "*200*" + response?.data?.data?.voucherCode + "#"
            );
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
            } else if (jsonResponse == "19") {
              setModalName("ErrorModal");
              setErrorModal({
                img: "/build/images/alfa/error.png",
                title: "Recharge Card Unavailable ",
                desc: `The ${getPrepaidVoucher.priceUSD}$ Touch Recharge card is unavailable. 
              Kindly choose another one.`,
                btn: "OK",
              });
              setModalShow(true);
            } else {
              setModalName("ErrorModal");
              setErrorModal({
                img: "/build/images/alfa/error.png",
                title: "Please Try again",
                desc: "you cannot purchase now",
                btn: "OK",
              });
              setModalShow(true);
            }
          }
        })
        .catch((error) => {
          setSpinnerLoader(false);
          console.log(error);
        });
    } else if (getDataGetting == "failed") {
      setSpinnerLoader(false);
      setIsButtonDisabled(false);
      setDataGetting("");
    }
  });

  const copyToClipboard = () => {
    const tempInput = document.createElement("input");
    tempInput.value = getSerialToClipboard;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
  };

  // console.log(getPrepaidVoucher);
  return (
    <>
      {getPaymentConfirmation && (
        <>
          <div id="PaymentConfirmationPrePaid">
            <div className="topSection">
              <div className="brBoucket"></div>
              <div className="titles">
                <div className="titleGrid"></div>
                <button
                  onClick={() => {
                    setActiveButton({ name: "MyBundle" });
                    setPaymentConfirmation(false);
                  }}
                >
                  Cancel
                </button>
              </div>
            </div>

            <div className="bodySection">
              <img
                className="SuccessImg"
                src="/build/images/touch/SuccessImg.png"
                alt="Bundle"
              />
              <div className="bigTitle">Payment Successful</div>
              <div className="descriptio">
                You have successfully purchased the $
                {getPrepaidVoucher.priceUSD} Touch recharge card.
              </div>

              <div className="br"></div>

              <div className="copyTitle">To recharge your prepaid number: </div>
              <div className="copyDesc">
                Copy the 14-digit secret code below
              </div>

              <button className="copySerialBtn" onClick={copyToClipboard}>
                <div></div>
                <div className="serial">{getSerialToClipboard}</div>
                <img
                  className="copySerial"
                  src="/build/images/alfa/copySerial.png"
                  alt="copySerial"
                />
              </button>

              <button
                id="ContinueBtn"
                className="mt-3"
                onClick={() => {
                  handleShare(getSerialToClipboard);
                }}
              >
                Share Code
              </button>

              <div className="stepsToRecharge">
                <div className="steps">
                  <div className="dot"></div>
                  <div className="textStep">Go to your phone tab</div>
                </div>
                <div className="steps">
                  <div className="dot"></div>
                  <div className="textStep">Paste the code</div>
                </div>
                <div className="steps">
                  <div className="dot"></div>
                  <div className="textStep">Tap Call</div>
                </div>
                <div className="steps">
                  <div className="dot"></div>
                  <div className="textStep">
                    Your mobile prepaid line is now recharged
                  </div>
                </div>
              </div>
            </div>
          </div>
        </>
      )}
      <div
        id="MyBundle"
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
        {!getPaymentConfirmation && (
          <>
            <div className="MyBundleBody">
              <div className="mainTitle">{getPrepaidVoucher.desc3}</div>
              {/* <div className="mainDesc">*All taxes excluded</div> */}
              <img
                className="BundleBigImg"
                src={`/build/images/touch/Bundle${getPrepaidVoucher.vouchertype}h.png`}
                alt="Bundle"
              />

              <div className="smlDesc">
                <img
                  className="question"
                  src={`/build/images/alfa/attention.svg`}
                  alt="question"
                  style={{ verticalAlign: "baseline" }}
                />
                Touch only accepts payments in L.L
              </div>
              {/* <div className="relatedInfo">{getPrepaidVoucher.desc1}</div> */}
              <div className="MoreInfo">
                <div className="label">Total before taxes</div>
                <div className="value">$ {getPrepaidVoucher.beforeTaxes}</div>
              </div>
              <div className="MoreInfo">
                <div className="label">+V.A.T & Stamp Duty</div>
                <div className="value">$ {getPrepaidVoucher.fees}</div>
              </div>
              <div className="br"></div>
              <div className="MoreInfo">
                <div className="label">Total after taxes</div>
                <div className="value">$ {getPrepaidVoucher.priceUSD}</div>
              </div>
              <div className="MoreInfo">
                <div className="label">Amount in L.L (Sayrafa rate)</div>
                <div className="value">
                  L.L {parseInt(getPrepaidVoucher.priceLBP).toLocaleString()}
                </div>
              </div>
              <div className="br"></div>
              <div className="MoreInfo">
                <div className="label">Total amount to pay</div>
                <div className="value1">
                  L.L {parseInt(getPrepaidVoucher.priceLBP).toLocaleString()}
                </div>
              </div>
              <div className="smlDesc">
                $1 = {parseInt(getPrepaidVoucher.sayrafa).toLocaleString()} L.L
                as per Sayrafa rate, subject to change on payment day.
              </div>
            </div>

            <button
              id="ContinueBtn"
              className="btnCont"
              onClick={handleConfirmPay}
              disabled={isButtonDisabled}
            >
              Pay Now
            </button>
          </>
        )}
      </div>
    </>
  );
};

export default MyBundle;
