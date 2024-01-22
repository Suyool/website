import React, { useEffect, useState } from "react";
import axios from "axios";
import { Spinner } from "react-bootstrap";

const Buy = ({
  parameters,
  setDisabledBtn,
  setSuccessModal,
  setErrorModal,
  setModalName,
  setModalShow,
  setWarningModal,
  setHeaderTitle,
  setBackLink,
  getTotalAmount,
  getDataGetting,
  setDataGetting,
  setTotalAmount,
  setCheckBuy,
}) => {
  useEffect(() => {
    localStorage.setItem("BackPage", "Play");
    setBackLink(localStorage.getItem("BackPage"));
    setHeaderTitle("Checkout");
  }, []);
  const selectedBallsToShow = localStorage.getItem("selectedBalls");
  const [getDisable, setDisable] = useState(false);
  const [isClicked, setIsClicked] = useState(false);
  const [getSpinnerLoader, setSpinnerLoader] = useState(false);
  const [getPlayedBalls, setPlayedBalls] = useState(
    JSON.parse(selectedBallsToShow) || []
  );
  useEffect(() => {
    setPlayedBalls(JSON.parse(selectedBallsToShow));
    if (selectedBallsToShow != null) {
      if (JSON.parse(selectedBallsToShow).length == 0) {
        setDisable(true);
      } else {
        setDisable(false);
      }
    }
  }, [selectedBallsToShow]);

  const handleDelete = (index) => {
    const updatedBalls = [...getPlayedBalls];
    updatedBalls.splice(index, 1);
    setPlayedBalls(updatedBalls);
    localStorage.setItem("selectedBalls", JSON.stringify(updatedBalls));
    const selectedBallsJSON = localStorage.getItem("selectedBalls");
    const selectedBalls = JSON.parse(selectedBallsJSON) || [];
    const totalAmount = selectedBalls.reduce(
      (total, ball) => total + ball.price,
      0
    );
    setTotalAmount(totalAmount);
  };
  const handleBuy = () => {
    setDisable(true);
    setSpinnerLoader(true);
    setIsClicked(true);

    if (isClicked) {
      return;
    }
    setTimeout(() => {
      console.log("clicked");
      setIsClicked(true);
      setDataGetting("");
      if (parameters?.deviceType === "Android") {
        setTimeout(() => {
          window.AndroidInterface.callbackHandler("message");
        }, 2000);
      } else if (parameters?.deviceType === "Iphone") {
        setTimeout(() => {
          window.webkit.messageHandlers.callbackHandler.postMessage(
            "fingerprint"
          );
        }, 2000);
      }
      window.handleCheckout = (message) => {
        setDataGetting(message);
      };
    }, 1000);
  };

  useEffect(() => {
    console.log(getDataGetting);
    if (getDataGetting == "success") {
      setDataGetting("");
      axios
        .post("/loto/play", {
          selectedBalls: selectedBallsToShow,
        })
        .then((response) => {
          const jsonResponse = response.data.message;
          setSpinnerLoader(false);
          if (response.data.status) {
            const amount = response.data.amount;
            setModalName("SuccessModal");
            setSuccessModal({
              imgPath: "/build/images/Loto/success.png",
              title: "LOTO Purchased Successfully",
              desc: (
                <div>
                  You have successfully paid L.L{" "}
                  {parseInt(amount).toLocaleString()} for LOTO.
                  <br />
                  Best of Luck!
                </div>
              ),
              deviceType: parameters?.deviceType,
            });
            setModalShow(true);
            localStorage.removeItem("selectedBalls");
            setPlayedBalls([]);
            setDisabledBtn(true);
            setCheckBuy(true);
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
          } else if (!response.data.status && response.data.flagCode == 150) {
            const amount = response.data.amount;
            setModalName("WarningModal");
            setWarningModal({
              imgPath: "/build/images/Loto/warning.png",
              title: jsonResponse.Title,
              desc: jsonResponse.SubTitle,
              path: jsonResponse.flag,
              btn: jsonResponse.Text,
            });
            setModalShow(true);
          } else if (!response.data.status && response.data.flagCode == 210) {
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/Loto/error.png",
              title: "Cannot Play Grid",
              desc: `You have a grid with the same numbers in this draw <br/>` + response.data.gridSelected,
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
          setDisabledBtn(
            selectedBallsToShow == null ||
              JSON.parse(selectedBallsToShow).length === 0
          );
        });
      setDisable(false);
    } else if (getDataGetting == "failed") {
      setDataGetting("");
      setSpinnerLoader(false);
      setDisable(false);
    }
  }, [getDataGetting]);

  return (
    <div id="Buy" className={` ${getSpinnerLoader ? "hideBackk" : ""}`}>
      {getSpinnerLoader && (
        <div id="spinnerLoader">
          <Spinner className="spinner" animation="border" variant="secondary" />
        </div>
      )}
      {getPlayedBalls &&
        getPlayedBalls.map((ballsSet, index) => {
          const hasBouquet = ballsSet.hasOwnProperty("bouquet");
          const hasBalls = ballsSet.hasOwnProperty("balls");
          if (hasBouquet) {
            return (
              <div className="gridborder" key={index}>
                <div className="header">
                  <span>
                    <img src="/build/images/Loto/LotoGrid.png" alt="loto" />
                    Bouquet
                  </span>
                </div>
                <div className="body">
                  <div className="bouquetSection">
                    <span>
                      {ballsSet.bouquet.replace("B", "")}{" "}
                      {ballsSet.bouquet === "B1" ? "Grid" : "Grids"}
                    </span>
                  </div>
                </div>
                <div className="footer">
                  <span className="price">
                    <span>L.L</span> {parseInt(ballsSet.price).toLocaleString()}
                  </span>
                  <span className="delete" onClick={() => handleDelete(index)}>
                    <img src="/build/images/Loto/trash.png" />
                  </span>
                </div>
              </div>
            );
          } else {
            return (
              <div className="gridborder" key={index}>
                <div className="header">
                  <span>
                    <img src="/build/images/Loto/LotoGrid.png" alt="loto" />
                    Grid
                  </span>
                </div>
                <div className="body">
                  <div className="ballSection">
                    {ballsSet.balls.map((ball, ballIndex) => (
                      <span
                        key={ballIndex}
                        className=""
                        style={
                          ballIndex >= 6 ? { backgroundColor: "#8D0500" } : {}
                        }
                      >
                        {ball}
                      </span>
                    ))}
                  </div>
                </div>
                <div className="footer">
                  <span className="price">
                    <span>L.L</span> {parseInt(ballsSet.price).toLocaleString()}
                  </span>
                  <span className="delete" onClick={() => handleDelete(index)}>
                    <img src="/build/images/Loto/trash.png" />
                  </span>
                </div>
              </div>
            );
          }
        })}

      <div className="zeedSection">
        <div className="title">Next Zeed Estimated Jackpot</div>
        <div className="price">
          LBP {parseInt(parameters.next_zeed_prize).toLocaleString()}
        </div>
        <div className="desc">
          Zeed is an additional game played on the Loto grid. It gives you an
          additional chance to win big. Zeed’s draw is made with Loto’s draw. It
          is also 2 draws per week.
        </div>
        <div className="playZeed">
          <span>
            PLAY ZEED (+{" "}
            {parseInt(parameters.gridpricematrix[0].zeed).toLocaleString()} LBP)
          </span>
        </div>
        <div className="zeedImage">
          <img src="/build/images/Loto/zeedLogo.png" alt="SmileLOGO" />
        </div>
      </div>

      <div id="TotalPlay">
        <span>TOTAL</span>
        <div className="thePrice">
          <div>L.L </div>
          <div className="big">{parseInt(getTotalAmount).toLocaleString()}</div>
        </div>
      </div>

      <button
        className="BuyBtn"
        id="buyButton"
        disabled={getDisable}
        onClick={() => {
          handleBuy();
        }}
      >
        BUY
      </button>
    </div>
  );
};

export default Buy;
