import React, { useEffect } from "react";
import AppAPI from "../Api/AppAPI";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const PackagesInfo = () => {
  const dispatch = useDispatch();
  const mobileResponse = useSelector((state) => state.appData.mobileResponse);
  const selectedPlan = useSelector((state) => state.appData.simlyData.SelectedPlan);
  const selectedPackage = useSelector((state) => state.appData.simlyData.SelectedPackage);
  const parameters = useSelector((state) => state.appData.parameters);
  const { PurchaseTopup, GetNetworksById, GetCountriesById } = AppAPI();

  useEffect(() => {
    dispatch(settingData({ field: "mobileResponse", value: "" }));
    dispatch(settingObjectData({ mainField: "headerData", field: "backLink", value: "Packages" }));
  }, []);

  const handlePay = () => {
    dispatch(settingData({ field: "isloading", value: true }));
    setTimeout(() => {
      dispatch(settingData({ field: "mobileResponse", value: "" }));
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
        dispatch(settingData({ field: "mobileResponse", value: message }));
      };
    }, 1000);
  };

  useEffect(() => {
    if (mobileResponse == "success") {
      dispatch(settingData({ field: "mobileResponse", value: "" }));
      PurchaseTopup(selectedPackage, selectedPlan);
    } else if (mobileResponse == "failed") {
      dispatch(settingData({ field: "mobileResponse", value: "" }));
      dispatch(settingData({ field: "isloading", value: false }));
    }
  }, [mobileResponse]);

  const planType = localStorage.getItem("parentPlanType");

  const handleDial = (string) => {
    let object = [
      {
        Dial: {
          Dial: string,
        },
      },
    ];
    if (parameters?.deviceType === "Android") {
      window.AndroidInterface.callbackHandler(JSON.stringify(object));
    } else if (parameters?.deviceType === "Iphone") {
      window.webkit.messageHandlers.callbackHandler.postMessage(object);
    }
  }

  return (
    <>
      <div className="packagesinfo">
        <div className="logo">
          <img src={selectedPlan.countryImageURL} alt={selectedPlan.name} />
        </div>
        <div className="title">{selectedPlan.name} Package</div>

        <div className="cardFor">
          <div className="data">
            <div className="tit">Data</div>
            <div className="desc">{selectedPackage.size} GB</div>
          </div>
          <div className="bd"></div>
          <div className="data">
            <div className="tit">Price</div>
            <div className="desc">{selectedPackage?.offre ? `${selectedPackage.initial_price_free}` : `$ ${selectedPackage.initial_price}`}</div>
          </div>
        </div>

        <div className="Caution mt-1">
          <div className="warImg">
            <img src="/build/images/attentionSign.svg" alt="warning" />
          </div>
          <div className="titlee">Before finalizing your order, make sure your device supports eSIM <span onClick={()=>handleDial("*#06#")}>by dialing *#06#</span>.</div>
        </div>
        
        <div className="valid">
          <div className="label">Valid for</div>
          <div className="value">{selectedPackage?.offre ? `${selectedPackage.duration}` : `${selectedPackage.duration} Days`}</div>
        </div>

        <div className="valid">
          <div className="label">Works in</div>
          <div className="value">{selectedPlan.name}</div>
        </div>

        {(planType == "Regional" || planType == "Global") && (
          <div className="valid" style={{ paddingTop: "unset" }}>
            <div className="label"></div>
            <div className="value3">
              <span
                onClick={() => {
                  GetCountriesById(selectedPlan?.isoCode);
                }}
              >
                View Countries
              </span>
            </div>
          </div>
        )}
        <div className="br"></div>

        <div className="valid">
          <div className="label">Plan Type</div>
          <div className="value1">{selectedPackage.planType}</div>
        </div>

        <div className="valid">
          <div className="label">Top Up</div>
          <div className="value1">{selectedPackage.topup ? "Available" : "Not Available"}</div>
        </div>

        <div className="valid">
          <div className="label">Network</div>
          <div className="value3">
            <span
              onClick={() => {
                GetNetworksById(selectedPackage.planId);
              }}
            >
              View All
            </span>
          </div>
        </div>

        <div className="valid">
          <div className="label">Activation Policy</div>
        </div>
        <div className="data">{selectedPackage?.activationPolicy}</div>

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
    </>
  );
};

export default PackagesInfo;
