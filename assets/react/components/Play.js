import React from "react";

const Play = () => {

    return (
        <div id="Play">
            <h3 className="gridplays">How many lottery grids do you want to play?</h3>

            <div className="gridborder">
                <div className="header">
                    <span><img src="/build/images/Loto/LotoGrid.png" alt="loto" />GRID 1</span>
                    <span className="right">PLAY ZEED (+ L.L 5,000 ) CHECKBOX</span>
                </div>
                <div className="body">
                    <div className="ballSection mt-2">
                        <span>11</span>
                        <span>16</span>
                        <span>18</span>
                        <span>27</span>
                        <span>29</span>
                        <span>42</span>
                    </div>
                    <div className="edit"><img src="/build/images/Loto/edit.png" /></div>
                </div>
                <div className="footer">
                    <span className="price">L.L 200,000</span>
                    <span className="delete"><img src="/build/images/Loto/trash.png" /></span>
                </div>
            </div>

            <div className="addGrid">
                <span>+</span>
            </div>

            <div className="wantToPlay">
                <div className="title">How often do you want to play?</div>

                <div className="listSection">

                    <div className="listItem">
                        <div className="checkbox">true</div>
                        <div className="playNB">
                            <div className="titleNb">Play Once</div>
                            <div className="desc">Thursday X at 9:00PM</div>
                        </div>
                        <div className="price">200,000 LBP</div>
                    </div>

                    <div className="listItem">
                        <div className="checkbox">true</div>
                        <div className="playNB">
                            <div className="titleNb">Play Once</div>
                            <div className="desc">Thursday X at 9:00PM</div>
                        </div>
                        <div className="price">200,000 LBP</div>
                    </div>

                    <div className="listItem">
                        <div className="checkbox">true</div>
                        <div className="playNB">
                            <div className="titleNb">Play Once</div>
                            <div className="desc">Thursday X at 9:00PM</div>
                        </div>
                        <div className="price">200,000 LBP</div>
                    </div>

                    <div className="listItem">
                        <div className="checkbox">true</div>
                        <div className="playNB">
                            <div className="titleNb">Play Once</div>
                            <div className="desc">Thursday X at 9:00PM</div>
                        </div>
                        <div className="price">200,000 LBP</div>
                    </div>

                </div>
            </div>

            <div className="btnSection">
                <div className="Total">
                    <span>TOTAL</span>
                    <span>L.L 200,000</span>
                </div>
                <button>Checkout</button>
            </div>

        </div>
    );
};

export default Play;