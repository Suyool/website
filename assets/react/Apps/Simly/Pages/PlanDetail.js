import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import AppAPI from "../Api/AppAPI";
import { settingObjectData } from "../Redux/Slices/AppSlice";

const PlanDetail = () => {
  const dispatch = useDispatch();
  const {GetEsimDetails,GetNetworksById,GetCountriesById,GetPlansUsingISOCode} = AppAPI();
  const simlyData = useSelector((state) => state.appData.simlyData);
  const planDetail = useSelector((state) => state.appData.planDetail);
  // console.log(getEsimDetail);
  useEffect(() => {
    dispatch(settingObjectData({ mainField: "headerData", field: "backLink", value: "Account" }));
  GetEsimDetails(simlyData.esimId);
  },[]);

  return (
    <>
      {planDetail ? (
        <>
          <div className="packagesinfo">
            <div className="logo">
              <img src={planDetail.countryImage ? planDetail.countryImage : "/build/images/simlyIcon.svg"} alt={planDetail?.country} />
            </div>
            <div className="title">{planDetail?.country} Package</div>
            <div className="accountcomp">
              <div className="accountCard plandetail">
                <div className="rechargable">
                  <div className="single-chart">
                    <svg viewBox="0 0 36 36" className={`circular-chart ${planDetail?.DataUsage?.sim?.status === "FULLY_USED" ? "violet" : "green"}`}>
                      <path
                        className="circle-bg"
                        d="M18 2.0845
       a 15.9155 15.9155 0 0 1 0 31.831
       a 15.9155 15.9155 0 0 1 0 -31.831"
                      />
                      <path
                        className="circle"
                        strokeDasharray={`${planDetail?.DataUsage?.sim?.consumedPercentage}, 100`}
                        d="M18 2.0845
       a 15.9155 15.9155 0 0 1 0 31.831
       a 15.9155 15.9155 0 0 1 0 -31.831"
                      />
                      <text x="18" y="20.35" className="percentage">
                        {planDetail?.DataUsage?.sim?.consumed}GB
                      </text>
                    </svg>
                    <div className="used">used from {planDetail?.DataUsage?.sim?.size} GB</div>
                  </div>
                  <div className="radio">
                    <input type="checkbox" id="eSim" name="eSim" value="eSim" checked={planDetail?.DataUsage?.sim?.status !== "FULLY_USED"} disabled />
                    <label className="esim">eSim is still valid</label>
                    <br />
                    <input type="checkbox" id="plans" name="plans" value="plan" checked={planDetail?.DataUsage?.sim?.status === "FULLY_USED"} disabled />
                    <label className="esim">Plan has been fully used</label>
                  </div>
                </div>
              </div>
            </div>
            <div className="br"></div>

            <div className="valid">
              <div className="label">Valid for</div>
              {/* <div className="value">{getPlanDetail?.simlyPlan?.duration} Days</div> */}
              <div className="value">
                {planDetail?.simlyPlan[0].offre ? (
                    "24h"
                ) : (
                    `${planDetail?.DataUsage?.plan?.daysLeft} Days`
                )}
              </div>
            </div>

            <div className="valid">
              <div className="label">Works in</div>
              <div className="value">{planDetail?.country}</div>
            </div>
            {simlyData?.eSimDetail?.PlanType != "Local" && (
              <div className="valid" style={{ paddingTop: "unset" }}>
                <div className="label"></div>
                <div className="value3" onClick={() => GetCountriesById(simlyData?.eSimDetail?.isoCode)}>
                  <span>View Countries</span>
                </div>
              </div>
            )}

            <div className="br"></div>

            <div className="valid">
              <div className="label">Initial Plan Price</div>
              <div className="value1">
                {planDetail?.simlyPlan[0].offre ? (
                    "Free"
                ) : (
                    `${planDetail?.simlyPlan[0]?.initial_price}`
                )}
              </div>
            </div>

            <div className="valid">
              <div className="label">Initial Plan Size</div>
              <div className="value1">{planDetail?.simlyPlan[0]?.size} GB</div>
            </div>

            <div className="valid">
              <div className="label">Plan Type</div>
              <div className="value1">{planDetail?.simlyPlan[0]?.planType}</div>
            </div>

            <div className="valid">
              <div className="label">Top Up</div>
              <div className="value1">{planDetail?.simlyPlan[0]?.topup ? "Available" : "Not Available"}</div>
            </div>

            <div className="valid">
              <div className="label">Network</div>
              <div className="value3">
                <span onClick={() => GetNetworksById(simlyData?.SelectedPackage)}>View All</span>
              </div>
            </div>
          </div>
        </>
      ) : (
        <></>
      )}
    </>
  );
};

export default PlanDetail;
