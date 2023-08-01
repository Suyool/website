import React, { useEffect, useState } from "react";

const PickYourGrid = ({
  parameters,
  setPickYourGrid,
  getPlay,
  getBallNumbers,
  setTotalAmount,
  getTotalAmount,
  getBallPlayed,
  setIsHide,
  setErrorModal,
  setModalName,
  setModalShow,
}) => {
  
  const [selectedBalls, setSelectedBalls] = useState([]);
  useEffect(() => {
    if (getBallPlayed.length == 0) {
      setSelectedBalls(Array(getBallNumbers).fill(null));
    } else {
      setSelectedBalls(getBallPlayed);
    }
  }, []);

  // Calculate the total amount based on the number of filled boxes
  if (getPlay) {
    const calculateTotalAmount = () => {
      const data = parameters.gridpricematrix;
      switch (selectedBalls.filter((ball) => ball !== null).length) {
        case 6:
          return data[0].price;
        case 7:
          return data[1].price;
        case 8:
          return data[2].price;
        case 9:
          return data[3].price;
        case 10:
          return data[4].price;
        // Add more cases as needed
        default:
          return 0; // Set default value if the number of filled boxes doesn't match any of the cases
      }
    };
    useEffect(() => {
      const filledBoxes = selectedBalls.filter((ball) => ball !== null).length;
      const totalAmount = calculateTotalAmount(filledBoxes);
      // Update the total amount whenever the selectedBalls change
      setTotalAmount(totalAmount);
    }, [selectedBalls]);
  }

  const handleBallClick = (number) => {
    const index = selectedBalls.findIndex((ball) => ball === null);
    if (index !== -1 && !selectedBalls.includes(number)) {
      const updatedBalls = [...selectedBalls];
      updatedBalls[index] = number;
      setSelectedBalls(updatedBalls);
    }
  };

  const handleClearPick = () => {
    setSelectedBalls(Array(getBallNumbers).fill(null));
  };

  const handleQuickPick = () => {
    setSelectedBalls((prevSelectedBalls) => {
      const availableBalls = ballNumbers.filter(
        (ball) => !prevSelectedBalls.includes(ball)
      );
      const randomBalls = [];
      if (!getPlay) {
        while (randomBalls.length < getBallNumbers) {
          const randomIndex = Math.floor(Math.random() * availableBalls.length);
          randomBalls.push(availableBalls[randomIndex]);
          availableBalls.splice(randomIndex, 1);
        }
        return randomBalls;
      }
    });
    if (getPlay) {
      const availableBalls = ballNumbers;
      const randomBalls = [];
      while (randomBalls.length < 6) {
        const randomIndex = Math.floor(Math.random() * availableBalls.length);
        randomBalls.push(availableBalls[randomIndex]);
        availableBalls.splice(randomIndex, 1);
      }
      const filledBalls = randomBalls.concat(Array(4).fill(null));
      setSelectedBalls(filledBalls);
      //   return randomBalls;
    }
  };

  const handleDone = () => {
    const lastBall = selectedBalls[selectedBalls.length - 1];
    if (selectedBalls.length > 5) {
      setIsHide(false);

      const filteredBalls = selectedBalls.filter((ball) => ball !== null);

      if (filteredBalls.length < 6) {
        setModalName("ErrorModal");
        setErrorModal({
          img: "/build/images/Loto/error.png",
          title: "Can not play grid",
          desc: `You need to select at least 6 numbers`,
        });
        setModalShow(true);
        return;
      }

      const ballSet = {
        balls: filteredBalls,
        price: getTotalAmount,
        withZeed: false,
        currency: "LBP",
        isbouquet: false,
      };
      const existingData = localStorage.getItem("selectedBalls");
      const existingBalls = existingData ? JSON.parse(existingData) : [];
      const isNewSet = existingBalls.every(
        (set) => JSON.stringify(set.balls) !== JSON.stringify(selectedBalls)
      );

      if (isNewSet) {
        // If the set is unique, add it to the existing data and save to localStorage
        const updatedBalls = [...existingBalls, ballSet];
        localStorage.setItem("selectedBalls", JSON.stringify(updatedBalls));
      } else {
        setModalName("ErrorModal");
        setErrorModal({
          img: "/build/images/Loto/error.png",
          title: "Can not play grid",
          desc: `You have a grid with same numbers in this draw`,
        });
        setModalShow(true);
      }
      setPickYourGrid(false);
    } else {
      console.log("The last ball is null");
    }
  };

  const handleCancel = () => {
    setPickYourGrid(false);
    setIsHide(false);
  };

  const ballNumbers = Array.from({ length: 42 }, (_, index) => index + 1);

  return (
    <div className="PickYourGrid">
      <div className="topSectionPick">
        <div className="titles">
          <div className="titleGrid">Pick Your Grid</div>
          <button onClick={handleCancel}>Cancel</button>
        </div>

        <div className="selectedBalls">
          {selectedBalls.map((number, index) =>
            index <= 5 ? (
              <div
                key={index}
                id={`${
                  getPlay && number == null && index > 5
                    ? `boxappear${index}`
                    : ""
                }`}
              >
                <span className={`${number !== null ? "active" : ""}`}>
                  {number}
                </span>
                <div className="shadow"></div>
              </div>
            ) : null
          )}
          { selectedBalls.slice(6).length > 0 && (
            <div style={{display:"flex"}}>
              {selectedBalls.slice(6).map((number, index) => (
                <div
                  key={index}
                  id={`${
                    getPlay && number == null ? `boxappear${index + 6}` : ""
                  }`}
                >
                  <span className={`${number !== null ? "active" : ""}`}>
                    {number}
                  </span>
                  <div className="shadow"></div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>

      <div className="bodySectionPick">
        {ballNumbers.map((number) => {
          const isSelected = selectedBalls.includes(number);
          const ballClass = isSelected ? "active" : "";

          return (
            <div className="ballCont" key={number}>
              <button onClick={() => handleBallClick(number)}>
                <span className={`${ballClass}`}>{number}</span>
              </button>
            </div>
          );
        })}
      </div>

      <div className="footSectionPick">
        <div id="Total">
          <span>TOTAL</span>
          <div className="thePrice">
            L.L{" "}
            <div className="big">
              {parseInt(getTotalAmount).toLocaleString()}
            </div>
          </div>
        </div>

        <div className="options">
          <button className="aboutGrid" onClick={handleClearPick}>
            Clear grid
          </button>
          <button className="aboutGrid" onClick={handleQuickPick}>
            Quick pick
          </button>
          <button className="done" onClick={handleDone}>
            Done
          </button>
        </div>
      </div>
    </div>
  );
};

export default PickYourGrid;
