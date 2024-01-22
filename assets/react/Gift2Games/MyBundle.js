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
  useEffect(() => {
    setHeaderTitle("Buy Product");
    setBackLink("");
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
      axios
        .post("/gift2games/product/pay", {
          Token: "",
          category: "Gift2Games",
          // category: getPrepaidVoucher.vouchercategory,
          desc: getPrepaidVoucher.title,
          amount: getPrepaidVoucher.price,
          currency: getPrepaidVoucher.currency,
          productId: getPrepaidVoucher.productId
        })
        .then((response) => {
          setSpinnerLoader(false);
          const jsonResponse = response?.data?.message;
          if (response?.data.IsSuccess) {
            setPaymentConfirmation(true);
            setSerialToClipboard(
                response?.data?.data?.data?.serialCode
            );
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
            } else if (jsonResponse == "19") {
              setModalName("ErrorModal");
              setErrorModal({
                img: "/build/images/alfa/error.png",
                title: "Recharge Card Unavailable ",
                desc: `The ${getPrepaidVoucher.priceUSD}$ Alfa Recharge card is unavailable. 
              Kindly choose another one.`,
                // path: response.data.path,
                btn: "OK",
              });
              setModalShow(true);
            } else {
              setModalName("ErrorModal");
              setErrorModal({
                img: "/build/images/alfa/error.png",
                title: "Please Try again",
                desc: "You cannot purchase now",
                // path: response.data.path,
                btn: "OK",
              });
              setModalShow(true);
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
  }, [getDataGetting]);

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
                src="/build/images/alfa/SuccessImg.png"
                alt="Bundle"
              />
              <div className="bigTitle">Payment Successful</div>
              <div className="descriptio">
                You have successfully purchased the {getPrepaidVoucher.title} at $
                {getPrepaidVoucher.price}.
              </div>
              <div className="br"></div>

              <div className="copyTitle">To recharge your Voucher: </div>
              <div className="copyDesc">
                Copy the secret serial code below
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
              {/* <div className="mainDesc">*All taxes excluded</div> */}
              <img
                className="BundleBigImg"
                src={getPrepaidVoucher.image}
                alt="Bundle"
              />
              <div className="smlDesc">
                <img
                  className="question"
                  src={`/build/images/alfa/attention.svg`}
                  alt="question"
                  style={{ verticalAlign: "baseline" }}
                />
                &nbsp;
                Gift2Games only accepts payments in $
              </div>
              {/* <div className="relatedInfo">{getPrepaidVoucher.desc2}</div> */}

              <div className="br"></div>
              <div className="MoreInfo">
                <div className="label">Total after taxes</div>
                <div className="value">$ {getPrepaidVoucher.price}</div>
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
