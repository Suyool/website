import React, { useEffect, useState } from "react";
// import data from "./result.json";
import axios from "axios";

const Result = ({ parameters }) => {
  const results = parameters.prize_loto_result;
  const data = parameters.prize_loto_perdays;
  // console.log(data);
  // const grids = [['11,23,27,3,29,39']];
  // console.log(data);
  // console.log(parameters.prize_loto_win.numbers);

  const [getWinBallInitial, setWinBallInitial] = useState([]);
  const [getWinBallInitialZeed, setWinBallInitialZeed] = useState([]);
  const [getMyGrids, setMyGrids] = useState([]);
  const [getMyGridsZeed, setMyGridsZeed] = useState([]);
  const [clickedIndex, setClickedIndex] = useState([]);
  const [getLastNumber, setLastNumber] = useState([]);
  const [getZeedNumber, setZeedNumber] = useState([]);

  const prize1 = parameters.prize_loto_win.prize1;
  const prize2 = parameters.prize_loto_win.prize2;
  const prize3 = parameters.prize_loto_win.prize3;
  const prize4 = parameters.prize_loto_win.prize4;
  const prize5 = parameters.prize_loto_win.prize5;

  const zeednumber1 = parameters.prize_loto_win.zeednumbers;
  const zeednumber2 = parameters.prize_loto_win.zeednumbers2;
  const zeednumber3 = parameters.prize_loto_win.zeednumbers3;
  const zeednumber4 = parameters.prize_loto_win.zeednumbers4;

  const prize1zeed = parameters.prize_loto_win.prize1zeed;
  const prize2zeed = parameters.prize_loto_win.prize2zeed;
  const prize3zeed = parameters.prize_loto_win.prize3zeed;
  const prize4zeed = parameters.prize_loto_win.prize4zeed;

  // useEffect(() => {
  //   console.log(getMyGrids);
  // }, [getMyGrids]);

  // data.forEach((item) => {
  //   useEffect(() => {
  //     setMyGrids(item.gridSelected);
  //   }, []);
  // });

  useEffect(() => {
    const resultsnumbers = parameters.prize_loto_win.numbers
      .split(",")
      .map(Number);
    setWinBallInitial(parameters.prize_loto_win.numbers.split(",").map(Number));
    setWinBallInitialZeed(
      parameters.prize_loto_win.zeednumbers.split("").map(Number)
    );
    const lastNumber = resultsnumbers[resultsnumbers.length - 1];
    setLastNumber(lastNumber);
    // console.log(lastNumber);
    // setMyGrids(grids.split(',').map(Number));
    data.forEach((item) => {
      // console.log(item)
      const parsedGrids = item.gridSelected.map((item) =>
        item["gridSelected"].split(" ").map(Number)
      );

      const zeedSelectedArray = item.gridSelected
        .map((item) => item["zeedSelected"])
        .filter((zeedSelected) => zeedSelected !== null);
      // const parsedGridsZeed = item.gridSelected.map((item) =>
      //   item["zeedSelected"].split("").map(Number)
      // );
      setZeedNumber(zeedSelectedArray);
      const parsedGridsZeed = item.gridSelected.map((item) => {
        const zeedSelected = item["zeedSelected"];
        // console.log(zeedSelected)
        if (zeedSelected === null) {
          return null;
        } else {
          return zeedSelected.split("").map(Number);
        }
      });
      setMyGridsZeed(parsedGridsZeed);

      setMyGrids(parsedGrids);
    });
    setClickedIndex(0);
  }, []);

  const [selectedMonthYear, setSelectedMonthYear] = useState("");
  const [startIndex, setStartIndex] = useState(0);

  const uniqueFilters = [];

  results.forEach((item) => {
    const filter = item.month + " " + item.year;
    if (!uniqueFilters.includes(filter)) {
      uniqueFilters.push(filter);
    }
  });

  const filteredData = results.filter(
    (item) => item.month + " " + item.year === selectedMonthYear
  );

  const handleMonthYearChange = (event) => {
    setSelectedMonthYear(event.target.value);
    setStartIndex(0); // Reset the startIndex when month or year changes
  };

  const handlePrevious = () => {
    setStartIndex((prevIndex) => Math.max(prevIndex - 1, 0));
  };

  const handleNext = () => {
    setStartIndex((prevIndex) =>
      Math.min(prevIndex + 1, filteredData.length - 4)
    );
  };

  const handleChangeDate = (item, index) => {
    axios
      .post("/loto", {
        drawNumber: item,
      })
      .then((response) => {
        console.log(response.data);
        setWinBallInitial(
          response.data.parameters.prize_loto_win.numbers.split(",").map(Number)
        );
        setWinBallInitialZeed(
          response.data.parameters.prize_loto_win.zeednumbers.split("").map(Number)
        );
        const parsedGrids =
          response?.data?.parameters?.prize_loto_perdays[0]?.gridSelected?.map(
            (item) => item['gridSelected'].split(" ").map(Number)
          );
        const resultsnumbers = response.data.parameters.prize_loto_win.numbers
          .split(",")
          .map(Number);

        setMyGrids(parsedGrids);
        const lastNumber = resultsnumbers[resultsnumbers.length - 1];
        setLastNumber(lastNumber);

        const zeedSelectedArray = response?.data?.parameters?.prize_loto_perdays[0]?.gridSelected?.map((item) => item["zeedSelected"])
        .filter((zeedSelected) => zeedSelected !== null);
      // const parsedGridsZeed = item.gridSelected.map((item) =>
      //   item["zeedSelected"].split("").map(Number)
      // );
      setZeedNumber(zeedSelectedArray);
      const parsedGridsZeed = response?.data?.parameters?.prize_loto_perdays[0]?.gridSelected?.map((item) => {

        const zeedSelected = item["zeedSelected"];
        // console.log(zeedSelected)
        if (zeedSelected === null) {
          return null;
        } else {
          return zeedSelected.split("").map(Number);
        }
      });
      setMyGridsZeed(parsedGridsZeed);


        setClickedIndex(index);
      })
      .catch((error) => {
        console.log(error);
      });
  };

  useEffect(() => {
    setSelectedMonthYear(uniqueFilters[0]);
  }, []);
  // console.log(getZeedNumber[0]);
  // console.log(getZeedNumber[0].indexOf(getZeedNumber[0]))
  return (
    <div id="Result">
      <div className="resultTopSection mt-4">
        <div className="title">Draw Numbers</div>
        <div className="ballSection mt-3">
          {getWinBallInitial.map((item, index) => (
            <span key={index}>{item}</span>
          ))}
        </div>
        <div className="ballSectionZeed mt-3">
          {getWinBallInitialZeed.map((item, index) => (
            <span key={index}>{item}</span>
          ))}
          <span className="nouse"></span>
          <span className="nouse"></span>
        </div>
      </div>

      <div className="nextDrawSection mt-4">
        <div className="filter-section">
          <select
            className="selectDesign"
            value={selectedMonthYear}
            onChange={handleMonthYearChange}
          >
            {uniqueFilters.map((item) => (
              <option key={item} value={item}>
                {item}
              </option>
            ))}
          </select>
        </div>

        <div className="dayDrow">
          <div className="goNext" onClick={handlePrevious}>
            <img src="/build/images/Loto/goPrevious.png" alt="GoPrevious" />
          </div>
          <div className="items">
            {filteredData
              .slice(startIndex, startIndex + 4)
              .map((item, index) => (
                <div
                  // className="item"
                  className={`item ${clickedIndex === index ? "clicked" : ""}`}
                  key={index}
                  onClick={() => {
                    handleChangeDate(item.drawNumber, index);
                  }}
                >
                  <div className="time">{item.day}</div>
                  <div className="day">{item.date.substring(0, 3)}</div>
                </div>
              ))}
          </div>
          <div className="goNext" onClick={handleNext}>
            <img src="/build/images/Loto/goNext.png" alt="goNext" />
          </div>
        </div>

        {getMyGrids &&
          getMyGrids
            // .sort((a, b) => {
            //   const aHasWin =
            //     getWinBallInitial.filter((winBall) => a.includes(winBall))
            //       .length >= 3;
            //   const bHasWin =
            //     getWinBallInitial.filter((winBall) => b.includes(winBall))
            //       .length >= 3;
            //   return bHasWin - aHasWin;
            // })
            .map((grid, index) => (
              <div className="winnweSection" key={index}>
                <div className="winnweHeader">
                  <div>
                    <img
                      src="/build/images/Loto/LotoLogo.png"
                      alt="SmileLOGO"
                    />
                    <span>BASIC</span>
                  </div>
                </div>
                <div className="winnweBody">
                  <div className="ballSection mt-2">
                    {grid.map((ball, ballIndex) => (
                      <span
                        key={ballIndex}
                        className={`${
                          getWinBallInitial.includes(ball) ? "win" : ""
                        }`}
                      >
                        {ball}
                      </span>
                    ))}
                  </div>
                </div>
                {getWinBallInitial.filter((winBall) => grid.includes(winBall))
                  .length >= 3 ? (
                  <div className="winnweFooter">
                    <div className="price">
                      <span>L.L </span>
                      {getWinBallInitial.filter((winBall) =>
                        grid.includes(winBall)
                      ).length == 6 &&
                        !grid.includes(getLastNumber) &&
                        parseInt(prize1).toLocaleString()}
                      {getWinBallInitial.filter((winBall) =>
                        grid.includes(winBall)
                      ).length == 6 &&
                        grid.includes(getLastNumber) &&
                        parseInt(prize2).toLocaleString()}
                      {getWinBallInitial.filter((winBall) =>
                        grid.includes(winBall)
                      ).length == 5 && parseInt(prize3).toLocaleString()}
                      {getWinBallInitial.filter((winBall) =>
                        grid.includes(winBall)
                      ).length == 4 && parseInt(prize4).toLocaleString()}
                      {getWinBallInitial.filter((winBall) =>
                        grid.includes(winBall)
                      ).length == 3 && parseInt(prize5).toLocaleString()}{" "}
                      Won
                    </div>
                    <div className="img">
                      <img
                        src="/build/images/Loto/trofie.png"
                        alt="SmileLOGO"
                      />
                    </div>
                  </div>
                ) : (
                  <div className="NoWinnweFooter">
                    <div>No Wins </div>
                  </div>
                )}
                {getMyGridsZeed[index] ? (
                  <>
                    <div className="winnweHeader">
                      <div>
                        <img
                          src="/build/images/Loto/zeedLogo.png"
                          alt="SmileLOGO"
                        />
                        <span>Zeed</span>
                      </div>
                    </div>
                    <div className="winnweBody">
                      <div className="ballSectionZeed mt-2">
                        {getMyGridsZeed[index] != null &&
                          getMyGridsZeed[index].map((Zeed, ZeedIndex) => (
                            <span
                              key={ZeedIndex}
                              className={`${
                                getWinBallInitialZeed.includes(Zeed)
                                  ? "win"
                                  : ""
                              }`}
                            >
                              {Zeed}
                            </span>
                          ))}
                      </div>
                    </div>
                    {getMyGridsZeed[index] &&
                    (getZeedNumber[index].substring(0, 5) === zeednumber1 ||
                      getZeedNumber[index].substring(1, 5) === zeednumber2 ||
                      getZeedNumber[index].substring(2, 5) === zeednumber3 ||
                      getZeedNumber[index].substring(3, 5) === zeednumber4) ? (
                      <div className="winnweFooterZeed">
                        <div className="price">
                          <span>L.L </span>
                          {getWinBallInitialZeed.filter((winBallZeed) =>
                            getMyGridsZeed[index].includes(winBallZeed)
                          ).length > 0
                            ? getZeedNumber[index].substring(0, 5) ===
                              zeednumber1
                              ? parseInt(prize1zeed).toLocaleString()
                              : getZeedNumber[index].substring(1, 5) ===
                                zeednumber2
                              ? parseInt(prize2zeed).toLocaleString()
                              : getZeedNumber[index].substring(2, 5) ===
                                zeednumber3
                              ? parseInt(prize3zeed).toLocaleString()
                              : getZeedNumber[index].substring(3, 5) ===
                                zeednumber4
                              ? parseInt(prize4zeed).toLocaleString()
                              : " "
                            : " "}
                          Won
                        </div>
                        <div className="img">
                          <img
                            src="/build/images/Loto/trofie.png"
                            alt="SmileLOGO"
                          />
                        </div>
                      </div>
                    ) : (
                      <div className="NoWinnweFooter">
                        <div>No Wins </div>
                      </div>
                    )}
                  </>
                ) : (
                  <></>
                )}
              </div>
            ))}
      </div>
    </div>
  );
};

export default Result;
