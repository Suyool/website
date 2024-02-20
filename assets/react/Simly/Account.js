import axios from "axios";
import React, { useEffect, useState } from "react";
import ContentLoader from "react-content-loader";
import { Spinner } from "react-bootstrap";

const Account = ({ setHeaderTitle, parameters, selectedPlan, setActiveButton, selectedPackage, setEsimDetail, setBackLink, getDataGetting, setDataGetting, setErrorModal, setSuccessModal, setModalName, setModalShow, setSpinnerLoader, getSpinnerLoader }) => {
  const [getAccountInformation, setAccountInformation] = useState();
  const [getMap, setMap] = useState(false);
  const [isLoading, setIsLoading] = useState(false);

  const [reqObj, setReqObj] = useState({
    planId: "",
    esimId: "",
    countryImage: "",
  });

    useEffect(() => {
        setHeaderTitle("My eSim Account");
        setBackLink("");
        setDataGetting("");
        setIsLoading(true);
        axios
            .post("/simly/getUsageOfEsim")
            .then((response) => {
                setMap(true);
                setAccountInformation(response.data.message);
                setIsLoading(false);
            })
            .catch((error) => {
                setIsLoading(false);
                console.log(error);
            });
    }, []);

  const Topup = () => {
    setSpinnerLoader(true);
    setTimeout(() => {
      console.log("clicked");
      setDataGetting("");
      if (parameters?.deviceType === "Android") {
        setTimeout(() => {
          window.AndroidInterface.callbackHandler("message");
        }, 2000);
      } else if (parameters?.deviceType === "Iphone") {
        setTimeout(() => {
          window.webkit.messageHandlers.callbackHandler.postMessage("fingerprint");
        }, 2000);
      }
      window.handleCheckout = (message) => {
        setDataGetting(message);
      };
    }, 1000);
  };

  useEffect(() => {
    if (getDataGetting == "success") {
      setDataGetting("");
      axios
        .post("/simly/purchaseTopup", reqObj)
        .then((response) => {
          const jsonResponse = response.data.message;
          if (response.data.status) {
            setSpinnerLoader(false);
            setModalName("SuccessModal");
            setSuccessModal({
              imgPath: "/build/images/Loto/success.png",
              title: "Simly Purchased Successfully",
              desc: (
                <div>
                  Please Download the qr
                  <br />
                  <img src={`${response.data.data.qrCodeImageUrl}`} />
                </div>
              ),
              qr: response.data.data.qrCodeImageUrl,
              deviceType: parameters?.deviceType,
            });
            setModalShow(true);
            localStorage.removeItem("selectedBalls");
          } else if (!response.data.status && response.data.flagCode == 10) {
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/Loto/error.png",
              title: jsonResponse.Title,
              desc: jsonResponse.SubTitle,
              path: jsonResponse.ButtonOne.Flag,
              btn: jsonResponse.ButtonOne.Text,
            });
            setModalShow(true);
          } else if (!response.data.status && response.data.flagCode == 11) {
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/Loto/error.png",
              title: jsonResponse.Title,
              desc: jsonResponse.SubTitle,
              path: jsonResponse.ButtonOne.Flag,
              btn: jsonResponse.ButtonOne.Text,
            });
            setModalShow(true);
          } else {
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/Loto/error.png",
              title: "Please Try again",
              desc: `You cannot purchase now`,
            });
            setModalShow(true);
          }
        })
        .catch((error) => {
          setSpinnerLoader(false);
          console.log(error);
          setDisabledBtn(selectedBallsToShow == null || JSON.parse(selectedBallsToShow).length === 0);
        });
    } else if (getDataGetting == "failed") {
      setDataGetting("");
      setSpinnerLoader(false);
      setDisable(false);
    }
  }, [getDataGetting]);

  // console.log(getAccountInformation);

  return (
    <>
      {getSpinnerLoader && (
        <div className={` ${getSpinnerLoader ? "accountInfo hideBackk" : "accountInfo"}`}>
          <div id="spinnerLoader">
            <Spinner className="spinner" animation="border" variant="secondary" />
          </div>
        </div>
      )}
      {isLoading ? (
        <div className="mt-5" style={{ margin: "0 10px" }}>
          <ContentLoader speed={2} width="100%" height="90vh" backgroundColor="#f3f3f3" foregroundColor="#ecebeb">
            <rect x="0" y="0" rx="3" ry="3" width="100%" height="180" />
            <rect x="0" y="210" rx="3" ry="3" width="100%" height="180" />
          </ContentLoader>
        </div>
      ) : (
        <>
          {getMap && (
            <>
              {getAccountInformation.map((data, index) => (
                <div key={index} className="accountcomp">
                  <div className="accountCard">
                    <div className="titleaccount">
                      {data.countryImage ? <img src={data.countryImage} /> : <img src="/build/images/simlyIcon.svg" />}
                      <span>{data.country}</span>
                    </div>
                    <div className="rechargable">
                      <div class="single-chart">
                        <svg viewBox="0 0 36 36" className={`circular-chart ${data.sim.size === data.sim.consumed ? 'violet' : 'green'}`}>
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
                        <input type="checkbox" id="eSim" name="eSim" value="eSim" checked={data.sim.size !== data.sim.consumed} disabled />
                        <label className="esim">eSim is still valid</label>
                        <br />
                        <input type="checkbox" id="plans" name="plans" value="plan" checked={data.sim.size === data.sim.consumed} disabled />
                        <label className="esim">Plan has been fully used</label>
                      </div>
                    </div>
                    <div className="btns">
                      <div className="topup">
                        <button
                          className="btntopup"
                          onClick={() => {
                            setReqObj({
                              esimId: data?.esimId,
                              planId: data?.plan,
                              countryImage: data?.countryImage,
                            });
                            Topup();
                          }}
                        >
                          Top up
                        </button>
                      </div>
                      <div className="details">
                        <button
                          className="btntopup"
                          onClick={() => {
                            setEsimDetail({});
                            setEsimDetail(data);
                            setActiveButton({ name: "PlanDetail" });
                          }}
                        >
                          Details
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </>
          )}
        </>
      )}
    </>
  );
};

export default Account;
