import React, { useState } from "react";

const Buy = () => {
    const selectedBallsToShow = localStorage.getItem("selectedBalls");
    const [getPlayedBalls, setPlayedBalls] = useState(
        JSON.parse(selectedBallsToShow) || []
    );

    const handleDelete = (index) => {
        const updatedBalls = [...getPlayedBalls];
        updatedBalls.splice(index, 1); // Remove the selected balls from the array

        setPlayedBalls(updatedBalls); // Update the state

        // Update the localStorage
        localStorage.setItem("selectedBalls", JSON.stringify(updatedBalls));
    };

    return (
        <div id="Buy">
            {getPlayedBalls &&
                getPlayedBalls.map((ballsSet, index) => (
                    <div className="gridborder" key={index}>
                        <div className="header">
                            <span><img src="/build/images/Loto/LotoGrid.png" alt="loto" />Bouquet</span>
                        </div>
                        <div className="body">
                            <div className="ballSection">
                                <span>25 Grids</span>
                            </div>
                        </div>
                        <div className="footer">
                            <span className="price">L.L 5,000,000</span>
                            <span className="delete" onClick={() => handleDelete(index)} ><img src="/build/images/Loto/trash.png" /></span>
                        </div>
                    </div>
                ))}




            <div className="zeedSection">
                <div className="title">Next Zeed Estimated Jackpot</div>
                <div className="price">LBP 400,000,000</div>
                <div className="desc">Zeed is an additional game played on the Loto grid. It gives you an additional chance to win big. Zeed’s draw is made with Loto’s draw. It is also 2 draws per week.</div>
                <div className="playZeed"><span>PLAY ZEED (+ 5,000 LBP)</span></div>
                <div className="zeedImage"><img src="/build/images/Loto/zeedLogo.png" alt="SmileLOGO" /></div>
            </div>

            <div id="Total">
                <span>TOTAL</span>
                <div className="thePrice">L.L <div className="big">200,000</div></div>
            </div>

            <button className="BuyBtn" onClick={() => { console.log("Buy") }}>
                Buy
            </button>
        </div>
    );
};

export default Buy;