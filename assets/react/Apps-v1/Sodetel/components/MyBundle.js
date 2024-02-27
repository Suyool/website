import axios from "axios";
import React, { useEffect, useState } from "react";
import { Spinner } from "react-bootstrap";
import {capitalizeFirstLetters} from "../../../functions";

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
  credential,
  identifier
}) => {
  useEffect(() => {
    setHeaderTitle(`Re-charge ${capitalizeFirstLetters(activeButton?.bundle)} Package`);
    setBackLink("Services");
    setIsButtonDisabled(false);
  }, []);
  const [getPaymentConfirmation, setPaymentConfirmation] = useState(false);
  const [getSerialToClipboard, setSerialToClipboard] = useState("");
  const [sodetelPassword, setSodetelPassword] = useState("");
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
      axios
        .post("/sodetel/refill", {
          refillData: getPrepaidVoucher,
          bundle: activeButton?.bundle,
          // identifier: credential[credential.name]?.replace(/\s/g, '')
          identifier: identifier,
          requestId: activeButton?.requestId,
        })
        .then((response) => {
          setSpinnerLoader(false);
          // const jsonResponse = JSON.parse(response?.data?.message);
          if (response?.data.IsSuccess) {
            setPaymentConfirmation(true);
            setSerialToClipboard(response?.data?.data?.id);
            setSodetelPassword(response?.data?.data?.password);
          } else {
            if (
              response.data?.IsSuccess === false &&
              (response.data?.flagCode === 10 || response.data.flagCode === 11)
            ) {
              const message = JSON.parse(response.data?.message);
              setModalDesc({
                name : "ErrorModal",
                imgPath: "/build/images/alfa/error.png",
                title: message.Title,
                description: message.SubTitle,
                show: true,
                btn: message?.ButtonOne?.Text,
                path: message?.ButtonOne.Flag,
              })
            } else if (
              !response.data.IsSuccess &&
              response.data.flagCode === 11
            ) {
              setModalDesc({
                name : "ErrorModal",
                imgPath: "/build/images/alfa/error.png",
                title: response.Title,
                description: response.SubTitle,
                show: true,
                btn: response.ButtonOne.Text,
                path: response.ButtonOne.Flag,
              });
            } else if (!response.data.IsSuccess &&
                response.data.data === -1) {
              setModalDesc({
                name : "ErrorModal",
                imgPath: "/build/images/alfa/error.png",
                title: "Recharge Card Unavailable ",
                description: `The ${getPrepaidVoucher?.plandescription} Sodetel Recharge Service is unavailable.`,
                show: true,
                btn: "OK",
              });
            } else {
              setModalDesc({
                name: "ErrorModal",
                imgPath: "/build/images/alfa/error.png",
                title: "Please Try again",
                description: "You cannot purchase this product now",
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

  const copyToClipboard = (value) => {
    const tempInput = document.createElement("input");
    tempInput.value = value;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
  };

  return (
    <>
      {getPaymentConfirmation && (
          <div id="PaymentConfirmationPrePaid">
            <div className="topSection">
              <div className="brBoucket"></div>
              <div className="titles">
                <div className="titleGrid"></div>
                <button
                  onClick={() => {
                    setActiveButton({...activeButton, name: "Default" });
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
                You have successfully purchased the {capitalizeFirstLetters(activeButton.bundle)} {getPrepaidVoucher.plandescription} package.
              </div>

              <div className="br"></div>

              <div className="copyTitle">To recharge your package:</div>
              <div className="copyDesc">Use the credentials below</div>

              <div className="copyDesc mt-3">ID</div>
              <button className="copySerialBtn" onClick={()=>copyToClipboard(getSerialToClipboard)}>
                <div className="serial">{getSerialToClipboard}</div>
                <img
                  className="copySerial"
                  src="/build/images/alfa/copySerial.png"
                  alt="copySerial"
                />
              </button>

              <div className="copyDesc">Password</div>
              <button className="copySerialBtn" onClick={()=>copyToClipboard(sodetelPassword)}>
                <div className="serial">{sodetelPassword}</div>
                <img
                    className="copySerial"
                    src="/build/images/alfa/copySerial.png"
                    alt="copySerial"
                />
              </button>
            </div>
          </div>
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
              <div className="mainTitle">The package related to your {credential.label} is:</div>
              <div className="bundleTitle">{getPrepaidVoucher?.plandescription}</div>

              {/* <div className="mainDesc">*All taxes excluded</div> */}
              <img
                className="BundleBigImg"
                src={`/build/images/sodetel/${getPrepaidVoucher.plancode}.svg`}
                alt="Bundle"
              />
              <div className="smlDesc">
                <img
                    className="question"
                    src={`/build/images/alfa/attention.svg`}
                    alt="question"
                    style={{ verticalAlign: "baseline" }}
                />
               &nbsp; Sodetel only accepts payments in LBP
              </div>

              <div className="MoreInfo">
                <div className="label">Total before taxes</div>
                <div className="value">L.L {parseInt(getPrepaidVoucher.priceht).toLocaleString()}</div>
              </div>
              <div className="MoreInfo">
                <div className="label">+V.A.T & Stamp Duty</div>
                <div className="value">L.L {parseInt(getPrepaidVoucher?.price - getPrepaidVoucher?.priceht).toLocaleString()}</div>
              </div>
              <div className="br"></div>
              <div className="MoreInfo">
                <div className="label">Total after taxes</div>
                <div className="value">L.L {parseInt(getPrepaidVoucher.price).toLocaleString()}</div>
              </div>

              <div className="br"></div>
              <div className="MoreInfo">
                <div className="label">Total amount to pay</div>
                <div className="value1">
                  L.L {parseInt(getPrepaidVoucher.price).toLocaleString()}
                </div>
              </div>
              <div className="smlDescSayrafa">
                $1 = {parseInt(getPrepaidVoucher.sayrafa).toLocaleString()} L.L (Subject to change).
              </div>
            </div>

            <button
              id="ContinueBtn"
              className="btnCont"
              onClick={handleConfirmPay}
              disabled={isButtonDisabled}
            >
              Re-charge Package
            </button>
          </>
        )}
      </div>
    </>
  );
};

export default MyBundle;
