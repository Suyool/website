import React, { useEffect } from "react";

const PackagesInfo = ({ selectedPlan,selectedPackage,setBackLink }) => {
    useEffect(() => {
        // console.log(selectedPlan)
        // console.log(selectedPackage)
        setBackLink("Packages")
    }, []);

    const handlePay = () => {
        console.log("buy");
    };

    return (
        <div className="packagesinfo">
            <div className="logo">
                <img src={selectedPlan.countryImageURL} alt={selectedPlan.name} />
            </div>
            <div className="title">{selectedPlan.name} Package</div>
            <div className="card">
                <div className="data">
                    <div className="title2">Data</div>
                    <div className="info">{selectedPackage.size} GB</div>
                </div>
                <div className="bd"></div>
                <div className="price">
                    <div className="price2">Price</div>
                    <div className="info">${selectedPackage.price}</div>
                </div>
            </div>
            <div className="valid">
                Valid for <span>{selectedPackage.duration} Days</span>
            </div>
            <div className="works">Works in</div>
            <div className="country">{selectedPlan.name}</div>
            <div className="information">
                <div className="network">
                    <div className="info">Network</div>
                    <div className="about">{selectedPackage.apn}</div>
                </div>
                <div className="network">
                    <div className="info">Plan Type</div>
                    <div className="about">{selectedPackage.planType}</div>
                </div>
                <div className="network">
                    <div className="info">Top Up</div>
                    <div className="about">{selectedPackage.topup ? "Available" : "Not Available"}</div>
                </div>
            </div>
            <div className="policy">Activation Policy</div>
            <div className="validation">{selectedPackage.activationPolicy}</div>
            <div className="pay">
                <button className="payactivate" onClick={handlePay}>Pay & Activate</button>
            </div>
        </div>
    );
};

export default PackagesInfo;
