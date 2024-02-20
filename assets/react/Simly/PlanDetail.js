import axios from "axios";
import React, { useEffect, useState } from "react";

const PlanDetail = ({ getEsimDetail, setBackLink }) => {
  const [getPlanDetail, setPlanDetail] = useState(null);
  const [isViewNetwork, setIsViewNetwork] = useState(false);
  // console.log(getEsimDetail);
  useEffect(() => {
    setBackLink("");
    axios
      .post("/simly/GetEsimDetails", {
        esimId: getEsimDetail?.esimId,
      })
      .then((response) => {
        setPlanDetail(response.data.message);
        // console.log(response.data.message);
      });
  }, []);

  return (
    <>
      {getPlanDetail ? (
        <>
          <div id={isViewNetwork ? "hideBackk" : ""} className="packagesinfo">
            <div className="logo">
              <img src={getPlanDetail.countryImage ? getPlanDetail.countryImage : "/build/images/simlyIcon.svg"} alt={getPlanDetail?.country} />
            </div>
            <div className="title">{getPlanDetail?.country} Package</div>
            <div className="card">
              <div className="data">
                <div className="title2">Data</div>
                <div className="info">{getPlanDetail?.DataUsage?.sim?.size} GB</div>
              </div>
              <div className="bd"></div>
              <div className="price">
                <div className="price2">Price</div>
                <div className="info">${getPlanDetail?.simlyPlan?.initial_price}</div>
              </div>
            </div>
            <div className="valid">
              Valid for <span>{getPlanDetail?.simlyPlan?.duration} Days</span>
            </div>
            <div className="works">Works in</div>
            <div className="country">{getPlanDetail?.country}</div>
            <div className="information">
              <div className="network">
                <div className="info">Network</div>
                <div className="about">
                  <span onClick={() => setIsViewNetwork(true)}>View All</span>
                </div>
              </div>
              <div className="network">
                <div className="info">Plan Type</div>
                <div className="about">{getPlanDetail?.simlyPlan?.planType}</div>
              </div>
              <div className="network">
                <div className="info">Top Up</div>
                <div className="about">{getPlanDetail?.simlyPlan?.topup ? "Available" : "Not Available"}</div>
              </div>
            </div>
            <div className="policy">Activation Policy</div>
            <div className="validation">{getPlanDetail?.simlyPlan?.activationPolicy}</div>
          </div>

          {isViewNetwork && (
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
                    <img src={getPlanDetail.countryImage ? getPlanDetail.countryImage : "/build/images/simlyIcon.svg"} alt="flag" />
                    <div className="method">
                      <div className="body">
                        {getPlanDetail?.NetworkAvailable[0]?.supported_networks?.map((network, index) => (
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
      ) : (
        <></>
      )}
    </>
  );
};

export default PlanDetail;
