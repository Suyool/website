// Regions.js
import React from "react";

const Regions = ({ simlyData, handleClick }) => {
    return (
        <div
            className="row"
            style={{ margin: "0 10px", width: "100%" }}
        >
            <div className="col">
                {simlyData?.AvailableCountries &&
                simlyData?.AvailableCountries.regional &&
                simlyData?.AvailableCountries.regional.map(
                    (region, index) => (
                        <div
                            key={index}
                            className="card mb-3"
                            onClick={() => handleClick(region.isoCode)}
                        >
                            <div className="card-body">
                                <div id="Topp">
                                    <img
                                        src={region.countryImageURL}
                                        alt={region.name}
                                        width={50}
                                    />
                                    <div className="noTopp">
                                        <div className="card-title">
                                            {region.name} Packages
                                        </div>
                                        <p className="card-text">
                                            {region.destinations} destinations
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )
                )}
            </div>
        </div>
    );
};

export default Regions;
