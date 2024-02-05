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
  selectedProduct
}) => {
  useEffect(() => {
    setHeaderTitle("Re-charge TerraNet");
    setBackLink("ReCharge");
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
        window.webkit.messageHandlers.callbackHandler.postMessage(
          "fingerprint"
        );
      }, 2000);
    }
  };

  const handlePayNow = () => {
    // Make the API request here
    axios
        .post("/terraNet/refill_customer_terranet", {
          productId: getPrepaidVoucher.ProductId,


          // Include any other necessary data
        })
        .then((response) => {
          console.log(response)
          if (response.data?.status) {
            setModalName("SuccessModal");
            setModalShow(true);
            setSuccessModal({
              title: response.data?.message,
              desc: `You have successfully paid your Terranet bill of ${getPrepaidVoucher.Currency} ${getPrepaidVoucher.Price}.`
            })
          } else {
            console.error("Payment error:", response.data);
          }
        })
        .catch((error) => {
          console.error("Payment error:", error);
        });
  };

  useEffect(() => {
    if (getDataGetting == "success") {
      handlePayNow();
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

  return (
    <>
      tst
      {/*{getPaymentConfirmation && (*/}
      {/*  <>*/}
      {/*    <div id="PaymentConfirmationPrePaid">*/}
      {/*      <div className="topSection">*/}
      {/*        <div className="brBoucket"></div>*/}
      {/*        <div className="titles">*/}
      {/*          <div className="titleGrid"></div>*/}
      {/*          <button*/}
      {/*            onClick={() => {*/}
      {/*              setActiveButton({ name: "MyBundle" });*/}
      {/*              setPaymentConfirmation(false);*/}
      {/*            }}*/}
      {/*          >*/}
      {/*            Cancel*/}
      {/*          </button>*/}
      {/*        </div>*/}
      {/*      </div>*/}

      {/*      <div className="bodySection">*/}
      {/*        <img*/}
      {/*          className="SuccessImg"*/}
      {/*          src="/build/images/alfa/SuccessImg.png"*/}
      {/*          alt="Bundle"*/}
      {/*        />*/}
      {/*        <div className="bigTitle">Payment Successful</div>*/}
      {/*        <div className="MoreInfo">*/}
      {/*          <div className="label">price</div>*/}
      {/*          <div className="value">L.L {getPrepaidVoucher.Price}</div>*/}
      {/*        </div>*/}
      {/*        <div className="MoreInfo">*/}
      {/*          <div className="label">description</div>*/}
      {/*          <div className="value">{getPrepaidVoucher.description}</div>*/}
      {/*        </div>*/}


      {/*        <button*/}
      {/*          id="ContinueBtn"*/}
      {/*          className="mt-3"*/}
      {/*          onClick={() => {*/}
      {/*            handleShare(getSerialToClipboard);*/}
      {/*          }}*/}
      {/*        >*/}
      {/*          Share Code*/}
      {/*        </button>*/}
      {/*      </div>*/}
      {/*    </div>*/}
      {/*  </>*/}
      {/*)}*/}
      {/*<div*/}
      {/*  id="MyBundle"*/}
      {/*  className={`${*/}
      {/*    getPaymentConfirmation || getSpinnerLoader ? "hideBackk" : ""*/}
      {/*  }`}*/}
      {/*>*/}
      {/*  {getSpinnerLoader && (*/}
      {/*    <div id="spinnerLoader">*/}
      {/*      <Spinner*/}
      {/*        className="spinner"*/}
      {/*        animation="border"*/}
      {/*        variant="secondary"*/}
      {/*      />*/}
      {/*    </div>*/}
      {/*  )}*/}
      {/*  {!getPaymentConfirmation && (*/}
      {/*    <>*/}
      {/*      <div className="MyBundleBody">*/}
      {/*        <div className="mainTitle">{getPrepaidVoucher.desc1}</div>*/}
      {/*        /!* <div className="mainDesc">*All taxes excluded</div> *!/*/}
      {/*        <img*/}
      {/*          className="BundleBigImg"*/}
      {/*          src={`/build/images/alfa/Bundle${getPrepaidVoucher.vouchertype}h.png`}*/}
      {/*          alt="Bundle"*/}
      {/*        />*/}
      {/*        <div className="smlDesc">*/}
      {/*          <img*/}
      {/*            className="question"*/}
      {/*            src={`/build/images/alfa/attention.svg`}*/}
      {/*            alt="question"*/}
      {/*            style={{ verticalAlign: "baseline" }}*/}
      {/*          />*/}
      {/*          &nbsp;*/}
      {/*          Alfa only accepts payments in L.L*/}
      {/*        </div>*/}
      {/*        /!* <div className="relatedInfo">{getPrepaidVoucher.desc2}</div> *!/*/}
      {/*        <div className="MoreInfo">*/}
      {/*          <div className="label">price</div>*/}
      {/*          <div className="value">L.L {getPrepaidVoucher.Price}</div>*/}
      {/*        </div>*/}
      {/*        <div className="MoreInfo">*/}
      {/*          <div className="label">description</div>*/}
      {/*          <div className="value">{getPrepaidVoucher.Description}</div>*/}
      {/*        </div>*/}
      {/*      </div>*/}

      {/*      <button*/}
      {/*        id="ContinueBtn"*/}
      {/*        className="btnCont"*/}
      {/*        onClick={handleConfirmPay}*/}
      {/*        disabled={isButtonDisabled}*/}
      {/*      >*/}
      {/*        Pay Now*/}
      {/*      </button>*/}
      {/*    </>*/}
      {/*  )}*/}
      {/*</div>*/}
    </>
  );
};

export default MyBundle;
