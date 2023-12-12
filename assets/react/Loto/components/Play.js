import React, { useEffect, useState } from "react";
import axios from "axios";

const Play = ({
  setBallPlayed,
  setPickYourGrid,
  setBallNumbers,
  setTotalAmount,
  setActiveButton,
  parameters,
  setIsHide,
  setHeaderTitle,
  setBackLink,
  setModalName,
  setModalShow,
  setWarningModal,
  setPlay,
  setTotalAmountLLDJ,
  setDataGetting,
  setBallNumbersIndex,
}) => {
  const [selectedOption, setSelectedOption] = useState(false);
  const [selectedSub, setSelectedSub] = useState(false);
  const [checked, setChecked] = useState(false);
  const selectedBallsToShow = localStorage.getItem("selectedBalls");
  const [getHowOftenPlay, setHowOftenPlay] = useState(0);
  var totalPrice = 0;
  const [getDisabledBtn, setDisabledBtn] = useState(
    selectedBallsToShow == null || JSON.parse(selectedBallsToShow).length === 0
  );

  const calculateTotalPrice = (balls) => {
    let total = 0;
    balls.forEach((item) => {
      total += item.price;
    });
    return total;
  };
  useEffect(() => {
    setDataGetting("");
    setSelectedOption(0);
    setSelectedSub(1);
    localStorage.setItem("BackPage", "LLDJ");
    setBackLink(localStorage.getItem("BackPage"));
    setHeaderTitle("Play");

    setDisabledBtn(
      selectedBallsToShow == null ||
        JSON.parse(selectedBallsToShow).length === 0
    );
  }, []);

  const [getPlayedBalls, setPlayedBalls] = useState(
    JSON.parse(selectedBallsToShow) || []
  );
  if (getPlayedBalls != null) {
    getPlayedBalls.forEach((item) => {
      totalPrice += item.price;
    });
    const hasBalls = getPlayedBalls.some((item) =>
      item.hasOwnProperty("balls")
    );
  }

  useEffect(() => {
    setModalShow(false);
    setPlayedBalls(JSON.parse(selectedBallsToShow));
    if (selectedBallsToShow != null) {
      if (JSON.parse(selectedBallsToShow).length == 0) {
        setDisabledBtn(true);
      } else {
        setDisabledBtn(false);
      }
    }
  }, [selectedBallsToShow, getHowOftenPlay]);

  const handleDelete = (index) => {
    const updatedBalls = [...getPlayedBalls];
    updatedBalls.splice(index, 1);
    setPlayedBalls(updatedBalls);
    localStorage.setItem("selectedBalls", JSON.stringify(updatedBalls));
    index = index - 1;
  };
  const handleEdit = (index) => {
    setBallNumbersIndex(index);
    setBallPlayed(getPlayedBalls[index].balls);
    setBallNumbers(getPlayedBalls[index].balls.length);
    if (getPlayedBalls[index].withZeed) {
      setTotalAmountLLDJ(getPlayedBalls[index].price - 10000);
    } else {
      setTotalAmountLLDJ(getPlayedBalls[index].price);
    }
    setPickYourGrid(true);
  };

  const handleCheckbox = (index) => {
    setChecked(!checked);
    setPlayedBalls((prevState) => {
      const updatedBalls = [...prevState];

      updatedBalls[index].withZeed = !updatedBalls[index].withZeed;
      if (updatedBalls[index].withZeed) {
        updatedBalls[index].price = updatedBalls[index].price + parameters.gridpricematrix[0].zeed;
      } else {
        updatedBalls[index].price = updatedBalls[index].price - parameters.gridpricematrix[0].zeed;
      }
      localStorage.setItem("selectedBalls", JSON.stringify(updatedBalls));
      const newTotalPrice = calculateTotalPrice(updatedBalls);
      setTotalAmount(newTotalPrice);
      return updatedBalls;
    });
  };

  const handleCheckout = () => {
    if (!selectedBallsToShow || JSON.parse(selectedBallsToShow).length === 0) {
      setDisabledBtn(
        selectedBallsToShow == null ||
          JSON.parse(selectedBallsToShow).length === 0
      );
    } else {
      const subscription = { subscription: selectedOption };
      const existingData = localStorage.getItem("selectedBalls");

      if (existingData) {
        const parsedData = JSON.parse(existingData);
        const newData = parsedData.map((entry) => ({
          ...entry,
          subscription: selectedSub,
        }));

        localStorage.setItem("selectedBalls", JSON.stringify(newData));
      }
      setDisabledBtn(
        selectedBallsToShow == null ||
          JSON.parse(selectedBallsToShow).length === 0
      );
      setTotalAmount(totalPrice * selectedSub);
      if (parameters.hasOwnProperty("tooLateDraw")) {
        setModalName("WarningModal");
        setWarningModal({
          imgPath: "/build/images/Loto/warning.png",
          title: parameters.tooLateDraw.message.Title,
          desc: parameters.tooLateDraw.message.SubTitle,
          path: parameters.tooLateDraw.message.flag,
          btn: parameters.tooLateDraw.message.Text,
        });
        setModalShow(true);
        return;
      }
      setActiveButton({ name: "Buy" });
      
    }
  };

  const howOftenYouWantToPlay = [
    {
      titleNb: "Play Once",
      desc: parameters.HowOftenDoYouWantToPlay[0] + " at 7:30PM",
      price: totalPrice === 0 ? "" : parseInt(totalPrice * 1).toLocaleString(),
    },
    {
      titleNb: "1 Week",
      desc: "2 Draws - until " + parameters.HowOftenDoYouWantToPlay[1],
      price: totalPrice === 0 ? "" : parseInt(totalPrice * 2).toLocaleString(),
    },
    {
      titleNb: "1 Month",
      desc: "8 Draws - until " + parameters.HowOftenDoYouWantToPlay[2],
      price: totalPrice === 0 ? "" : parseInt(totalPrice * 8).toLocaleString(),
    },
    {
      titleNb: "6 Months 52 Draws",
      desc: "until " + parameters.HowOftenDoYouWantToPlay[3],
      price: totalPrice === 0 ? "" : parseInt(totalPrice * 52).toLocaleString(),
    },
    {
      titleNb: "1 Year 104 Draws",
      desc: "until " + parameters.HowOftenDoYouWantToPlay[4],
      price:
        totalPrice === 0 ? "" : parseInt(totalPrice * 104).toLocaleString(),
    },
  ];

  const handleOptionSelect = (index) => {
    setHowOftenPlay(index);
    let sub = 0;
    if (index == 0) {
      totalPrice = totalPrice * 1;
      sub = 1;
    } else if (index == 1) {
      totalPrice = totalPrice * 2;
      sub = 2;
    } else if (index == 2) {
      totalPrice = totalPrice * 8;
      sub = 8;
    } else if (index == 3) {
      totalPrice = totalPrice * 52;
      sub = 52;
    } else if (index == 4) {
      totalPrice = totalPrice * 104;
      sub = 104;
    }
    setTotalAmount(totalPrice);
    setSelectedOption(index);
    setSelectedSub(sub);
  };

  return (
    <div id="Play">
      <div className="gridplays">
        How many lottery grids do you want to play?
      </div>

      {getPlayedBalls &&
        getPlayedBalls.map((ballsSet, index) => {
          const hasBouquet = ballsSet.hasOwnProperty("bouquet");
          const hasBalls = ballsSet.hasOwnProperty("balls");
          if (hasBouquet) {
            return (
              <div className="gridborder mt-2" key={index}>
                <div className="header">
                  <span>
                    <img src="/build/images/Loto/LotoGrid.png" alt="loto" />
                    Bouquet
                  </span>
                  <span className="right">
                    <span>PLAY ZEED (+ L.L {parseInt(parameters.gridpricematrix[0].zeed).toLocaleString()})</span>

                    <div className="toggle">
                      <div className="toggle-switch">
                        <div
                          id="toggle"
                          className={
                            ballsSet.withZeed
                              ? "toggle-input checked-toggle"
                              : "toggle-input"
                          }
                          onClick={() => {
                            handleCheckbox(index);
                          }}
                        />
                        <label htmlFor="toggle" className="toggle-label" />
                      </div>
                    </div>
                  </span>
                </div>
                <div className="body">
                  <div className="bouquetSection">
                    <span>{ballsSet.bouquet.replace("B", "")} Grids</span>
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
              <div className="gridborder mt-2" key={index}>
                <div className="header">
                  <span>
                    <img src="/build/images/Loto/LotoGrid.png" alt="loto" />{" "}
                    GRID {index + 1}
                  </span>
                  <span className="right">
                    <span>PLAY ZEED (+ L.L {parseInt(parameters.gridpricematrix[0].zeed).toLocaleString()})</span>

                    <div className="toggle">
                      <div className="toggle-switch">
                        <div
                          id="toggle"
                          className={
                            ballsSet.withZeed
                              ? "toggle-input checked-toggle"
                              : "toggle-input"
                          }
                          onClick={() => {
                            handleCheckbox(index);
                          }}
                        />
                        <label htmlFor="toggle" className="toggle-label" />
                      </div>
                    </div>
                  </span>
                </div>
                <div className="body">
                  <div className="ballSection mt-2">
                    {ballsSet.balls.map((ball, ballIndex) =>
                      ball !== null ? <span key={ballIndex} style={ballIndex >= 6 ? { backgroundColor: '#8D0500' } : {}}>{ball}</span> : null
                    )}
                  </div>
                  <div className="edit" onClick={() => handleEdit(index)}>
                    <img src="/build/images/Loto/edit.png" alt="edit" />
                  </div>
                </div>
                <div className="footer">
                  <span className="price">
                    <span>L.L</span> {parseInt(ballsSet.price).toLocaleString()}
                  </span>
                  <span className="delete" onClick={() => handleDelete(index)}>
                    <img src="/build/images/Loto/trash.png" alt="delete" />
                  </span>
                </div>
              </div>
            );
          }
        })}

      <div
        className="addGrid"
        onClick={() => {
          setBallNumbers(10);
          setTotalAmountLLDJ(0);
          setPickYourGrid(true);
          setIsHide(true);
          setPlay(1);
        }}
      >
        <span>+</span>
      </div>
      {/* DONOTREMOVE */}
      {/* <div className="br"></div>
      <div className="wantToPlay">
        <div className="title">How often do you want to play?</div>
        <div className="listSection">
          {howOftenYouWantToPlay.map((item, index) => (
            <div className="listItem" key={index}>
              <div className="checkbox">
                <img
                  src={
                    selectedOption === index
                      ? "/build/images/Loto/radioTrue.svg"
                      : "/build/images/Loto/radioFalse.svg"
                  }
                  alt="loto"
                  onClick={() => handleOptionSelect(index)}
                />
              </div>
              <div className="playNB">
                <div className="titleNb">{item.titleNb}</div>
                <div className="desc">{item.desc}</div>
              </div>
              <div className="price">{item.price}</div>
            </div>
          ))}
        </div>
      </div> */}

      <div className="btnSection">
        <div id="Total">
          <span>TOTAL</span>
          <div className="thePrice">
            L.L{" "}
            <div className="big">{parseInt(totalPrice).toLocaleString()}</div>
          </div>
        </div>
        <button disabled={getDisabledBtn} onClick={() => handleCheckout()}>
          Checkout
        </button>
      </div>
    </div>
  );
};

export default Play;
