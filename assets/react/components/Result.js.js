import React, { useEffect, useState } from "react";
import data from "./result.json";

const Result = ({ parameters }) => {
  console.log(parameters.prize_loto_win.numbers);
  const [getWinBallInitial, setWinBallInitial] = useState([]);
  const prize1 = parameters.prize_loto_win.prize1;
  const prize2 = parameters.prize_loto_win.prize2;
  const prize3 = parameters.prize_loto_win.prize3;
  const prize4 = parameters.prize_loto_win.prize4;
  const prize5 = parameters.prize_loto_win.prize5;

  const [getMyGrids, setMyGrids] = useState([
    [11, 16, 17, 42, 31, 18, 19, 14],
    [11, 12, 16, 22, 35, 15],
    [1, 12, 9, 2, 6, 14],
    [11, 16, 17, 1, 2, 3],
  ]);

  useEffect(() => {
    setWinBallInitial(parameters.prize_loto_win.numbers.split(',').map(Number));
  }, []);

  
  const [selectedMonthYear, setSelectedMonthYear] = useState("");
  const [startIndex, setStartIndex] = useState(0);

  const uniqueFilters = [];

  data.forEach((item) => {
    const filter = item.month + " " + item.year;
    if (!uniqueFilters.includes(filter)) {
      uniqueFilters.push(filter);
    }
  });

  const filteredData = data.filter(
    (item) =>
      item.month + " " + item.year === selectedMonthYear
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

  useEffect(()=>{
    setSelectedMonthYear(uniqueFilters[0])
  },[])


  return (
    <div id="Result">
      <div className="resultTopSection mt-4">
        <div className="title">Draw Numbers</div>
        <div className="ballSection mt-2">
          {getWinBallInitial.map((item, index) => (
            <span key={index}>{item}</span>
          ))}
        </div>
      </div>

      <div className="nextDrawSection mt-4">
         <div className="filter-section">
          <select className="selectDesign" value={selectedMonthYear} onChange={handleMonthYearChange}>
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
            {filteredData.slice(startIndex, startIndex + 4).map((item, index) => (
              <div className="item" key={index}>
                <div className="time">{item.day}</div>
                <div className="day">{item.date.substring(0, 3)}</div>
              </div>
            ))}
          </div>
          <div className="goNext" onClick={handleNext}>
            <img src="/build/images/Loto/goNext.png" alt="goNext" />
          </div>
        </div>

        {getMyGrids
          .sort((a, b) => {
            const aHasWin =
              getWinBallInitial.filter((winBall) => a.includes(winBall))
                .length >= 3;
            const bHasWin =
              getWinBallInitial.filter((winBall) => b.includes(winBall))
                .length >= 3;
            return bHasWin - aHasWin;
          })
          .map((grid, index) => (
            <div className="winnweSection" key={index}>
              <div className="winnweHeader">
                <div>
                  <img src="/build/images/Loto/LotoLogo.png" alt="SmileLOGO" />
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
                    {getWinBallInitial.filter((winBall) =>
                      grid.includes(winBall)
                    ).length == 7 && parseInt(prize1).toLocaleString()}
                    {getWinBallInitial.filter((winBall) =>
                      grid.includes(winBall)
                    ).length == 6 && parseInt(prize2).toLocaleString()}
                    {getWinBallInitial.filter((winBall) =>
                      grid.includes(winBall)
                    ).length == 5 && parseInt(prize3).toLocaleString()}
                    {getWinBallInitial.filter((winBall) =>
                      grid.includes(winBall)
                    ).length == 4 && parseInt(prize4).toLocaleString()}
                    {getWinBallInitial.filter((winBall) =>
                      grid.includes(winBall)
                    ).length == 3 && parseInt(prize5).toLocaleString()}
                  </div>
                  <div className="img">
                    <img src="/build/images/Loto/trofie.png" alt="SmileLOGO" />
                  </div>
                </div>
              ) : (
                <div className="NoWinnweFooter">
                  <div>No Wins </div>
                </div>
              )}
            </div>
          ))}
      </div>
    </div>
  );
};

export default Result;
