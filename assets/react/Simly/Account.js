import axios from "axios";
import React, { useEffect, useState } from "react";

const Account = ({ setHeaderTitle, setBackLink }) => {
  const [getGegabytes, setGegabytes] = useState();
  const [getUsedGegabytes, setUsedGegabytes] = useState();
  const [getAccountInformation, setAccountInformation] = useState();
  const [getMap, setMap] = useState(false);

  useEffect(() => {
    setHeaderTitle("My eSim Account");
    setBackLink("");

    axios
      .post("/simly/getUsageOfEsim")
      .then((response) => {
        setMap(true);
        setAccountInformation(response.data.message);
      })
      .catch((error) => {
        console.log(error);
      });
  }, []);

  console.log(getAccountInformation);

  return (
    <>
      {getMap && (
        <>
          {getAccountInformation.map((data, index) => (
            <div key={index} className="accountcomp">
              <div className="accountCard">
                <div className="titleaccount">
                  {data.countryImage ? (
                    <img src={data.countryImage} />
                  ) : (
                    <img src="/build/images/simlyIcon.svg" />
                  )}
                  <span>{data.country}</span>
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
                        stroke-dasharray={`${data.sim.consumedPercentage}, 100`}
                        d="M18 2.0845
       a 15.9155 15.9155 0 0 1 0 31.831
       a 15.9155 15.9155 0 0 1 0 -31.831"
                      />
                      <text x="18" y="20.35" class="percentage">
                        {data.sim.consumed}GB
                      </text>
                    </svg>
                    <div className="used">used from {data.sim.size} GB</div>
                  </div>
                  <div className="radio">
                    <input
                      type="checkbox"
                      id="eSim"
                      name="eSim"
                      value="eSim"
                      checked={data.sim.size !== data.sim.consumed}
                      disabled
                    />
                    <label className="esim">eSim is still valid</label>
                    <br />
                    <input
                      type="checkbox"
                      id="plans"
                      name="plans"
                      value="plan"
                      checked={data.sim.size === data.sim.consumed}
                      disabled
                    />
                    <label className="esim">Plan has been fully used</label>
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
          ))}
        </>
      )}
    </>
  );
};

export default Account;
