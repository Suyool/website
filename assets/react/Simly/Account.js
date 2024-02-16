import React, { useEffect, useState } from "react";

const Account = ({ setHeaderTitle, setBackLink }) => {
  useEffect(() => {
    setHeaderTitle("My eSim Account");
    setBackLink("");
  }, []);
  return (
    <div className="accountcomp">
      <div className="accountCard">
        <div className="titleaccount">
          <img src="/build/images/simlyIcon.svg" />
          <span>United States of America</span>
        </div>
        <div className="rechargable">
          <div class="single-chart">
            <svg viewBox="0 0 36 36" class="circular-chart green">
              <path
                class="circle-bg"
                d="M18 2.0845
          a 15.9155 15.9155 0 0 1 0 31.831
          a 15.9155 15.9155 0 0 1 0 -31.831"
              />
              <path
                class="circle"
                stroke-dasharray="90, 100"
                d="M18 2.0845
          a 15.9155 15.9155 0 0 1 0 31.831
          a 15.9155 15.9155 0 0 1 0 -31.831"
              />
              <text x="18" y="20.35" class="percentage">
                20GB
              </text>
            </svg>
            <div className="used">used from 20.0 GB</div>
          </div>
          <div className="radio">
            <input type="radio" id="eSim" name="eSim" value="eSim" checked disabled />
            <label className="esim">
              eSim is still valid
            </label>
            <br />
            <input type="radio" id="plans" name="plans" value="plan" disabled />
            <label className="esim">
              Plan has been fully used
            </label>
          </div>
        </div>
        <div className="btns">
          <div className="topup">
            <button className="btntopup">Top up</button>
          </div>
          <div className="details">
            <button className="btntopup">Details</button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Account;
