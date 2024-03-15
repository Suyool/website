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
                    <div className="card-body">
                        <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                            <div className="itemsList">
                                <h6 className="card-title">
                                    <img
                                        width={17} />
                                    <span className="ms-2" style={{ fontFamily: "PoppinsMedium" }}>
                         </span>
                                </h6>
                                <p className="card-text itemSize">
                                    1GB Gift
                                </p>
                                <p className="card-text desc">
                                    Valid for 24hrs
                                </p>
                            </div>
                            <div>
                                <p className="card-text price">
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="card-body">
                        <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                            <div className="itemsList">
                                <h6 className="card-title">
                                    <img
                                        width={17} />
                                    <span className="ms-2" style={{ fontFamily: "PoppinsMedium" }}>
                         </span>
                                </h6>
                                <p className="card-text itemSize">
                                    1GB Gift
                                </p>
                                <p className="card-text desc">
                                    Valid for 24hrs
                                </p>
                            </div>
                            <div>
                                <p className="card-text price">
                                </p>
                            </div>
                        </div>
                    </div>


                    <div className="card-body">
                        <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                            <div className="itemsList">
                                <h6 className="card-title">
                                    <img
                                        width={17} />
                                    <span className="ms-2" style={{ fontFamily: "PoppinsMedium" }}>
                         </span>
                                </h6>
                                <p className="card-text itemSize">
                                    1GB Gift
                                </p>
                                <p className="card-text desc">
                                    Valid for 24hrs
                                </p>
                            </div>
                            <div>
                                <p className="card-text price">
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="card-body">
                        <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                            <div className="itemsList">
                                <h6 className="card-title">
                                    <img
                                        width={17} />
                                    <span className="ms-2" style={{ fontFamily: "PoppinsMedium" }}>
                         </span>
                                </h6>
                                <p className="card-text itemSize">
                                    1GB Gift
                                </p>
                                <p className="card-text desc">
                                    Valid for 24hrs
                                </p>
                            </div>
                            <div>
                                <p className="card-text price">
                                </p>
                            </div>
                        </div>
                    </div>
                    {country?.plans?.map((packageItem, index) => (
                        <>
                            {!packageItem?.isbought && (
                                <div key={packageItem.planId} className="col-md-6">
                                    {/* <div className={`card mb-3 bg-package${(index % 3) + 1}`} onClick={() => handleCardClick(country, packageItem)}> */}
                                    <div className={`card mb-3 bg-package1 ${packageItem?.offre ? 'offre' : ''}`} onClick={() => handleCardClick(country, packageItem)}>
                                        <div className="card-body">
                                            <div style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                                                <div className="itemsList">
                                                    <h6 className="card-title">
                                                        <img src={country.countryImageURL} alt={country.name} width={17} />
                                                        <span className="ms-2" style={{ fontFamily: "PoppinsMedium" }}>
                           {country?.name}
                         </span>
                                                    </h6>
                                                    <p className="card-text itemSize">{packageItem?.offre ? `${packageItem.size}GB` : `${packageItem.size}GB`}</p>
                                                    <p className="card-text desc">{packageItem?.offre ? `Valid for ${packageItem.duration}` : `Valid for ${packageItem.duration} Days`}</p>
                                                </div>
                                                <div>
                                                    <p className="card-text price">{packageItem?.offre ? `${packageItem.initial_price_free}` : `$ ${packageItem.initial_price}`}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default PackageItems;
