import React, { useEffect, useState } from "react";

const PackagesInfo = ({setBackLink}) => {
    useEffect(() => {
        setBackLink("");
    }, []);

    const handlePay = () => {
        console.log("buy")
    }
  return (
    <div className="packagesinfo">
      <div className="logo">
        <img src="/build/images/simlyIcon.svg" />
      </div>
      <div className="title">Global Package</div>
      <div className="card">
        <div className="data">
          <div className="title2">Data</div>
          <div className="info">1 GB</div>
        </div>
        <div className="bd"></div>
        <div className="price">
          <div className="price2">Price</div>
          <div className="info">$3.5</div>
        </div>
      </div>
      <div className="valid">
        Valid for <span>7 Days</span>
      </div>
      <div className="works">Works in</div>
      <div className="country">France, Italy ...</div>
      <div className="information">
        <div className="network">
          <div className="info">Network</div>
          <div className="about">View All</div>
        </div>
        <div className="network">
          <div className="info">Plan Type</div>
          <div className="about">Data Only</div>
        </div>
        <div className="network">
          <div className="info">Top Up</div>
          <div className="about">Available</div>
        </div>
      </div>
      <div className="policy">Activation Policy</div>
      <div className="validation">
        The Validity period starts earn the SIM connects to any supported
        networks
      </div>
      <div className="pay">
        <button className="payactivate" onClick={handlePay}>Pay & Activate</button>
      </div>
    </div>
  );
};

export default PackagesInfo;
