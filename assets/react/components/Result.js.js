import React, { useEffect, useState } from "react";

const Result = () => {
  const [getWinBallInitial, setWinBallInitial] = useState([]);
  const [getMyGrids, setMyGrids] = useState([
    [11, 16, 17, 42, 31, 18, 19, 14],
    [11, 12, 15, 22, 35, 15],
    [1, 12, 9, 2, 6, 14],
    [11, 16, 17, 1, 2, 3],
  ]);
  useEffect(() => {
    setWinBallInitial([11, 16, 17, 42, 25, 18]);
  }, []);

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
        <div className="selectTime">May 2023</div>

        <div className="dayDrow">
          <div className="goNext">
            <img src="/build/images/Loto/goNext.png" alt="goNext" />
          </div>
          <div className="items">
            <div className="item" onClick={()=>{
                 setWinBallInitial(Array.from({ length: 6 }, () => Math.floor(Math.random() * 42)));
            }}>
              <div className="time">14</div>
              <div className="day">Mon</div>
            </div>
            <div className="item">
              <div className="time">14</div>
              <div className="day">Mon</div>
            </div>
            <div className="item">
              <div className="time">14</div>
              <div className="day">Mon</div>
            </div>
            <div className="item">
              <div className="time">14</div>
              <div className="day">Mon</div>
            </div>
            <div className="item">
              <div className="time">14</div>
              <div className="day">Mon</div>
            </div>
          </div>
          <div className="goNext">
            <img src="/build/images/Loto/goNext.png" alt="goNext" />
          </div>
        </div>

        {getMyGrids.sort((a, b) => {
    const aHasWin = getWinBallInitial.filter((winBall) => a.includes(winBall)).length > 2;
    const bHasWin = getWinBallInitial.filter((winBall) => b.includes(winBall)).length > 2;
    return bHasWin - aHasWin;
  }).map((grid, index) => (
          <div className="winnweSection" key={index}>
            <div className="winnweHeader">
              <div>
                <img src="/build/images/Loto/LotoLogo.png" alt="SmileLOGO" />
                <span>BASIC</span>
              </div>
            </div>
            <div className="winnweBody">
              <div  className="ballSection mt-2">
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
              .length > 2 ? (
              <div className="winnweFooter">
                <div className="price">L.L 2,000,000 won</div>
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
