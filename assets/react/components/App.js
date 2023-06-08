import React, { useState } from "react";

const App = ({ v }) => {
    const [activeButton, setActiveButton] = useState({ name: "LLDJ" });
    const handleButtonClick = (name) => {
        setActiveButton({ name: name });
    };

    return (
        <div id="LotoBody">
            <div>
                {activeButton.name === "LLDJ" && "LLDJ"}
                {activeButton.name === "Play" && "Play"}
                {activeButton.name === "Result" && "Result"}
            </div>

            <div className="navigation">
                <button className={`NavBtn ${activeButton.name === "LLDJ" ? "active" : ""}`} onClick={() => { handleButtonClick("LLDJ") }}>
                    <img src={activeButton.name === "LLDJ" ? "/build/images/Loto/LLDJSlected.png" : "/build/images/Loto/LLDJ.png"} alt="LLDJ" />
                    <div className={`title ${activeButton.name === "LLDJ" ? "activeTitle" : ""}`}>LLDJ</div>
                </button>
                <button className={`NavBtn ${activeButton.name === "Play" ? "PlayActive" : ""}`} onClick={() => { handleButtonClick("Play") }}>
                    <img src={activeButton.name === "Play" ? "/build/images/Loto/playSelected.png" : "/build/images/Loto/play.png"} alt="Play" />
                    <div className={`title ${activeButton.name === "Play" ? "activeTitle" : ""}`}>{activeButton.name === "Play" ? "" : "Play"}</div>
                </button>
                <button className={`NavBtn ${activeButton.name === "Result" ? "active" : ""}`} onClick={() => { handleButtonClick("Result") }}>
                    <img src={activeButton.name === "Result" ? "/build/images/Loto/resultSelected.png" : "/build/images/Loto/result.png"} alt="Result" />
                    <div className={`title ${activeButton.name === "Result" ? "activeTitle" : ""}`}>Result</div>
                </button>
            </div>
        </div>
    );
};

export default App;