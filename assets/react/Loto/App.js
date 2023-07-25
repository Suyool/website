import React, { useState, useEffect } from "react";
import BottomNav from "./components/BottomNav";
import LLDJ from "./components/LLDJ";
import Play from "./components/Play";
import Result from "./components/Result.js";
import PickYourGrid from "./components/PickYourGrid";
import SuccessModal from "./Modal/Modal/SuccessModal";
import ErrorModal from "./Modal/Modal/ErrorModal";
import Buy from "./components/Buy";
import WarningModal from "./Modal/Modal/WarningModal";

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

    const [getModalName, setModalName] = useState("");
    const [modalShow, setModalShow] = useState(false);
    const [getSuccessModal, setSuccessModal] = useState({ imgPath: "/build/images/Loto//build/images/Loto/success.png", title: "", desc: "" });
    const [getErrorModal, setErrorModal] = useState({ img: "/build/images/Loto//build/images/Loto/error.png", title: "Error Modal", desc: "ErrorModal ErrorModal ErrorModal ErrorModal ErrorModal" });
    const [getWarningModal, setWarningModal] = useState({ imgPath: "/build/images/Loto//build/images/Loto/warning.png", title: "Warning Modal", desc: "Warning Modal" });

    
    return (
        <div id="LotoBody">

            <div className={`scrolableView ${activeButton.name === "Result" && "resultScroll"}`}>
                <img className="mt-3" src="/build/images/Loto/LibanaiseJeux.png" alt="La Libanaise des Jeux" />
                {getDataGetting != null && <h1>data Getting: {getDataGetting}</h1>}

                {activeButton.name === "LLDJ" && <LLDJ parameters={parameters} setBallNumbers={setBallNumbers} setActiveButton={setActiveButton} setTotalAmount={setTotalAmount} setPickYourGrid={setPickYourGrid} setIsHide={setIsHide} isHideBack={isHideBack} />}
                {activeButton.name === "Play" && <Play setBallPlayed={setBallPlayed} setPickYourGrid={setPickYourGrid} setTotalAmount={setTotalAmount} setBallNumbers={setBallNumbers} setActiveButton={setActiveButton} setDisabledBtn={setDisabledBtn} getDisabledBtn={getDisabledBtn} />}
                {activeButton.name === "Result" && <Result parameters={parameters} />}

                {activeButton.name === "Buy" && <Buy parameters={parameters} setDisabledBtn={setDisabledBtn} setModalShow={setModalShow} setModalName={setModalName} setSuccessModal={setSuccessModal} setErrorModal={setErrorModal} setWarningModal={setWarningModal}  />}
            </div>

            {getPickYourGrid && <PickYourGrid setPickYourGrid={setPickYourGrid} getBallNumbers={getBallNumbers} getTotalAmount={getTotalAmount} getBallPlayed={getBallPlayed} setIsHide={setIsHide} setModalShow={setModalShow} setModalName={setModalName} setErrorModal={setErrorModal} />}
            <BottomNav activeButton={activeButton} setActiveButton={setActiveButton} />

            {getModalName === "SuccessModal" && <SuccessModal getSuccessModal={getSuccessModal} show={modalShow} onHide={() => { setModalShow(false); setModalName("") }} />}
            {getModalName === "ErrorModal" && <ErrorModal getErrorModal={getErrorModal} show={modalShow} onHide={() => { setModalShow(false); setModalName("") }} />}
            {getModalName === "WarningModal" && <WarningModal getWarningModal={getWarningModal} show={modalShow} onHide={() => { setModalShow(false); setModalName("") }} />}

        </div>
    );
};

export default App;