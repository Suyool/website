import React, { useEffect } from "react";
import { useDispatch } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const PackageItems = ({ country }) => {
  const dispatch = useDispatch();

  const handleCardClick = (plan, packages) => {
    dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "PackagesInfo" }));
    dispatch(settingObjectData({ mainField: "simlyData", field: "SelectedPlan", value: plan }));
    dispatch(settingObjectData({ mainField: "simlyData", field: "SelectedPackage", value: packages }));
  };

  return (
    <div className="container itemsPackageCont">
      <div>
        <div className="subTitle2">{country?.name}</div>
        <div className="row">
          {country.plans.map((packageItem, index) => (
            <div key={packageItem.planId} className="col-md-6">
              {/* <div className={`card mb-3 bg-package${(index % 3) + 1}`} onClick={() => handleCardClick(country, packageItem)}> */}
              <div className={`card mb-3 bg-package1`} onClick={() => handleCardClick(country, packageItem)}>
                <div className="card-body">
                  <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                    <div className="itemsList">
                      <h6 className="card-title">
                        <img src={country.countryImageURL} alt={country.name} width={17} />
                        <span className="ms-2" style={{ fontFamily: "PoppinsMedium" }}>
                          {country?.name}
                        </span>
                      </h6>
                      <p className="card-text itemSize">{packageItem.size}GB</p>
                      <p className="card-text desc">Valid for {packageItem.duration} Days</p>
                    </div>
                    <div>
                      <p className="card-text price">${packageItem.initial_price}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default PackageItems;
