import React, { useState } from "react";
import BottomNav from "./components/BottomNav";
import LLDJ from "./components/LLDJ";
import Play from "./components/Play";
import Result from "./components/Result.js";
import PickYourGrid from "./components/PickYourGrid";

const App = ({ parameters }) => {
    const [activeButton, setActiveButton] = useState({ name: "LLDJ" });
    const [getPickYourGrid, setPickYourGrid] = useState(true);

    return (
        <div id="LotoBody">

            <div className="scrolableView">
                <img src="/build/images/Loto/LibanaiseJeux.png" alt="La Libanaise des Jeux" />

                {activeButton.name === "LLDJ" && <LLDJ parameters={parameters} />}
                {activeButton.name === "Play" && <Play />}
                {activeButton.name === "Result" && <Result />}
            </div>

            {getPickYourGrid && <PickYourGrid />}
            <BottomNav activeButton={activeButton} setActiveButton={setActiveButton} />
        </div>
    );
};

export default App;