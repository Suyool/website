import React from "react";

const Buy = () => {

    return (
        <div id="Buy">
            <div className="gridborder">
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
                    <span className="delete"><img src="/build/images/Loto/trash.png" /></span>
                </div>
            </div>

            <div className="zeedSection">
                <div className="title">Next Zeed Estimated Jackpot</div>
                <div className="price">LBP 400,000,000</div>
                <div className="desc">Zeed is an additional game played on the Loto grid. It gives you an additional chance to win big. Zeed’s draw is made with Loto’s draw. It is also 2 draws per week.</div>
                <div className="playZeed"><span>PLAY ZEED (+ 5,000 LBP)</span></div>
                <div className="zeedImage"><img src="/build/images/Loto/zeedLogo.png" alt="SmileLOGO" /></div>
            </div>

            <div className="Total">
                <span>TOTAL</span>
                <span>L.L 5,005,000</span>
            </div>
        </div>
    );
};

export default Buy;