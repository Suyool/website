import React from "react";

const BottomNav = ({ activeButton, setActiveButton }) => {

    const handleButtonClick = (name) => {
        setActiveButton({ name: name });
    };
    return (
        <div className="navigation">
            <div className="subCont">
                <button className={`NavBtn ${activeButton.name === "LLDJ" ? "active" : ""}`} onClick={() => { handleButtonClick("LLDJ") }}>
                    <img src={activeButton.name === "LLDJ" ? "/build/images/Loto/LLDJSlected.png" : "/build/images/Loto/LLDJ.png"} alt="LLDJ" />
                    <div className={`title ${activeButton.name === "LLDJ" ? "activeTitle" : ""}`}>LLDJ</div>
                </button>
                <button className={` ${activeButton.name === "Play" ? "PlayActive" : "NavBtnPlay"}`} onClick={() => { handleButtonClick("Play") }}>
                    <img src={activeButton.name === "Play" ? "/build/images/Loto/playSelected.png" : "/build/images/Loto/play.png"} alt="Play" />
                    <div className={`title ${activeButton.name === "Play" ? "activeTitle" : ""}`}>{activeButton.name === "Play" ? "" : "PLAY"}</div>
                </button>
                <button className={`NavBtn ${activeButton.name === "Result" ? "active" : ""}`} onClick={() => { handleButtonClick("Result") }}>
                    <img src={activeButton.name === "Result" ? "/build/images/Loto/resultSelected.png" : "/build/images/Loto/result.png"} alt="Result" />
                    <div className={`title ${activeButton.name === "Result" ? "activeTitle" : ""}`}>RESULT</div>
                </button>
            </div>
        </div>
    );
};

export default BottomNav;