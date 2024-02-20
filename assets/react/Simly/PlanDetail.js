import axios from "axios";
import React, { useEffect, useState } from "react";

const PlanDetail = ({ getEsimDetail, setBackLink }) => {
  const [getPlanDetail, setPlanDetail] = useState(null);
  const [isViewNetwork, setIsViewNetwork] = useState(false);
  // console.log(getEsimDetail);
  useEffect(() => {
    setBackLink("Account");
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
            <div className="accountcomp">
              <div className="accountCard plandetail">
                <div className="rechargable">
                  <div class="single-chart">
                    <svg viewBox="0 0 36 36" className={`circular-chart ${getPlanDetail?.DataUsage?.sim?.status === "FULLY_USED" ? "violet" : "green"}`}>
                      <path
                        class="circle-bg"
                        d="M18 2.0845
       a 15.9155 15.9155 0 0 1 0 31.831
       a 15.9155 15.9155 0 0 1 0 -31.831"
                      />
                      <path
                        class="circle"
                        stroke-dasharray={`${getPlanDetail?.DataUsage?.sim?.consumedPercentage}, 100`}
                        d="M18 2.0845
       a 15.9155 15.9155 0 0 1 0 31.831
       a 15.9155 15.9155 0 0 1 0 -31.831"
                      />
                      <text x="18" y="20.35" class="percentage">
                        {getPlanDetail?.DataUsage?.sim?.consumed}GB
                      </text>
                    </svg>
                    <div className="used">used from {getPlanDetail?.DataUsage?.sim?.size} GB</div>
                  </div>
                  <div className="radio">
                    <input type="checkbox" id="eSim" name="eSim" value="eSim" checked={getPlanDetail?.DataUsage?.sim?.status !== "FULLY_USED"} disabled />
                    <label className="esim">eSim is still valid</label>
                    <br />
                    <input type="checkbox" id="plans" name="plans" value="plan" checked={getPlanDetail?.DataUsage?.sim?.status === "FULLY_USED"} disabled />
                    <label className="esim">Plan has been fully used</label>
                  </div>
                </div>
              </div>
            </div>
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
              Initial duration <span>{getPlanDetail?.simlyPlan?.duration} Days</span>
            </div>
            <div className="valid">
              Days left <span>{getPlanDetail?.DataUsage?.plan?.daysLeft} Days</span>
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
