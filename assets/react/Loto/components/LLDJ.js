import React, { useEffect, useState } from "react";
import Countdown from "./Countdown";
import BouquetOptions from "./BouquetOptions";

const LLDJ = ({
  parameters,
  setPickYourGrid,
  setTotalAmount,
  setBallNumbers,
  setIsHide,
  isHideBack,
  setActiveButton,
  setHeaderTitle,
  setBackLink,
  setPlay
}) => {
  const [getBouquetgridprice, setBouquetgridprice] = useState(0);
  useEffect(() => {
    // console.log(parameters)
    setBackLink(localStorage.getItem('BackPage'));
    localStorage.setItem('BackPage','LLDJ');
    setBouquetgridprice(parameters.gridprice);
    setHeaderTitle("La Libanaise Des Jeux");
    
  }, []);
  // console.log(parameters);
  const [getShowBouquet, setShowBouquet] = useState(false);
  // const [get8Grids, set8Grids]=useState(false);
  const set8Grids = () => {
    // const lastBall = selectedBalls[selectedBalls.length - 1];
    const bouquetData = {
      bouquet: "B" + 8, // Use the gridNb property instead of balls
      price: parameters.gridprice * 8,
      currency: "LBP",
      withZeed: false,
      isbouquet: true,
    };

    // Get the existing data from local storage
    const existingData = localStorage.getItem("selectedBalls");

    if (existingData) {
      // Parse the existing data and add the new bouquet data
      const newData = [...JSON.parse(existingData), bouquetData];
      localStorage.setItem("selectedBalls", JSON.stringify(newData));
    } else {
      // Create a new array with the bouquet data and store it in local storage
      localStorage.setItem("selectedBalls", JSON.stringify([bouquetData]));
    }
    setActiveButton({ name: "Play" });
  };
  //
  const set1Grid = () => {
    // const lastBall = selectedBalls[selectedBalls.length - 1];
    const bouquetData = {
      bouquet: "B" + 1, // Use the gridNb property instead of balls
      price: parameters.gridprice,
      currency: "LBP",
      withZeed: false,
      isbouquet: true,
    };

    // Get the existing data from local storage
    const existingData = localStorage.getItem("selectedBalls");

    if (existingData) {
      // Parse the existing data and add the new bouquet data
      const newData = [...JSON.parse(existingData), bouquetData];
      localStorage.setItem("selectedBalls", JSON.stringify(newData));
    } else {
      // Create a new array with the bouquet data and store it in local storage
      localStorage.setItem("selectedBalls", JSON.stringify([bouquetData]));
    }
    setActiveButton({ name: "Play" });
  };

  return (
    <>
      <div id="LLDJ" className={`${isHideBack ? "isHideBack" : ""}`}>
        <div className="estimatedPriceSection mt-3">
          <div className="title">Next Loto Estimated Jackpot</div>
          <div className="priceLoto">
            LBP {parseInt(parameters.next_loto_prize).toLocaleString()}
          </div>
          <img src="/build/images/Loto/LotoLogo.png" alt="SmileLOGO" />
        </div>

        <div className="estimatedPriceSection mt-5">
          <div className="title">Next Zeed Estimated Jackpot</div>
          <div className="priceZeed">
            LBP {parseInt(parameters.next_zeed_prize).toLocaleString()}
          </div>
          <img src="/build/images/Loto/zeedLogo.png" alt="SmileLOGO" />
        </div>

        <div className="nextDraw m-4">
          <div className="title">Next Draw #{parameters.next_draw_number}</div>
          <div className="desc">
            {new Date(parameters.next_date).toLocaleDateString("en-US", {
              weekday: "long",
              month: "short",
              day: "numeric",
              year: "numeric",
            })}
          </div>
          <Countdown nextDrawNumber={parameters.next_date} />
        </div>

        <div className="questionsSection mt-3">
          <div className="title">What are you waiting for?</div>
          <button className="PlayBtn" onClick={() => {
            setActiveButton({ name: "Play" });
            setBallNumbers(10);
            setTotalAmount(0);
          setPickYourGrid(true);
          setIsHide(true);
          setPlay(1)
        }}>Play Now</button>
        </div>

        <div className="gridsSwction">
          <div className="itemsSection">
            <div className="items">
              <div className="title">1 GRID</div>
              <div className="price">
                {parseInt(parameters.gridprice).toLocaleString()} LBP
              </div>
              <button
                className="letsPlayBtn"
                onClick={() => {
                  set1Grid(true);
                }}
              >
                PLAY NOW
              </button>
            </div>

            <div className="items redone">
              <div className="image">
                <img src="/build/images/Loto/popular.png" alt="popular" />
              </div>
              <div className="title">8 GRIDS</div>
              <div className="price">
                {parseInt(parameters.gridprice * 8).toLocaleString()} LBP
              </div>
              <button
                className="letsPlayBtn"
                onClick={() => {
                  set8Grids(true);
                }}
              >
                PLAY NOW
              </button>
            </div>

            <div className="items">
              <img src="/build/images/Loto/bouquet.png" alt="bouquet" />
              <div className="title">BOUQUET</div>
              <div className="price"></div>
              <button
                className="letsPlayBtn"
                onClick={() => {
                  setShowBouquet(true);
                  setIsHide(true);
                }}
              >
                PLAY NOW
              </button>
            </div>
          </div>
        </div>

        <div className="directlyPlaySection mt-4">
          <div className="bigTitle">Play directly by ball numbers</div>
          <div className="itemsSection">
            {parameters.gridpricematrix &&
              parameters.gridpricematrix.map((item, index) => (
                <div className="items" key={index}>
                  <div className="nb">{item.numbers}</div>
                  <div className="blurSection"></div>
                  <div className="title">NUMBERS</div>
                  <div className="price">
                    {parseInt(item.price).toLocaleString()}LBP
                  </div>
                  <button
                    className="letsPlayBtn"
                    onClick={() => {
                      setBallNumbers(item.numbers);
                      setTotalAmount(item.price);
                      setPickYourGrid(true);
                      setIsHide(true);
                      setPlay(0);
                    }}
                  >
                    PLAY
                  </button>
                </div>
              ))}
            <div className="emptyDiv"></div>
          </div>
        </div>
      </div>
      {getShowBouquet && (
        <BouquetOptions
          getBouquetgridprice={getBouquetgridprice}
          setShowBouquet={setShowBouquet}
          setIsHide={setIsHide}
          setActiveButton={setActiveButton}
        />
      )}
    </>
  );
};

export default LLDJ;
