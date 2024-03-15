import React from "react";

const Global = ({ simlyData, handleClick }) => {
    return (
        <div className="row" style={{ margin: "0 10px", width: "100%" }}>
            <div className="col">
                {simlyData?.AvailableCountries &&
                simlyData?.AvailableCountries.global &&
                simlyData?.AvailableCountries.global.map((globalItem, index) => (
                    <div
                        key={index}
                        className="card mb-3"
                        onClick={() => handleClick(globalItem.isoCode)}
                    >
                        <div className="card-body">
                            <div id="Topp">
                                <img
                                    src={globalItem.countryImageURL}
                                    alt={globalItem.name}
                                    width={50}
                                />
                                <div className="noTopp">
                                    <div className="card-title">
                                        {globalItem.name} Packages
                                    </div>
                                    <p className="card-text">
                                        {globalItem.destinations} destinations
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default Global;
