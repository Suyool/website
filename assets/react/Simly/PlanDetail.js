import axios from "axios";
import React, { useEffect, useState } from "react";
import { Spinner } from "react-bootstrap";

const PlanDetail = ({ parameters, selectedPlan, selectedPackage, setBackLink, getDataGetting, setDataGetting, setErrorModal, setSuccessModal, setModalName, setModalShow, setSpinnerLoader, getSpinnerLoader }) => {
  const [isViewNetwork, setIsViewNetwork] = useState(false);
  const [getNetwork, setNetwork] = useState(null);
  useEffect(() => {
    setDataGetting("");
    console.log(selectedPlan);
    console.log(selectedPackage);
    setBackLink("");
  }, []);

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

  console.log(JSON.stringify(getNetwork));

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
    </>
  );
};

export default PlanDetail;
