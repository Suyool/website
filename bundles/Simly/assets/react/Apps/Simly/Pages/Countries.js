import React from "react";

const Countries = ({filteredData, handleClick,displaycontinent }) => {
    return (
        <>
            <div style={{ width: "100%" }}>
                <div className="row ps-3">
                    <div className="col">
                        <div className="card-columns continent-card-container">
                            {filteredData?.map((continentObj, index) => (
                                <div key={index} className="continent-container">
                                    {Object.keys(continentObj)?.map((continent) => (
                                        <React.Fragment key={continent}>
                                            <div className="title">
                                                {displaycontinent(continent)}
                                            </div>
                                            <div className="country-scroll-container">
                                                <div className="row flex-nowrap">
                                                    {continentObj[continent].map(
                                                        (country, idx) => (
                                                            <div className="imgText" key={idx}>
                                                                <div key={idx} className="">
                                                                    <div
                                                                        className="card countryCard"
                                                                        onClick={() =>
                                                                            handleClick(country.isoCode)
                                                                        }
                                                                    >
                                                                        <div className="card-body">
                                                                            <img
                                                                                src={country.countryImageURL}
                                                                                alt={country.name}
                                                                                width={50}
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <h5 className="card-title">
                                                                    {country.name}
                                                                </h5>
                                                            </div>
                                                        )
                                                    )}
                                                </div>
                                            </div>
                                        </React.Fragment>
                                    ))}
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default Countries;
