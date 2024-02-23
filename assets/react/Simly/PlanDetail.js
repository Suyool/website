import axios from "axios";
import React, { useEffect, useState } from "react";

const PlanDetail = ({ getEsimDetail, setBackLink }) => {
  const [getPlanDetail, setPlanDetail] = useState(null);
  const [isViewNetwork, setIsViewNetwork] = useState(false);
  const [getCountry, setCountry] = useState(null);
  const [isViewCountry, setIsViewCountry] = useState(false);

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
  console.log(getEsimDetail);

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
                  <div className="single-chart">
                    <svg viewBox="0 0 36 36" className={`circular-chart ${getPlanDetail?.DataUsage?.sim?.status === "FULLY_USED" ? "violet" : "green"}`}>
                      <path
                        className="circle-bg"
                        d="M18 2.0845
       a 15.9155 15.9155 0 0 1 0 31.831
       a 15.9155 15.9155 0 0 1 0 -31.831"
                      />
                      <path
                        className="circle"
                        strokeDasharray={`${getPlanDetail?.DataUsage?.sim?.consumedPercentage}, 100`}
                        d="M18 2.0845
       a 15.9155 15.9155 0 0 1 0 31.831
       a 15.9155 15.9155 0 0 1 0 -31.831"
                      />
                      <text x="18" y="20.35" className="percentage">
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
            <div className="br"></div>

            <div className="valid">
              <div className="label">Valid for</div>
              {/* <div className="value">{getPlanDetail?.simlyPlan?.duration} Days</div> */}
              <div className="value">{getPlanDetail?.DataUsage?.plan?.daysLeft} Days</div>
            </div>

            <div className="valid">
              <div className="label">Works in</div>
              <div className="value">{getPlanDetail?.country}</div>
            </div>
            {getEsimDetail?.PlanType != "Local" && (
              <div className="valid" style={{ paddingTop: "unset" }}>
                <div className="label"></div>
                <div className="value3" onClick={() => handleViewCountry(getEsimDetail?.isoCode)}>
                  <span>View Countries</span>
                </div>
              </div>
            )}

            <div className="br"></div>

            <div className="valid">
              <div className="label">Initial Plan Price</div>
              <div className="value1">${getPlanDetail?.simlyPlan?.initial_price}</div>
            </div>

            <div className="valid">
              <div className="label">Initial Plan Size</div>
              <div className="value1">{getPlanDetail?.simlyPlan?.size} GB</div>
            </div>

            <div className="valid">
              <div className="label">Plan Type</div>
              <div className="value1">{getPlanDetail?.simlyPlan?.planType}</div>
            </div>

            <div className="valid">
              <div className="label">Top Up</div>
              <div className="value1">{getPlanDetail?.simlyPlan?.topup ? "Available" : "Not Available"}</div>
            </div>

            <div className="valid">
              <div className="label">Network</div>
              <div className="value3">
                <span onClick={() => setIsViewNetwork(true)}>View All</span>
              </div>
            </div>
          </div>

          {isViewNetwork && (
            <>
              <div id="backHid"></div>

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
                            <div style={{ color: "black" }}>{network.name}</div>
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
      {isViewCountry && Array.isArray(getCountry) && (
        <>
          <div id="backHid"></div>

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
                    {getCountry[0][getEsimDetail?.isoCode]?.map((country, index) => (
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

export default PlanDetail;
