import axios from "axios";
import React, { useEffect, useState } from "react";
import { Spinner } from "react-bootstrap";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const MyBundle = () => {
  const dispatch = useDispatch();
  const parameters = useSelector((state) => state.appData.parameters);
  const mobileResponse = useSelector((state) => state.appData.mobileResponse);
  const getPrepaidVoucher = useSelector((state) => state.appData.prepaidData.prepaidVoucher);

  const [getPaymentConfirmation, setPaymentConfirmation] = useState(false);
  const [getSerialToClipboard, setSerialToClipboard] = useState("");
  const [isButtonDisabled, setIsButtonDisabled] = useState(false);
  const [getSpinnerLoader, setSpinnerLoader] = useState(false);

  useEffect(() => {
    dispatch(settingData({ field: "headerData", value: { title: "Re-charge Alfa", backLink: "ReCharge", currentPage: "MyBundle" } }));
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
    console.log(JSON.stringify(object));
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
        window.webkit.messageHandlers.callbackHandler.postMessage("fingerprint");
      }, 2000);
    }
  };

  useEffect(() => {
    if (mobileResponse == "success") {
      axios
        .post("/alfa/BuyPrePaid", {
          Token: "",
          category: "ALFA",
          // category: getPrepaidVoucher.vouchercategory,
          desc: getPrepaidVoucher.desc,
          type: getPrepaidVoucher.vouchertype,
          amountLBP: getPrepaidVoucher.priceLBP,
          amountUSD: getPrepaidVoucher.priceUSD,
        })
        .then((response) => {
          setSpinnerLoader(false);
          const jsonResponse = response?.data?.message;
          console.log(jsonResponse);
          // console.log()
          if (response?.data.IsSuccess) {
            setPaymentConfirmation(true);
            setSerialToClipboard("*14*" + response?.data?.data?.voucherCode + "#");
          } else {
            console.log(response.data.flagCode);
            if (response.data.IsSuccess == false && response.data.flagCode == 10) {
              dispatch(
                settingData({
                  field: "modalData",
                  value: {
                    isShow: true,
                    name: "ErrorModal",
                    img: "/build/images/alfa/error.png",
                    title: jsonResponse.Title,
                    desc: jsonResponse.SubTitle,
                    btn: jsonResponse.ButtonOne.Text,
                    flag: jsonResponse.ButtonOne.Flag,
                  },
                })
              );
            } else if (!response.data.IsSuccess && response.data.flagCode == 11) {
              dispatch(
                settingData({
                  field: "modalData",
                  value: {
                    isShow: true,
                    name: "ErrorModal",
                    img: "/build/images/alfa/error.png",
                    title: jsonResponse.Title,
                    desc: jsonResponse.SubTitle,
                    btn: jsonResponse.ButtonOne.Text,
                    flag: jsonResponse.ButtonOne.Flag,
                  },
                })
              );
            } else if (jsonResponse == "19") {
              dispatch(
                settingData({
                  field: "modalData",
                  value: {
                    isShow: true,
                    name: "ErrorModal",
                    img: "/build/images/alfa/error.png",
                    title: "Recharge Card Unavailable ",
                    desc: `The ${getPrepaidVoucher.priceUSD}$ Alfa Recharge card is unavailable. 
                    Kindly choose another one.`,
                    btn: "OK",
                    flag: "",
                  },
                })
              );
            } else if (!response.data.IsSuccess && response.data.flagCode == 210) {
              dispatch(
                settingData({
                  field: "modalData",
                  value: {
                    isShow: true,
                    name: "ErrorModal",
                    img: "/build/images/alfa/error.png",
                    title: response.data.Title,
                    desc: response.data.message,
                    btn: "OK",
                    flag: "",
                  },
                })
              );
            } else {
              dispatch(
                settingData({
                  field: "modalData",
                  value: {
                    isShow: true,
                    name: "ErrorModal",
                    img: "/build/images/alfa/error.png",
                    title: "Please Try again",
                    desc: "You cannot purchase now",
                    btn: "OK",
                    flag: "",
                  },
                })
              );
            }
          }
        })
        .catch((error) => {
          setSpinnerLoader(false);
          console.log(error);
        });
    } else if (mobileResponse == "failed") {
      setSpinnerLoader(false);
      setIsButtonDisabled(false);
      dispatch(settingData({ field: "mobileResponse", value: "" }));
    }
  });

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
                    dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "MyBundle" }));
                    setPaymentConfirmation(false);
                  }}
                >
                  Cancel
                </button>
              </div>
            </div>

            <div className="bodySection">
              <img className="SuccessImg" src="/build/images/alfa/SuccessImg.png" alt="Bundle" />
              <div className="bigTitle">Payment Successful</div>
              <div className="descriptio">You have successfully purchased the ${getPrepaidVoucher.priceUSD} Alfa recharge card.</div>

              <div className="br"></div>

              <div className="copyTitle">To recharge your prepaid number: </div>
              <div className="copyDesc">Copy the 14-digit secret code below</div>

              <button
                className="copySerialBtn"
                onClick={() => {
                  handleShare(getSerialToClipboard);
                }}
              >
                <div></div>
                <div className="serial">{getSerialToClipboard}</div>
                <img className="copySerial" src="/build/images/alfa/copySerial.png" alt="copySerial" />
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
                  <div className="textStep">Your mobile prepaid line is now recharged</div>
                </div>
              </div>
            </div>
          </div>
        </>
      )}
      <div id="MyBundle" className={`${getPaymentConfirmation || getSpinnerLoader ? "hideBackk" : ""}`}>
        {getSpinnerLoader && (
          <div id="spinnerLoader">
            <Spinner className="spinner" animation="border" variant="secondary" />
          </div>
        )}
        {!getPaymentConfirmation && (
          <>
            <div className="MyBundleBody">
              <div className="mainTitle">{getPrepaidVoucher.desc1}</div>
              {/* <div className="mainDesc">*All taxes excluded</div> */}
              <img className="BundleBigImg" src={`/build/images/alfa/Bundle${getPrepaidVoucher.vouchertype}h.png`} alt="Bundle" />
              <div className="smlDesc">
                <img className="question" src={`/build/images/alfa/attention.svg`} alt="question" style={{ verticalAlign: "baseline" }} />
                &nbsp; Alfa only accepts payments in L.L
              </div>
              {/* <div className="relatedInfo">{getPrepaidVoucher.desc2}</div> */}
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
                <div className="label">Amount in L.L</div>
                <div className="value">L.L {parseInt(getPrepaidVoucher.priceLBP).toLocaleString()}</div>
              </div>
              <div className="br"></div>
              <div className="MoreInfo">
                <div className="label">Total amount to pay</div>
                <div className="value1">L.L {parseInt(getPrepaidVoucher.priceLBP).toLocaleString()}</div>
              </div>
              <div className="smlDescSayrafa">$1 = {parseInt(getPrepaidVoucher.sayrafa).toLocaleString()} L.L (Subject to change).</div>
            </div>

            <button id="ContinueBtn" className="btnCont" onClick={handleConfirmPay} disabled={isButtonDisabled}>
              Pay Now
            </button>
          </>
        )}
      </div>
    </>
  );
};

export default MyBundle;
