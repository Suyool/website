import React, { useEffect, useState } from "react";
import ContentLoader from "react-content-loader";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";

const Account = () => {
  const dispatch = useDispatch();
  const parameters = useSelector((state) => state.appData.parameters);
  const mobileResponse = useSelector((state) => state.appData.mobileResponse);
  const isLoadingData = useSelector((state) => state.appData.isLoadingData);
  const isLoading = useSelector((state) => state.appData.isloading);
  const simlyData = useSelector((state) => state.appData.simlyData);

  const {
    GetUsageOfEsim,
    PurchaseTopupEsim,
    GetNetworksById,
    GetPlansUsingISOCode,
  } = AppAPI();

  const [reqObj, setReqObj] = useState({
    planId: "",
    esimId: "",
    countryImage: "",
  });

  useEffect(() => {
    dispatch(
      settingData({
        field: "headerData",
        value: {
          title: "My Global eSIM Account",
          backLink: "Packages",
          currentPage: "Account",
        },
      })
    );
    dispatch(settingData({ field: "mobileResponse", value: "" }));
    GetUsageOfEsim();
  }, []);

  useEffect(() => {
    if (mobileResponse == "success") {
      dispatch(settingData({ field: "mobileResponse", value: "" }));
      PurchaseTopupEsim(reqObj);
    } else if (mobileResponse == "failed") {
      dispatch(settingData({ field: "mobileResponse", value: "" }));
      dispatch(settingData({ field: "isloading", value: false }));
    }
  }, [mobileResponse]);

  return (
    <>
      {/* {isLoadingData && (
        <div className={` ${getSpinnerLoader ? "accountInfo hideBackk" : "accountInfo"}`}>
          <div id="spinnerLoader">
            <Spinner className="spinner" animation="border" variant="secondary" />
          </div>
        </div>
      )} */}
      {isLoadingData ? (
        <div className="mt-5" style={{ margin: "0 10px", width: "100%" }}>
          <ContentLoader
            speed={2}
            width="100%"
            height="90vh"
            backgroundColor="#f3f3f3"
            foregroundColor="#ecebeb"
          >
            <rect x="0" y="0" rx="3" ry="3" width="100%" height="180" />
            <rect x="0" y="210" rx="3" ry="3" width="100%" height="180" />
          </ContentLoader>
        </div>
      ) : (
        <>
          <div style={{ width: "100%" }}>
          {simlyData.accountInformation == null || simlyData.accountInformation.length === 0 && (
                <>
                <div className="ifempty card">
                  <div className="title">
                    <img src="/build/images/simly/card.svg" /><br/>
                    You have no eSIM yet
                  </div>
                  <div className="desc">
                  Once you purchase an eSIM it will appear here.
                  </div>
                </div>
                </>
              )}
            {simlyData.mapData && (
              <>
                {simlyData.accountInformation.map((data, index) => (
                  <div key={index} className="accountcomp">
                    <div className="accountCard">
                      <div className="titleaccount">
                        {data.countryImage ? (
                          <img src={data.countryImage} />
                        ) : (
                          <img src="/build/images/simlyIcon.svg" />
                        )}
                        <span>{data.country}</span>
                      </div>
                      <div className="rechargable">
                        <div className="single-chart">
                          <svg
                            viewBox="0 0 36 36"
                            className={`circular-chart ${
                              data.sim.status === "FULLY_USED"
                                ? "violet"
                                : "green"
                            }`}
                          >
                            <path
                              className="circle-bg"
                              d="M18 2.0845
       a 15.9155 15.9155 0 0 1 0 31.831
       a 15.9155 15.9155 0 0 1 0 -31.831"
                            />
                            <path
                              className="circle"
                              strokeDasharray={`${data.sim.consumedPercentage}, 100`}
                              d="M18 2.0845
       a 15.9155 15.9155 0 0 1 0 31.831
       a 15.9155 15.9155 0 0 1 0 -31.831"
                            />
                            <text x="18" y="20.35" className="percentage">
                              {data.sim.consumed}GB
                            </text>
                          </svg>
                          <div className="used">
                            used from {data.sim.size} GB
                          </div>
                        </div>
                        <div className="radio">
                          <input
                            type="checkbox"
                            id="eSim"
                            name="eSim"
                            value="eSim"
                            checked={data.sim.status !== "FULLY_USED"}
                            disabled
                          />
                          <label className="esim">eSim is still valid</label>
                          <br />
                          <input
                            type="checkbox"
                            id="plans"
                            name="plans"
                            value="plan"
                            checked={data.sim.status === "FULLY_USED"}
                            disabled
                          />
                          <label className="esim">
                            Plan has been fully used
                          </label>
                        </div>
                      </div>
                      <div className="btns">
                        {
                          data.sim.status !== "REFUNDED" && (
                            <div
                              className={
                                data.sim.status !== "PENDING"
                                  ? "topup"
                                  : "details"
                              }
                            >
                              <button
                                className="btntopup"
                                onClick={() => {
                                  if (data.sim.status !== "TERMINATED") {
                                    GetPlansUsingISOCode(data?.isoCode);
                                    dispatch(
                                      settingData({
                                        field: "headerData",
                                        value: {
                                          title: "Global eSIM",
                                          backLink: "Packages",
                                          currentPage: "Packages",
                                        },
                                      })
                                    );
                                  }else{
                                    dispatch(
                                      settingData({
                                        field: "bottomSlider",
                                        value: {
                                          isShow: true,
                                          name: "isExpired",
                                          backPage: "",
                                          data: {
                                            // gb: data?.sim?.size,
                                            // amount: data?.initialPrice,
                                            // country: data.country,
                                            // esimId: data?.esimId,
                                            // planId: data?.plans,
                                            // countryImage: data?.countryImage,
                                          },
                                          isButtonDisable: false,
                                          expiredimage: "/build/images/simly/expired.svg"
                                        },
                                      })
                                    );
                                  }

                                  // setReqObj({
                                  //   esimId: data?.esimId,
                                  //   planId: data?.plans,
                                  //   countryImage: data?.countryImage,
                                  // });
                                    
                                }}
                                disabled={
                                  data.sim.status === "REFUNDED"
                                }
                              >
                                Top up
                              </button>
                            </div>
                          )}

                        <div
                          className={
                            data.sim.status === "PENDING" ? "topup" : "details"
                          }
                        >
                          <button
                            className="btntopup"
                            onClick={() => {
                              dispatch(
                                settingObjectData({
                                  mainField: "simlyData",
                                  field: "esimId",
                                  value: data.esimId,
                                })
                              );
                              dispatch(
                                settingObjectData({
                                  mainField: "simlyData",
                                  field: "eSimDetail",
                                  value: data,
                                })
                              );
                              dispatch(
                                settingObjectData({
                                  mainField: "simlyData",
                                  field: "SelectedPackage",
                                  value: data?.plans,
                                })
                              );
                              dispatch(
                                settingObjectData({
                                  mainField: "headerData",
                                  field: "currentPage",
                                  value:
                                    data.sim.status === "PENDING"
                                      ? "RechargeThePayment"
                                      : "PlanDetail",
                                })
                              );
                              // if (data.sim.status !== "PENDING") {
                              //   localStorage.setItem("qrImage", data.qrCodeImage);
                              //   localStorage.setItem("qrString", data.qrCodeString);
                              // }
                            }}
                            disabled={data.sim.status === "REFUNDED"}
                          >
                            {data.sim.status === "PENDING"
                              ? "Install"
                              : data.sim.status === "REFUNDED"
                              ? "Refunded"
                              : "Details"}
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </>
            )}
          </div>
        </>
      )}
    </>
  );
};

export default Account;
