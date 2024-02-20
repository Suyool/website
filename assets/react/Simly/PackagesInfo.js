import axios from "axios";
import React, { useEffect, useState } from "react";
import { Spinner } from "react-bootstrap";

const PackagesInfo = ({ parameters, selectedPlan, selectedPackage, setBackLink, getDataGetting, setDataGetting, setErrorModal, setSuccessModal, setModalName, setModalShow, setSpinnerLoader, getSpinnerLoader }) => {
  useEffect(() => {
    setDataGetting("");
    console.log(selectedPlan);
    console.log(selectedPackage);
    setBackLink("");
  }, []);

  const handlePay = () => {
    setSpinnerLoader(true);
    setTimeout(() => {
      console.log("clicked");
      setDataGetting("");
      if (parameters?.deviceType === "Android") {
        setTimeout(() => {
          window.AndroidInterface.callbackHandler("message");
        }, 2000);
      } else if (parameters?.deviceType === "Iphone") {
        setTimeout(() => {
          window.webkit.messageHandlers.callbackHandler.postMessage("fingerprint");
        }, 2000);
      }
      window.handleCheckout = (message) => {
        setDataGetting(message);
      };
    }, 1000);
  };

  useEffect(() => {
    if (getDataGetting == "success") {
      setDataGetting("");
      axios
        .post("/simly/purchaseTopup", {
          planId: selectedPackage.planId,
          country: selectedPlan.name,
          countryImage: selectedPlan.countryImageURL,
          parentPlanType: localStorage.getItem("parentPlanType"),
        })
        .then((response) => {
          const jsonResponse = response.data.message;
          if (response.data.status) {
            setSpinnerLoader(false);
            setModalName("SuccessModal");
            setSuccessModal({
              imgPath: "/build/images/Loto/success.png",
              title: "Simly Purchased Successfully",
              desc: (
                <div>
                  Please Download the qr
                  <br />
                  <img src={`${response.data.data.qrCodeImageUrl}`} />
                </div>
              ),
              qr: response.data.data.qrCodeImageUrl,
              qrImg: response.data.data.qrCodeString,
              deviceType: parameters?.deviceType,
            });
            setModalShow(true);
            localStorage.removeItem("selectedBalls");
          } else if (!response.data.status && response.data.flagCode == 10) {
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/Loto/error.png",
              title: jsonResponse.Title,
              desc: jsonResponse.SubTitle,
              path: jsonResponse.ButtonOne.Flag,
              btn: jsonResponse.ButtonOne.Text,
            });
            setModalShow(true);
          } else if (!response.data.status && response.data.flagCode == 11) {
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/Loto/error.png",
              title: jsonResponse.Title,
              desc: jsonResponse.SubTitle,
              path: jsonResponse.ButtonOne.Flag,
              btn: jsonResponse.ButtonOne.Text,
            });
            setModalShow(true);
          } else {
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/Loto/error.png",
              title: "Please Try again",
              desc: `You cannot purchase now`,
            });
            setModalShow(true);
          }
        })
        .catch((error) => {
          setSpinnerLoader(false);
          console.log(error);
          setDisabledBtn(selectedBallsToShow == null || JSON.parse(selectedBallsToShow).length === 0);
        });
    } else if (getDataGetting == "failed") {
      setDataGetting("");
      setSpinnerLoader(false);
    }
  }, [getDataGetting]);

  return (
    <>
      <div className={` ${getSpinnerLoader ? "packagesinfo hideBackk" : "packagesinfo"}`}>
        {getSpinnerLoader && (
          <div id="spinnerLoader">
            <Spinner className="spinner" animation="border" variant="secondary" />
          </div>
        )}
        <div className="logo">
          <img src={selectedPlan.countryImageURL} alt={selectedPlan.name} />
        </div>
        <div className="title">{selectedPlan.name} Package</div>
        <div className="card">
          <div className="data">
            <div className="title2">Data</div>
            <div className="info">{selectedPackage.size} GB</div>
          </div>
          <div className="bd"></div>
          <div className="price">
            <div className="price2">Price</div>
            <div className="info">${selectedPackage.price}</div>
          </div>
        </div>
        <div className="valid">
          Valid for <span>{selectedPackage.duration} Days</span>
        </div>
        <div className="works">Works in</div>
        <div className="country">{selectedPlan.name}</div>
        <div className="information">
          <div className="network">
            <div className="info">Network</div>
            <div className="about">{selectedPackage.apn}</div>
          </div>
          <div className="network">
            <div className="info">Plan Type</div>
            <div className="about">{selectedPackage.planType}</div>
          </div>
          <div className="network">
            <div className="info">Top Up</div>
            <div className="about">{selectedPackage.topup ? "Available" : "Not Available"}</div>
          </div>
        </div>
        <div className="policy">Activation Policy</div>
        <div className="validation">{selectedPackage.activationPolicy}</div>
        <div className="pay">
          <button className="payactivate" onClick={()=>{handlePay()}}>
            Pay & Activate
          </button>
        </div>
      </div>
    </>
  );
};

export default PackagesInfo;
