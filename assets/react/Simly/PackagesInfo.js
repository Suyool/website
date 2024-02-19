import React, { useEffect } from "react";

const PackagesInfo = ({ country, plan }) => {
    useEffect(() => {
    }, []);

    const handlePay = () => {
        console.log("buy");
    };

    return (
        <div className="packagesinfo">
            <div className="logo">
                <img src={country.countryImageURL} alt={country.name} />
            </div>
            <div className="title">{country.name} Package</div>
            <div className="card">
                <div className="data">
                    <div className="title2">Data</div>
                    <div className="info">{plan.size} GB</div>
                </div>
                <div className="bd"></div>
                <div className="price">
                    <div className="price2">Price</div>
                    <div className="info">${plan.price}</div>
                </div>
            </div>
            <div className="valid">
                Valid for <span>{plan.duration} Days</span>
            </div>
            <div className="works">Works in</div>
            <div className="country">{country.name}</div>
            <div className="information">
                <div className="network">
                    <div className="info">Network</div>
                    <div className="about">{plan.apn}</div>
                </div>
                <div className="network">
                    <div className="info">Plan Type</div>
                    <div className="about">{plan.planType}</div>
                </div>
                <div className="network">
                    <div className="info">Top Up</div>
                    <div className="about">{plan.topup ? "Available" : "Not Available"}</div>
                </div>
            </div>
            <div className="policy">Activation Policy</div>
            <div className="validation">{plan.activationPolicy}</div>
            <div className="pay">
                <button className="payactivate" onClick={handlePay}>Pay & Activate</button>
            </div>
        </div>
    );
};

export default PackagesInfo;
