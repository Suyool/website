import axios from "axios";
import React, { useEffect, useState } from "react";
import { Spinner } from "react-bootstrap";

const PackagesInfo = ({ parameters, selectedPlan, selectedPackage, setBackLink, getDataGetting, setDataGetting, setErrorModal, setSuccessModal, setActiveButton, setModalName, setModalShow, setSpinnerLoader, getSpinnerLoader }) => {
  const [isViewNetwork, setIsViewNetwork] = useState(false);
  const [isViewCountry, setIsViewCountry] = useState(false);
  const [getNetwork, setNetwork] = useState(null);
  const [getCountry, setCountry] = useState(null);
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
              title: "eSIM Payment Successful",
              desc: (
                <div>
                  You have successfully purchased the ${selectedPackage.initial_price} {selectedPlan.name} eSIM.
                </div>
              ),
              btn: "Install eSIM",
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
        });
    } else if (getDataGetting == "failed") {
      setDataGetting("");
      setSpinnerLoader(false);
    }
  }, [getDataGetting]);

  const handleViewNetwork = (plan) => {
    setIsViewNetwork(!isViewNetwork);
    axios
      .get(`/simly/getNetworksById?planId=${plan}`)
      .then((response) => {
        setNetwork(response?.data?.message);
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
      });
  };

  const handleViewCountry = (country) => {
    setIsViewCountry(!isViewCountry);
    axios
      .get(`/simly/getContientAvailableByCountry?country=${country}`)
      .then((response) => {
        setCountry(response?.data?.message);
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
      });
  };

  const planType = localStorage.getItem("parentPlanType");

  console.log(getCountry);

  return (
    <>
      <div id={isViewNetwork ? "hideBackk" : ""} className={` ${getSpinnerLoader ? "packagesinfo hideBackk" : "packagesinfo"}`}>
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
            <div className="info">${selectedPackage.initial_price}</div>
          </div>
        </div>
        <div className="valid">
          Valid for <span>{selectedPackage.duration} Days</span>
        </div>
        <div className="works">Works in</div>
        <div className="country">{selectedPlan.name}</div>
        <div className="information">
          {(planType == "Regional" || planType == "Global") && (
            <div className="network">
              <div className="info">Works in</div>
              <div className="about">
                <span onClick={() => handleViewCountry(selectedPlan.isoCode)}>View All</span>
              </div>
            </div>
          )}

          <div className="network">
            <div className="info">Network</div>
            <div className="about">
              <span onClick={() => handleViewNetwork(selectedPackage.planId)}>View All</span>
            </div>
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
          <button
            className="payactivate"
            onClick={() => {
              handlePay();
            }}
          >
            Pay & Activate
          </button>
        </div>
      </div>

      {isViewNetwork && Array.isArray(getNetwork) && (
        <>
          <div id="PaymentConfirmationSection">
            <div className="topSection">
              <div className="brBoucket"></div>
              <div className="titles">
                <div className="titleGrid">Supported Networks</div>
                <button
                  onClick={() => {
                    setIsViewNetwork(false);
                  }}
                >
                  Cancel
                </button>
              </div>
            </div>

            <div className="bodySection">
              <div className="cardSec">
                <img src={getNetwork[0]?.countryImageURL} alt="flag" />
                <div className="method">
                  <div className="body">
                    {getNetwork[0]?.supported_networks?.map((network, index) => (
                      <div className="plan" key={index}>
                        <div>{network.name}</div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </div>

            <div className="footSectionPick">
              <button
                onClick={() => {
                  setIsViewNetwork(false);
                }}
              >
                Got it
              </button>
            </div>
          </div>
        </>
      )}

      {isViewCountry && Array.isArray(getCountry) && (
        <>
          <div id="PaymentConfirmationSection">
            <div className="topSection">
              <div className="brBoucket"></div>
              <div className="titles">
                <div className="titleGrid">Supported Countries</div>
                <button
                  onClick={() => {
                    setIsViewCountry(false);
                  }}
                >
                  Cancel
                </button>
              </div>
            </div>

            <div className="bodySection">
              <div className="cardSec">
                <div className="method">
                  <div className="bodyCountry">
                    {getCountry[0][selectedPlan.isoCode]?.map((country, index) => (
                      <div className="plan" key={index}>
                        <img src={country.countryImageURL} alt="flag" />
                        <div className="name">{country.name}</div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            </div>

            <div className="footSectionPick">
              <button
                onClick={() => {
                  setIsViewCountry(false);
                }}
              >
                Got it
              </button>
            </div>
          </div>
        </>
      )}
    </>
  );
};

export default PackagesInfo;
