import React, { useState } from "react";
import axios from "axios";

const Play = ({ setBallPlayed, setPickYourGrid, setBallNumbers, setTotalAmount, setActiveButton }) => {
  const selectedBallsToShow = localStorage.getItem("selectedBalls");

  const [getDisabledBtn, setDisabledBtn] = useState(
    selectedBallsToShow == null ||
    JSON.parse(selectedBallsToShow).length === 0
  );

  const [getPlayedBalls, setPlayedBalls] = useState(
    JSON.parse(selectedBallsToShow) || []
  );

  // console.log(getDisabledBtn);
  // console.log(getPlayedBalls);


  const handleDelete = (index) => {
    const updatedBalls = [...getPlayedBalls];
    updatedBalls.splice(index, 1); // Remove the selected balls from the array

    setPlayedBalls(updatedBalls); // Update the state

    // Update the localStorage
    localStorage.setItem("selectedBalls", JSON.stringify(updatedBalls));
  };
  const handleEdit = (index) => {
    console.log(getPlayedBalls[index].balls);

    setBallPlayed(getPlayedBalls[index].balls);
    setBallNumbers(getPlayedBalls[index].balls.length);
    setTotalAmount(getPlayedBalls[index].price);
    setPickYourGrid(true);
  };

  const handleCheckbox = (index) => {
    setPlayedBalls((prevState) => {
      const updatedBalls = [...prevState];
      updatedBalls[index].withZeed = !updatedBalls[index].withZeed; // Toggle the value of isZeed
      if (updatedBalls[index].withZeed) {
        updatedBalls[index].price = updatedBalls[index].price + 5000;
      } else {
        updatedBalls[index].price = updatedBalls[index].price - 5000;
      }
      localStorage.setItem("selectedBalls", JSON.stringify(updatedBalls)); // Update the value in localStorage
      return updatedBalls;
    });
  };

  const handleCheckout = () => {
    if (
      !selectedBallsToShow ||
      JSON.parse(selectedBallsToShow).length === 0
    ) {
      setActiveButton({ name: "Buy" })
    } else {
      setActiveButton({ name: "Buy" })
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

  return (
    <div id="Play">
      <div className="gridplays">How many lottery grids do you want to play?</div>

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
                <input
                  className="switch"
                  type="checkbox"
                  checked={ballsSet.withZeed} // Set the checkbox based on isZeed value
                  onChange={() => handleCheckbox(index)}
                />
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
              <span className="price"><span>L.L</span> {parseInt(ballsSet.price).toLocaleString()}</span>
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
          <div className="listItem">
            <div className="checkbox">
              <input type="radio" name="radio" />
              {/* <input type="radio" name="radio" checked="checked"/> */}
            </div>
            <div className="playNB">
              <div className="titleNb">Play Once</div>
              <div className="desc">Thursday X at 9:00PM</div>
            </div>
            <div className="price">200,000 LBP</div>
          </div>

          <div className="listItem">
            <div className="checkbox">
              <input type="radio" name="radio" />
            </div>
            <div className="playNB">
              <div className="titleNb">Play Once</div>
              <div className="desc">Thursday X at 9:00PM</div>
            </div>
            <div className="price">200,000 LBP</div>
          </div>

          <div className="listItem">
            <div className="checkbox">
              <input type="radio" name="radio" />
            </div>
            <div className="playNB">
              <div className="titleNb">1 Month</div>
              <div className="desc">4 Draws - until xxx</div>
            </div>
            <div className="price">800,000 LBP</div>
          </div>

          <div className="listItem">
            <div className="checkbox">
              <input type="radio" name="radio" />
            </div>
            <div className="playNB">
              <div className="titleNb">6 Months x Draws</div>
              <div className="desc">until xxx</div>
            </div>
            <div className="price">1,200,000 LBP</div>
          </div>

          <div className="listItem">
            <div className="checkbox">
              <input type="radio" name="radio" />
            </div>
            <div className="playNB">
              <div className="titleNb">1 Year x Draws</div>
              <div className="desc">until xxx</div>
            </div>
            <div className="price">1,200,000 LBP</div>
          </div>

          <div className="listItem">
            <div className="checkbox">
              <input type="radio" name="radio" />
            </div>
            <div className="playNB">
              <div className="titleNb">Autoplay</div>
              <div className="desc">Can be canceled at any time</div>
            </div>
            <div className="price">200,000 LBP/ Draw</div>
          </div>
        </div>
      </div>

      <div className="btnSection">
        <div className="Total">
          <span>TOTAL</span>
          <div className="thePrice">L.L <div className="big">200,000</div></div>
        </div>
        <button disabled={getDisabledBtn} onClick={() => handleCheckout()}>
          Checkout
        </button>
      </div>
    </div>
  );
};

export default Play;
