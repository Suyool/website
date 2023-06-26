import React, { useState, useEffect } from "react";
import BottomNav from "./components/BottomNav";
import LLDJ from "./components/LLDJ";
import Play from "./components/Play";
import Result from "./components/Result.js";
import PickYourGrid from "./components/PickYourGrid";
import Buy from "./components/Buy";

const App = ({ parameters }) => {
    const [activeButton, setActiveButton] = useState({ name: "LLDJ" });
    const [getPickYourGrid, setPickYourGrid] = useState(false);
    const [getBallNumbers, setBallNumbers] = useState(0);
    const [getTotalAmount, setTotalAmount] = useState(0);

    const [getBallPlayed, setBallPlayed] = useState([]);
    const [isHideBack, setIsHide] = useState(false);

    const [getDataGetting, setDataGetting] = useState("");
    const selectedBallsToShow = localStorage.getItem("selectedBalls");

    const [getDisabledBtn, setDisabledBtn] = useState(
        selectedBallsToShow == null ||
        JSON.parse(selectedBallsToShow).length === 0
    );

    useEffect(() => {
        window.handleCheckout = (message) => {
            setDataGetting(message);
        };
    }, []);
    return (
        <div id="LotoBody">

            <div className="scrolableView">
                <img className="mt-3" src="/build/images/Loto/LibanaiseJeux.png" alt="La Libanaise des Jeux" />
                {getDataGetting != null && <h1>data Getting: {getDataGetting}</h1>}

                {activeButton.name === "LLDJ" && <LLDJ parameters={parameters} setBallNumbers={setBallNumbers} setTotalAmount={setTotalAmount} setPickYourGrid={setPickYourGrid} setIsHide={setIsHide} isHideBack={isHideBack} />}
                {activeButton.name === "Play" && <Play setBallPlayed={setBallPlayed} setPickYourGrid={setPickYourGrid} setTotalAmount={setTotalAmount} setBallNumbers={setBallNumbers} setActiveButton={setActiveButton} setDisabledBtn={setDisabledBtn} getDisabledBtn={getDisabledBtn} />}
                {activeButton.name === "Result" && <Result parameters={parameters} />}

                {activeButton.name === "Buy" && <Buy setDisabledBtn={setDisabledBtn} />}
            </div>

            {getPickYourGrid && <PickYourGrid setPickYourGrid={setPickYourGrid} getBallNumbers={getBallNumbers} getTotalAmount={getTotalAmount} getBallPlayed={getBallPlayed} setIsHide={setIsHide} />}
            <BottomNav activeButton={activeButton} setActiveButton={setActiveButton} />
        </div>
    );
};

export default App;