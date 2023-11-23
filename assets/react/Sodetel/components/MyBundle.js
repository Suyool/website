import axios from "axios";
import React, { useEffect, useState } from "react";
import { Spinner } from "react-bootstrap";
import {capitalizeFirstLetters} from "../../functions";

const MyBundle = ({
  setDataGetting,
  parameters,
  getDataGetting,
  getPrepaidVoucher,
  activeButton,
  setActiveButton,
  setHeaderTitle,
  setBackLink,
  setModalDesc,
  credential
}) => {
  useEffect(() => {
    setHeaderTitle(`Re-charge ${capitalizeFirstLetters(activeButton?.bundle)} Package`);
    setBackLink("Services");
    setIsButtonDisabled(false);
  }, []);
  const [getPaymentConfirmation, setPaymentConfirmation] = useState(false);
  const [getSerialToClipboard, setSerialToClipboard] = useState("");
  const [isButtonDisabled, setIsButtonDisabled] = useState(false);
  const [getSpinnerLoader, setSpinnerLoader] = useState(false);

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
      window.AndroidInterface.callbackHandler(JSON.stringify(object));
    } else if (parameters?.deviceType === "Iphone") {
      window.webkit.messageHandlers.callbackHandler.postMessage(object);
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
      console.log("prepaid",getPrepaidVoucher);
      axios
        .post("/sodetel/refill", {
          // category: "ALFA",
          // category: getPrepaidVoucher.vouchercategory,
          // desc: getPrepaidVoucher.desc,
          // type: getPrepaidVoucher.vouchertype,
          // amountLBP: getPrepaidVoucher.priceLBP,
          // amountUSD: getPrepaidVoucher.priceUSD,
          refillData: getPrepaidVoucher,
          bundle: activeButton?.bundle,
          identifier: credential[credential.name]
        })
        .then((response) => {
          setSpinnerLoader(false);
          const jsonResponse = response?.data?.message;
          if (response?.data.IsSuccess) {
            setPaymentConfirmation(true);
            setSerialToClipboard(
              "*14*" + response?.data?.data?.voucherCode + "#"
            );
          } else {
            if (
              response.data.IsSuccess == false &&
              response.data.flagCode == 10
            ) {
              setModalDesc({
                name : "ErrorModal",
                imgPath: "/build/images/alfa/error.png",
                title: jsonResponse.Title,
                description: jsonResponse.SubTitle,
                show: true,
                btn: jsonResponse.ButtonOne.Text,
                path: jsonResponse.ButtonOne.Flag,
              })
            } else if (
              !response.data.IsSuccess &&
              response.data.flagCode == 11
            ) {
              setModalDesc({
                name : "ErrorModal",
                imgPath: "/build/images/alfa/error.png",
                title: jsonResponse.Title,
                description: jsonResponse.SubTitle,
                show: true,
                btn: jsonResponse.ButtonOne.Text,
                path: jsonResponse.ButtonOne.Flag,
              });
            } else if (jsonResponse == "19") {
              setModalDesc({
                name : "ErrorModal",
                imgPath: "/build/images/alfa/error.png",
                title: "Recharge Card Unavailable ",
                description: `The ${getPrepaidVoucher.priceUSD}$ Alfa Recharge card is unavailable.
                Kindly choose another one.`,
                show: true,
                btn: "OK",
              });
            } else {
              setModalDesc({
                name: "ErrorModal",
                imgPath: "/build/images/alfa/error.png",
                title: "Please Try again",
                description: "You cannot purchase now",
                show: true,
                btn: "OK",
              });
            }
          }
        })
        .catch((error) => {
          setSpinnerLoader(false);
        });
    } else if (getDataGetting == "failed") {
      setSpinnerLoader(false);
      setIsButtonDisabled(false);
      setDataGetting("");
    }
  });

  console.log(credential)

  const copyToClipboard = () => {
    const tempInput = document.createElement("input");
    tempInput.value = getSerialToClipboard;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
  };

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
                    setActiveButton({...activeButton, name: "MyBundle" });
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
                src="/build/images/alfa/SuccessImg.png"
                alt="Bundle"
              />
              <div className="bigTitle">Payment Successful</div>
              <div className="descriptio">
                You have successfully purchased the {activeButton.bundle} {getPrepaidVoucher.plandescription} service.
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
              <div className="mainTitle">{getPrepaidVoucher.desc1}</div>
              {/* <div className="mainDesc">*All taxes excluded</div> */}
              {/*<img*/}
              {/*  className="BundleBigImg"*/}
              {/*  src={`/build/images/alfa/Bundle${getPrepaidVoucher.vouchertype}h.png`}*/}
              {/*  alt="Bundle"*/}
              {/*/>*/}

              <div className="relatedInfo">{getPrepaidVoucher?.plandescription}</div>
              <div className="smlDesc">
                <img
                    className="question"
                    src={`/build/images/alfa/attention.svg`}
                    alt="question"
                    style={{ verticalAlign: "baseline" }}
                />
                Sodetel only accepts payments in L.L
              </div>

              <div className="MoreInfo">
                <div className="label">Total before taxes</div>
                <div className="value">L.L {getPrepaidVoucher?.priceht}</div>
              </div>
              {/*<div className="MoreInfo">*/}
              {/*  <div className="label">+V.A.T & Stamp Duty</div>*/}
              {/*  <div className="value">$ { getPrepaidVoucher.pricettc - getPrepaidVoucher.priceht}</div>*/}
              {/*</div>*/}
              <div className="br"></div>
              <div className="MoreInfo">
                <div className="label">Total after taxes</div>
                <div className="value">L.L {getPrepaidVoucher?.pricettc}</div>
              </div>
              <div className="MoreInfo">
                <div className="label">+V.A.T & Stamp Duty</div>
                <div className="value">L.L {getPrepaidVoucher?.pricettc - getPrepaidVoucher?.priceht}</div>
              </div>
              <div className="br"></div>
              <div className="MoreInfo">
                <div className="label">Total amount to pay</div>
                <div className="value1">
                  L.L {parseInt(getPrepaidVoucher.pricettc).toLocaleString()}
                </div>
              </div>
              <div className="smlDescSayrafa">
                $1 = {parseInt(getPrepaidVoucher.sayrafa).toLocaleString()} L.L (Sayrafa rate, subject to change).
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
