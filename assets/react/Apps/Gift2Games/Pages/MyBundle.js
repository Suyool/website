import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { settingData } from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";
import {Spinner} from "react-bootstrap";

const MyBundle = () => {
  const dispatch = useDispatch();
  const { pay } = AppAPI();
  const parameters = useSelector((state) => state.appData.parameters);
  const mobileResponse = useSelector((state) => state.appData.mobileResponse);
  const getPrepaidVoucher = useSelector((state) => state.appData.productInfo);
  const [getPaymentConfirmation, setPaymentConfirmation] = useState(false);
  const [getSerialToClipboard, setSerialToClipboard] = useState("");
  const [getSpinnerLoader, setSpinnerLoader] = useState(false);

  const [isButtonDisabled, setIsButtonDisabled] = useState(false);

  useEffect(() => {
    dispatch(settingData({ field: "headerData", value: { title: "Re-charge Touch", backLink: "", currentPage: "MyBundle" } }));
    setIsButtonDisabled(false);
  }, []);

  const handleConfirmPay = () => {
    dispatch(settingData({ field: "isloading", value: true }));
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
      pay(getPrepaidVoucher);
    } else if (mobileResponse == "failed") {
      dispatch(settingData({ field: "isloading", value: false }));
      setIsButtonDisabled(false);
      dispatch(settingData({ field: "mobileResponse", value: "" }));
    }
  }, [mobileResponse]);

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
                          setActiveButton({ name: "" });
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
                    You have successfully purchased the {getPrepaidVoucher.title} for $
                    {getPrepaidVoucher.displayPrice}
                  </div>
                  <div className="br"></div>

                  <div className="copyTitle">To use your voucher:</div>
                  <div className="copyDesc">
                    Copy the code below
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
                      className="mt-4"
                      onClick={() => {
                        handleShare(getSerialToClipboard);
                      }}
                  >
                    Share Code
                  </button>
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
                  <div className="mainTitle">{getPrepaidVoucher.title}</div>
                  <img
                      className="BundleBigImg"
                      src={getPrepaidVoucher.image || getDefaultImage(setTypeID)}
                      alt="Bundle"
                      onError={(e) => {
                        e.target.src = '../build/images/g2g/freefire.png';
                      }}
                  />
                  <div className="smlDesc">
                    <img
                        className="question"
                        src={`/build/images/alfa/attention.svg`}
                        alt="question"
                        style={{ verticalAlign: "baseline" }}
                    />
                    &nbsp;
                    Only payments in USD are accepted.
                  </div>

                  <div className="br"></div>
                  <div className="MoreInfo">
                    <div className="label">Total amount to pay</div>
                    <div className="value">$ {getPrepaidVoucher.price}</div>
                  </div>

                </div>
                <div className="payNowBtnCont">
                  <button
                      id="ContinueBtn"
                      className="btnCont"
                      onClick={handleConfirmPay}
                      disabled={isButtonDisabled}
                  >
                    Pay Now
                  </button>
                </div>
              </>
          )}
        </div>
      </>
  );
};

export default MyBundle;
