import React, { useEffect, useState } from "react";

const PickYourGrid = ({ setPickYourGrid, getBallNumbers, getTotalAmount, getBallPlayed, setIsHide }) => {
    const [selectedBalls, setSelectedBalls] = useState([]);
    useEffect(() => {
        if (getBallPlayed.length == 0) {
            setSelectedBalls(Array(getBallNumbers).fill(null))
        } else {
            setSelectedBalls(getBallPlayed)
        }
    }, [])

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
            const availableBalls = ballNumbers.filter((ball) => !prevSelectedBalls.includes(ball));
            const randomBalls = [];
            while (randomBalls.length < getBallNumbers) {
                const randomIndex = Math.floor(Math.random() * availableBalls.length);
                randomBalls.push(availableBalls[randomIndex]);
                availableBalls.splice(randomIndex, 1);
            }
            return randomBalls;
        });
    };

    const handleDone = () => {
        // console.log(selectedBalls);
        // console.log(getTotalAmount);
        setIsHide(false);
        // Create an object to store the selected balls and their price
        const ballSet = {
            balls: selectedBalls,
            price: getTotalAmount,
            withZeed: false
        };

        // Retrieve existing data from localStorage
        const existingData = localStorage.getItem('selectedBalls');

        // Parse the retrieved data to an array or initialize an empty array
        const existingBalls = existingData ? JSON.parse(existingData) : [];

        // Append the ballSet object to the existing array
        const updatedBalls = [...existingBalls, ballSet];

        // Store the updated array in localStorage
        localStorage.setItem('selectedBalls', JSON.stringify(updatedBalls));

        setPickYourGrid(false);
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
                    {selectedBalls.map((number, index) => (
                        <div>
                            <span className={`${number !== null ? "active" : ""}`} key={index}>{number}</span>
                            <div className="shadow"></div>
                        </div>
                    ))}
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
                    <div className="thePrice">L.L <div className="big">{parseInt(getTotalAmount).toLocaleString()}</div></div>
                </div>

                <div className="options">
                    <button className="aboutGrid" onClick={handleClearPick}>
                        Clear grid
                    </button>
                    <button className="aboutGrid" onClick={handleQuickPick}>
                        Quick pick
                    </button>
                    <button className="done" onClick={handleDone}>Done</button>
                </div>
            </div>
        </div>
    );
};

export default PickYourGrid;

