import React from "react";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const BottomSlider = () => {
  const dispatch = useDispatch();
  const bottomSlider = useSelector((state) => state.appData.bottomSlider);
  const parameters = useSelector((state) => state.appData.parameters);
  const SelectedPlan = useSelector((state) => state.appData.simlyData.SelectedPlan);
  const Topup = () => {
    dispatch(settingData({ field: "isloading", value: true }));
    dispatch(settingObjectData({ mainField: "bottomSlider", field: "isButtonDisable", value: true }));
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
  return (
    <div id="BottomSliderContainer">
      <div className="topSection">
        <div className="brBoucket"></div>
        <div className="titles">
          <div className="titleGrid">
            {bottomSlider?.name == "availableNetworks" && <>Supported Networks</>}
            {bottomSlider?.name == "availableCountries" && <>Supported Countries</>}
            {bottomSlider?.name == "isViewNetwork" && <>Top Up E-Sim</>}
          </div>
          <button
            onClick={() => {
              dispatch(settingData({ field: "bottomSlider", value: { isShow: false, name: "", data: {} } }));
            }}
          >
            Cancel
          </button>
        </div>
      </div>

      <div className="bodySection">
        {bottomSlider?.name == "availableNetworks" && (
          <div id={bottomSlider?.name}>
            <div className="cardSec">
              <img src={bottomSlider?.data.networks[0]?.countryImageURL} alt="flag" />
              <div className="method">
                <div className="body">
                  {bottomSlider?.data.networks[0]?.supported_networks?.map((network, index) => (
                    <div className="plan" key={index}>
                      <div style={{ color: "black" }}>{network.name}</div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
            <div className="footSectionPick">
              <button
                onClick={() => {
                  dispatch(settingData({ field: "bottomSlider", value: { isShow: false, name: "", data: {} } }));
                }}
              >
                Got it
              </button>
            </div>
          </div>
        )}
        {bottomSlider?.name == "availableCountries" && (
          <div id={bottomSlider?.name}>
            <div className="cardSec">
              <div className="method">
                <div className="bodyCountry">
                  {bottomSlider?.data.countryInfo[0][SelectedPlan.isoCode]?.map((country, index) => (
                    <div className="plan" key={index}>
                      <img src={country.countryImageURL} alt="flag" />
                      <div className="name">{country.name}</div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
            <div className="footSectionPick">
              <button
                onClick={() => {
                  dispatch(settingData({ field: "bottomSlider", value: { isShow: false, name: "", data: {} } }));
                }}
              >
                Got it
              </button>
            </div>
          </div>
        )}
        {bottomSlider?.name == "isViewNetwork" && (
          <div id={bottomSlider?.name}>
            <div className="cardSec">
              <img src={bottomSlider?.data?.countryImage} alt="flag" />
              <div className="method">
                <div className="bodyToTopup">
                  <div className="country">{bottomSlider?.data?.country}</div>
                  <div className="data">Data only</div>
                  <div className="line"></div>
                  <div className="country">Top Up</div>
                  <div className="data">{bottomSlider?.data?.gb}GB</div>
                  <div className="line"></div>
                  <div className="country">Amount</div>
                  <div className="data">${bottomSlider?.data?.amount}</div>
                  <div className="line"></div>
                </div>
              </div>
            </div>
            <div className="footSectionPick" style={{color: "#00ADFF"}} >
              <button
                onClick={() => {
                  Topup();
                }}
                disabled={bottomSlider.isButtonDisable}
              >
                Confirm & TopUp
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default BottomSlider;
