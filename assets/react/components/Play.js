import React, { useState } from "react";

const Play = ({setBallPlayed , setPickYourGrid , setBallNumbers }) => {
    const [getPlayedBalls, setPlayedBalls] = useState(JSON.parse(localStorage.getItem("selectedBalls")) || []);

    const handleDelete = (index) => {
        const updatedBalls = [...getPlayedBalls];
        updatedBalls.splice(index, 1); // Remove the selected balls from the array

        setPlayedBalls(updatedBalls); // Update the state

        // Update the localStorage
        localStorage.setItem("selectedBalls", JSON.stringify(updatedBalls));
    };
    const handleEdit = (index) => {
        setBallPlayed(getPlayedBalls[index])
        setBallNumbers(getPlayedBalls[index].length)
        setPickYourGrid(true)
        // console.log(getPlayedBalls[index].length)
    }

    return (
        <div id="Play">
            <h3 className="gridplays">How many lottery grids do you want to play?</h3>

            {getPlayedBalls && getPlayedBalls.map((balls, index) => (
                <div className="gridborder mt-4" key={index}>
                    <div className="header">
                        <span>
                            <img src="/build/images/Loto/LotoGrid.png" alt="loto" /> GRID {index + 1}
                        </span>
                        <span className="right">
                            PLAY ZEED (+ L.L 5,000)
                            <input className="switch" type="checkbox" />
                        </span>
                    </div>
                    <div className="body">
                        <div className="ballSection mt-2">
                            {balls.map((ball, index) => (
                                <span key={index}>{ball}</span>
                            ))}
                        </div>
                        <div className="edit"  onClick={() => handleEdit(index)}>
                            <img src="/build/images/Loto/edit.png" alt="edit" />
                        </div>
                    </div>
                    <div className="footer">
                        <span className="price">L.L 200,000</span>
                        <span className="delete" onClick={() => handleDelete(index)}>
                            <img src="/build/images/Loto/trash.png" alt="delete" />
                        </span>
                    </div>
                </div>
            ))}

            <div className="addGrid">
                <span>+</span>
            </div>

            <div className="wantToPlay">
                <div className="title">How often do you want to play?</div>

                <div className="listSection">

                    <div className="listItem">
                        <div className="checkbox"><input type="checkbox" /></div>
                        <div className="playNB">
                            <div className="titleNb">Play Once</div>
                            <div className="desc">Thursday X at 9:00PM</div>
                        </div>
                        <div className="price">200,000 LBP</div>
                    </div>

                    <div className="listItem">
                        <div className="checkbox"><input type="checkbox" /></div>
                        <div className="playNB">
                            <div className="titleNb">Play Once</div>
                            <div className="desc">Thursday X at 9:00PM</div>
                        </div>
                        <div className="price">200,000 LBP</div>
                    </div>

                    <div className="listItem">
                        <div className="checkbox"><input type="checkbox" /></div>
                        <div className="playNB">
                            <div className="titleNb">Play Once</div>
                            <div className="desc">Thursday X at 9:00PM</div>
                        </div>
                        <div className="price">200,000 LBP</div>
                    </div>

                    <div className="listItem">
                        <div className="checkbox"><input type="checkbox" /></div>
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