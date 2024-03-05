import React from "react";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const BottomSlider = () => {
  const dispatch = useDispatch();
  const bottomSlider = useSelector((state) => state.appData.bottomSlider);
  const parameters = useSelector((state) => state.appData.parameters);

  return (
    <div id="BottomSliderContainer">
      <div className="topSection">
        <div className="brBoucket"></div>
        <div className="titles">
          <div className="titleGrid"></div>
          <button
            onClick={() => {
              dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: bottomSlider?.backPage }));
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
              <img src={bottomSlider?.networks[0]?.countryImageURL} alt="flag" />
              <div className="method">
                <div className="body">
                  {bottomSlider?.networks[0]?.supported_networks?.map((network, index) => (
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
                  {getCountry[0][selectedPlan.isoCode]?.map((country, index) => (
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
      </div>
    </div>
  );
};

export default BottomSlider;
