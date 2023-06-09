import React, { useState } from "react";
import BottomNav from "./components/BottomNav";
import LLDJ from "./components/LLDJ";
import Play from "./components/Play";
import Result from "./components/Result.js";
import PickYourGrid from "./components/PickYourGrid";

const App = ({ parameters }) => {
    const [activeButton, setActiveButton] = useState({ name: "LLDJ" });
    const [getPickYourGrid, setPickYourGrid] = useState(false);
    const [getBallNumbers, setBallNumbers] = useState(0);
    const [getTotalAmount, setTotalAmount] = useState(0);

    return (
        <div id="LotoBody">

            <div className="scrolableView">
                <img src="/build/images/Loto/LibanaiseJeux.png" alt="La Libanaise des Jeux" />

                {activeButton.name === "LLDJ" && <LLDJ parameters={parameters} setBallNumbers={setBallNumbers} setTotalAmount={setTotalAmount} setPickYourGrid={setPickYourGrid} />}
                {activeButton.name === "Play" && <Play />}
                {activeButton.name === "Result" && <Result />}
            </div>

            {getPickYourGrid && <PickYourGrid setPickYourGrid={setPickYourGrid} getBallNumbers={getBallNumbers} getTotalAmount={getTotalAmount} />}
            <BottomNav activeButton={activeButton} setActiveButton={setActiveButton} />
        </div>
    );
};

export default App;