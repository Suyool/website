import React, { useEffect, useState } from "react";
import axios from "axios";

const Result = ({ parameters, setHeaderTitle, setBackLink ,getCheckBuy,setCheckBuy }) => {
  const results = parameters.prize_loto_result;
  const data = parameters.prize_loto_perdays;

  const [ getWinBallInitial, setWinBallInitial ] = useState([]);
  const [ getWinBallInitialZeed, setWinBallInitialZeed ] = useState([]);
  const [ getMyGrids, setMyGrids ] = useState([]);
  const [ getMyGridsZeed, setMyGridsZeed ] = useState([]);
  const [ clickedIndex, setClickedIndex ] = useState(0);
  const [ getLastNumber, setLastNumber ] = useState([]);
  const [ getZeedNumber, setZeedNumber ] = useState([]);
  const [ prize1, setprize1 ] = useState([]);
  const [ prize2, setprize2 ] = useState([]);
  const [ prize3, setprize3 ] = useState([]);
  const [ prize4, setprize4 ] = useState([]);
  const [ prize5, setprize5 ] = useState([]);
  const [ zeednumber1, setZeedNumber1 ] = useState([]);
  const [ zeednumber2, setZeedNumber2 ] = useState([]);
  const [ zeednumber3, setZeedNumber3 ] = useState([]);
  const [ zeednumber4, setZeedNumber4 ] = useState([]);
  const [ prize1zeed, setprize1zeed ] = useState([]);
  const [ prize2zeed, setprize2zeed ] = useState([]);
  const [ prize3zeed, setprize3zeed ] = useState([]);
  const [ prize4zeed, setprize4zeed ] = useState([]);

  useEffect(() => {
    localStorage.setItem("BackPage", "LLDJ");
    setBackLink(localStorage.getItem("BackPage"));
    setHeaderTitle("Results");

    if(getCheckBuy){
      handleChangeDate(parameters.prize_loto_result[0]?.drawNumber, 0);
    }

    const resultsnumbers = parameters.prize_loto_win.numbers
      .split(",")
      .map(Number);
    if (parameters.prize_loto_win?.numbers != "") {
      setWinBallInitial(
        parameters.prize_loto_win?.numbers?.split(",").map(Number)
      );
      setWinBallInitialZeed(
        parameters.prize_loto_win?.zeednumbers?.split("").map(Number)
      );
    }
    const lastNumber = resultsnumbers[resultsnumbers.length - 1];
    setLastNumber(lastNumber);
    data.forEach((item) => {
      const parsedGrids = item.gridSelected.map((item) =>
        item["gridSelected"].split(" ").map(Number)
      );

      const zeedSelectedArray = item.gridSelected
        .map((item) => item["zeedSelected"])
        .filter((zeedSelected) => zeedSelected !== null);
      setZeedNumber(zeedSelectedArray);
      const parsedGridsZeed = item.gridSelected.map((item) => {
        const zeedSelected = item["zeedSelected"];
        if (zeedSelected === null) {
          return null;
        } else {
          return zeedSelected.split("").map(Number);
        }
      });
      setMyGridsZeed(parsedGridsZeed);
      // console.log(parsedGrids);

      setMyGrids(parsedGrids);
    });
    setClickedIndex(0);
  }, []);

  useEffect(() => {
    setprize1(parameters.prize_loto_win.prize1);
    setprize2(parameters.prize_loto_win.prize2);
    setprize3(parameters.prize_loto_win.prize3);
    setprize4(parameters.prize_loto_win.prize4);
    setprize5(parameters.prize_loto_win.prize5);

    setZeedNumber1(parameters.prize_loto_win.zeednumbers);
    setZeedNumber2(parameters.prize_loto_win.zeednumbers2);
    setZeedNumber3(parameters.prize_loto_win.zeednumbers3);
    setZeedNumber4(parameters.prize_loto_win.zeednumbers4);

    setprize1zeed(parameters.prize_loto_win.prize1zeed);
    setprize2zeed(parameters.prize_loto_win.prize2zeed);
    setprize3zeed(parameters.prize_loto_win.prize3zeed);
    setprize4zeed(parameters.prize_loto_win.prize4zeed);
  }, []);

  const [ selectedMonthYear, setSelectedMonthYear ] = useState("");
  const [ startIndex, setStartIndex ] = useState(0);

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
    setMyGrids([]);
    setSelectedMonthYear(event.target.value);
    setStartIndex(0);
    setClickedIndex(null);
  };


  const handlePrevious = () => {
    setStartIndex((prevIndex) => Math.max(prevIndex - 1, 0));
    setClickedIndex(null);
  };

  const handleNext = () => {
    setStartIndex((prevIndex) =>
      Math.min(prevIndex + 1, filteredData.length - 4)
    );
    setClickedIndex(null);
  };

  const handleChangeDate = (item, index) => {
    axios
      .post("/loto", {
        drawNumber: item,
      })
      .then((response) => {
        if (response.data.parameters.prize_loto_win.numbers != "") {
          setWinBallInitial(
            response.data.parameters.prize_loto_win.numbers
              .split(",")
              .map(Number)
          );
          setWinBallInitialZeed(
            response.data.parameters.prize_loto_win.zeednumbers
              .split("")
              .map(Number)
          );
        } else {
          setWinBallInitial([]);
          setWinBallInitialZeed([]);
        }
        const parsedGrids =
          response?.data?.parameters?.prize_loto_perdays[0]?.gridSelected?.map(
            (item) => item["gridSelected"].split(" ").map(Number)
          );
        const resultsnumbers = response.data.parameters.prize_loto_win.numbers
          .split(",")
          .map(Number);

        setMyGrids(parsedGrids);
        const lastNumber = resultsnumbers[resultsnumbers.length - 1];
        setLastNumber(lastNumber);

        const zeedSelectedArray =
          response?.data?.parameters?.prize_loto_perdays[0]?.gridSelected
            ?.map((item) => item["zeedSelected"])
            .filter((zeedSelected) => zeedSelected !== null);
        setZeedNumber(zeedSelectedArray);
        const parsedGridsZeed =
          response?.data?.parameters?.prize_loto_perdays[0]?.gridSelected?.map(
            (item) => {
              const zeedSelected = item["zeedSelected"];
              if (zeedSelected === null) {
                return null;
              } else {
                return zeedSelected.split("").map(Number);
              }
            }
          );
        setMyGridsZeed(parsedGridsZeed);
        console.log(response.data.parameters.prize_loto_win.prize2);

        setprize1(response.data.parameters.prize_loto_win.prize1);
        setprize2(response.data.parameters.prize_loto_win.prize2);
        setprize3(response.data.parameters.prize_loto_win.prize3);
        setprize4(response.data.parameters.prize_loto_win.prize4);
        setprize5(response.data.parameters.prize_loto_win.prize5);

        setZeedNumber1(response.data.parameters.prize_loto_win.zeednumbers);
        setZeedNumber2(response.data.parameters.prize_loto_win.zeednumbers2);
        setZeedNumber3(response.data.parameters.prize_loto_win.zeednumbers3);
        setZeedNumber4(response.data.parameters.prize_loto_win.zeednumbers4);

        setprize1zeed(response.data.parameters.prize_loto_win.prize1zeed);
        setprize2zeed(response.data.parameters.prize_loto_win.prize2zeed);
        setprize3zeed(response.data.parameters.prize_loto_win.prize3zeed);
        setprize4zeed(response.data.parameters.prize_loto_win.prize4zeed);

        setClickedIndex(index);
      })
      .catch((error) => {
        console.log(error);
      });
  };

  useEffect(() => {
    setSelectedMonthYear(uniqueFilters[0]);
  }, []);
  return (
    <div id="Result">
      <div className="resultTopSection mt-4">
        <div className="title">Draw Numbers</div>
        <div className="ballSection mt-3">
          {getWinBallInitial.length > 0 &&
            getWinBallInitial.map((item, index) => (
              <span key={index}>{item}</span>
            ))}
        </div>
        <div className="ballSectionZeed mt-3">
          {getWinBallInitialZeed.length > 0 &&
            getWinBallInitialZeed.map((item, index) => (
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
            .map((grid, index) => ({ grid, index }))
            .sort((a, b) => {
              const aHasWin =
                getWinBallInitial.filter((winBall) => a.grid.includes(winBall))
                  .length >= 3;
              const bHasWin =
                getWinBallInitial.filter((winBall) => b.grid.includes(winBall))
                  .length >= 3;
              return bHasWin - aHasWin || a.index - b.index;
            })
            .map(({ grid, index }) => (
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
                        ).length == 7 &&
                        parseInt(prize1).toLocaleString()}
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
                        ).length == 3 && parseInt(prize5).toLocaleString()}
                        {}
                     &nbsp;Won
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
                          getMyGridsZeed[index].map((Zeed, ZeedIndex) => {
                            const zeedNumber = getZeedNumber[index];
                            const zeedChar = zeedNumber?.charAt(ZeedIndex);
                            const isWin =
                              getWinBallInitialZeed.includes(Zeed) &&
                              ((zeedChar === zeednumber1.charAt(ZeedIndex) &&
                                ZeedIndex < 5) || // First 5 balls
                                (zeedChar ===
                                  zeednumber2.charAt(ZeedIndex - 1) &&
                                  ZeedIndex >= 1) ||
                                (zeedChar ===
                                  zeednumber3.charAt(ZeedIndex - 2) &&
                                  ZeedIndex >= 2) ||
                                (zeedChar ===
                                  zeednumber4.charAt(ZeedIndex - 3) &&
                                  ZeedIndex >= 3)); // Second ball to last

                            return (
                              <span
                                key={ZeedIndex}
                                className={`${isWin ? "win" : ""}`}
                              >
                                {Zeed}
                              </span>
                            );
                          })}
                      </div>
                    </div>
                    {getMyGridsZeed[index] &&
                    getWinBallInitialZeed.length > 0 &&
                    (getZeedNumber[index]?.substring(0, 5) === zeednumber1 ||
                      getZeedNumber[index]?.substring(1, 5) === zeednumber2 ||
                      getZeedNumber[index]?.substring(2, 5) === zeednumber3 ||
                      getZeedNumber[index]?.substring(3, 5) === zeednumber4) ? (
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
                                      ? parseInt(prize4zeed).toLocaleString() + ' '
                                      : " "
                              : " "}
                           &nbsp;Won
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
