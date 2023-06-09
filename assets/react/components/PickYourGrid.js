import React, { useState } from "react";

const PickYourGrid = ({ setPickYourGrid, getBallNumbers, getTotalAmount }) => {
    const [selectedBalls, setSelectedBalls] = useState(Array(getBallNumbers).fill(null));

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
        setPickYourGrid(false)
    };

    const handleCancel = () => {
        setPickYourGrid(false)
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
                        <span key={index}>{number}</span>
                    ))}
                </div>
            </div>
            <div className="bodySectionPick">
                {ballNumbers.map((number) => (
                    <div className="ballCont" key={number}>
                        <button onClick={() => handleBallClick(number)}>
                            <span>{number}</span>
                        </button>
                    </div>
                ))}
            </div>
            <div className="footSectionPick">
                <div className="Total">
                    <span>TOTAL</span>
                    <span>L.L {getTotalAmount}</span>
                </div>

                <div className="options">
                    <button className="aboutGrid" onClick={handleClearPick}>
                        Clear pick
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

