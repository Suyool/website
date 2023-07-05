import React, { useEffect, useState } from "react";
import axios from "axios";

const Play = ({
  setBallPlayed,
  setPickYourGrid,
  setBallNumbers,
  setTotalAmount,
  setActiveButton,
  getDisabledBtn,
  setDisabledBtn,
}) => {
  const [selectedOption, setSelectedOption] = useState(null);
  const [checked, setChecked] = useState(false);

  const selectedBallsToShow = localStorage.getItem("selectedBalls");

  useEffect(() => {
    setDisabledBtn(
      selectedBallsToShow == null ||
      JSON.parse(selectedBallsToShow).length === 0
    );
  }, []);

  const [getPlayedBalls, setPlayedBalls] = useState(JSON.parse(selectedBallsToShow) || []);

  const handleDelete = (index) => {
    const updatedBalls = [...getPlayedBalls];
    updatedBalls.splice(index, 1);
    setPlayedBalls(updatedBalls);
    localStorage.setItem("selectedBalls", JSON.stringify(updatedBalls));
  };
  const handleEdit = (index) => {
    // console.log(getPlayedBalls[index].balls);
    setBallPlayed(getPlayedBalls[index].balls);
    setBallNumbers(getPlayedBalls[index].balls.length);
    setTotalAmount(getPlayedBalls[index].price);
    setPickYourGrid(true);
  };

  const handleCheckbox = (index) => {
    setChecked(!checked)
    setPlayedBalls((prevState) => {
      const updatedBalls = [...prevState];
      console.log(index)

      updatedBalls[index].withZeed = !updatedBalls[index].withZeed;
      if (updatedBalls[index].withZeed) {
        updatedBalls[index].price = updatedBalls[index].price + 5000;
      } else {
        updatedBalls[index].price = updatedBalls[index].price - 5000;
      }
      localStorage.setItem("selectedBalls", JSON.stringify(updatedBalls));
      return updatedBalls;
    });
  };

  const handleCheckout = () => {
    if (!selectedBallsToShow || JSON.parse(selectedBallsToShow).length === 0) {
      setDisabledBtn(
        selectedBallsToShow == null ||
        JSON.parse(selectedBallsToShow).length === 0
      );
      setActiveButton({ name: "Buy" });
    } else {
      setDisabledBtn(
        selectedBallsToShow == null ||
        JSON.parse(selectedBallsToShow).length === 0
      );
      setActiveButton({ name: "Buy" });
      //   axios
      //     .post("/loto/play", {
      //       selectedBalls: selectedBallsToShow,
      //     })
      //     .then((response) => {
      //       console.log(response);
      //       localStorage.removeItem("selectedBalls")
      //       setPlayedBalls([]);
      //       setDisabledBtn(
      //         selectedBallsToShow == null ||
      //         JSON.parse(selectedBallsToShow).length === 0
      //       );
      //     })
      //     .catch((error) => {
      //       console.log(error);
      //       setDisabledBtn(
      //         selectedBallsToShow == null ||
      //         JSON.parse(selectedBallsToShow).length === 0
      //       );
      //     });
    }
  };

  const howOftenYouWantToPlay = [
    {
      titleNb: "Play Once",
      desc: "Thursday X at 9:00PM",
      price: "200,000 LBP",
    },
    {
      titleNb: "Play Once",
      desc: "Thursday X at 9:00PM",
      price: "200,000 LBP",
    },
    {
      titleNb: "1 Month",
      desc: "4 Draws - until xxx",
      price: "800,000 LBP",
    },
    {
      titleNb: "6 Months x Draws",
      desc: "until xxx",
      price: "1,200,000 LBP",
    },
    {
      titleNb: "1 Year x Draws",
      desc: "until xxx",
      price: "1,200,000 LBP",
    },
    {
      titleNb: "Autoplay",
      desc: "Can be canceled at any time",
      price: "200,000 LBP/ Draw",
    },
  ];

  const handleOptionSelect = (index) => {
    setSelectedOption(index);
  };

  return (
    <div id="Play">
      <div className="gridplays">
        How many lottery grids do you want to play?
      </div>

      {getPlayedBalls &&
        getPlayedBalls.map((ballsSet, index) => (
          <div className="gridborder mt-2" key={index}>
            <div className="header">
              <span>
                <img src="/build/images/Loto/LotoGrid.png" alt="loto" /> GRID{" "}
                {index + 1}
              </span>
              <span className="right">
                <span>PLAY ZEED (+ L.L 5,000)</span>

                <div className="toggle">
                  <div className="toggle-switch">
                    <div
                      id="toggle"
                      className={ballsSet.withZeed ? "toggle-input checked-toggle" : "toggle-input"}
                      onClick={() => { handleCheckbox(index) }}
                    />
                    <label htmlFor="toggle" className="toggle-label" />
                  </div>
                </div>

              </span>
            </div>
            <div className="body">
              <div className="ballSection mt-2">
                {ballsSet.balls.map((ball, ballIndex) => (
                  <span key={ballIndex}>{ball}</span>
                ))}
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
        ))}

      <div
        className="addGrid"
        onClick={() => {
          setActiveButton({ name: "LLDJ" });
        }}
      >
        <span>+</span>
      </div>

      <div className="br"></div>
      <div className="wantToPlay">
        <div className="title">How often do you want to play?</div>
        <div className="listSection">

          {howOftenYouWantToPlay.map((item, index) => (
            <div className="listItem" key={index}>
              <div className="checkbox">
                <img
                  src={selectedOption === index ? "/build/images/Loto/radioTrue.svg" : "/build/images/Loto/radioFalse.svg"}
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
      </div>

      <div className="btnSection">
        <div id="Total">
          <span>TOTAL</span>
          <div className="thePrice">
            L.L <div className="big">200,000</div>
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
