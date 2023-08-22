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
}) => {
  // console.log(parameters?.deviceType);
  useEffect(() => {
    setBackLink(localStorage.getItem("BackPage"));
    setHeaderTitle("Checkout");
    localStorage.setItem("BackPage", "Buy");
  }, []);
  const selectedBallsToShow = localStorage.getItem("selectedBalls");
  const [getDisable, setDisable] = useState(false);
  const [getSpinnerLoader, setSpinnerLoader] = useState(false);
  var totalPrice = getTotalAmount;
  const [getPlayedBalls, setPlayedBalls] = useState(
    JSON.parse(selectedBallsToShow) || []
  );

  // if (getPlayedBalls != null) {
  //   getPlayedBalls.forEach((item) => {
  //     totalPrice += item.price;
  //   });
  // }

  useEffect(() => {
    console.log("clicked");
    // setSelectedOption(0);

    // setTotalAmount(totalPrice);
    setPlayedBalls(JSON.parse(selectedBallsToShow));
    if (selectedBallsToShow != null) {
      if (JSON.parse(selectedBallsToShow).length == 0) {
        setDisable(true);
      } else {
        setDisable(false);
      }
    }
    // console.log(totalPrice)
  }, [selectedBallsToShow]);

  // getPlayedBalls.forEach((item) => {
  //   totalPrice += item.price;
  // });

  const handleDelete = (index) => {
    const updatedBalls = [...getPlayedBalls];
    updatedBalls.splice(index, 1); // Remove the selected balls from the array

    setPlayedBalls(updatedBalls); // Update the state

    // Update the localStorage
    localStorage.setItem("selectedBalls", JSON.stringify(updatedBalls));
  };
  //   useEffect(() => {
  //     setDisable(true);
  //     console.log(getDisable);
  //   }, [getDisable]);

  const handleBuy = () => {
    // const buttonElement = document.getElementById("buyButton");
    // if (buttonElement) {
    //   console.log("h");
    //   buttonElement.disabled = true;
    // }
    setSpinnerLoader(true);
    setDisable(true);
    if (parameters?.deviceType === "Android") {
      console.log("tstandroid");

      setTimeout(() => {
        window.AndroidInterface.callbackHandler("message");
      }, 8000);
    } else if (parameters?.deviceType === "iPhone") {
      console.log("tstIOS");
      // const message = "data";

      setTimeout(() => {
        // window.webkit.messageHandlers.postMessage(function(message){alert("oki");}+"");
        //window.webkit.messageHandlers.callbackHandler.postMessage(function(){alert("oki");}+"");
        window.webkit.messageHandlers.callbackHandler.postMessage(
          "Hello Native mark!"
        );
      }, 8000);
    }
  };

  useEffect(() => {
    console.log(getDataGetting);
    if (getDataGetting == "hi") {
      axios
        .post("/loto/play", {
          selectedBalls: selectedBallsToShow,
        })
        .then((response) => {
          const jsonResponse = response.data.message;
          setSpinnerLoader(false);
          if (response.data.status) {
            const amount = response.data.amount;
            localStorage.removeItem("selectedBalls");
            setPlayedBalls([]);
            setDisabledBtn(
              selectedBallsToShow == null ||
                JSON.parse(selectedBallsToShow).length === 0
            );
            setModalName("SuccessModal");
            setSuccessModal({
              imgPath: "/build/images/Loto/success.png",
              title: "LOTO Purchased Successfully",
              desc: `You have successfully paid LBP ${amount} for LOTO. Best of Luck!`,
            });
            setModalShow(true);
          } else if (!response.data.status && response.data.flagCode == 10) {
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/Loto/error.png",
              title: jsonResponse.Title,
              desc: jsonResponse.SubTitle,
              path: jsonResponse.ButtonOne.flag,
              btn: jsonResponse.ButtonOne.Text,
            });
            setModalShow(true);
          } else if (!response.data.status && response.data.flagCode == 11) {
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/Loto/error.png",
              title: jsonResponse.Title,
              desc: jsonResponse.SubTitle,
              path: jsonResponse.ButtonOne.flag,
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
          } else {
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/Loto/error.png",
              title: "Please Try again",
              desc: `You can not purchase now`,
              // path: response.data.path,
              // btn:'Top up'
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
    }
  },[getDataGetting]);

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
                      <span key={ballIndex} className="">
                        {ball}
                      </span>
                    ))}
                  </div>
                </div>
                <div className="footer">
                  <span className="price">
                    <span>L.L</span> {parseInt(ballsSet.price).toLocaleString()}
                  </span>
                  {/* <span className="delete" onClick={() => handleDelete(index)}>
                    <img src="/build/images/Loto/trash.png" />
                  </span> */}
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
          <span>PLAY ZEED (+ 5,000 LBP)</span>
        </div>
        <div className="zeedImage">
          <img src="/build/images/Loto/zeedLogo.png" alt="SmileLOGO" />
        </div>
      </div>

      <div id="Total">
        <span>TOTAL</span>
        <div className="thePrice">
          <div>L.L </div>
          <div className="big">{parseInt(totalPrice).toLocaleString()}</div>
        </div>
      </div>

      <button
        className="BuyBtn"
        id="buyButton"
        disabled={getDisable}
        // disabled={getDisabledBtn}
        onClick={() => {
          handleBuy();
        }}
      >
        Buy
      </button>
    </div>
  );
};

export default Buy;
