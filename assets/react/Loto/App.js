import React, { useState, useEffect } from "react";
import BottomNav from "./components/BottomNav.js";
import LLDJ from "./components/LLDJ.js";
import Play from "./components/Play.js";
import Result from "./components/Result.js.js";
import PickYourGrid from "./components/PickYourGrid.js";
import SuccessModal from "./Modal/Modal/SuccessModal.js";
import ErrorModal from "./Modal/Modal/ErrorModal.js";
import Buy from "./components/Buy.js";
import WarningModal from "./Modal/Modal/WarningModal.js";
import Header from "./Header.js";

const App = ({ parameters }) => {
  const [getBackLink, setBackLink] = useState({ name: "" });
  const [getHeaderTitle, setHeaderTitle] = useState("Loto");
  const [getBallNumbersIndex, setBallNumbersIndex] = useState(-1);

  const [activeButton, setActiveButton] = useState({ name: "LLDJ" });
  const [getPickYourGrid, setPickYourGrid] = useState(false);
  const [getBallNumbers, setBallNumbers] = useState(0);
  const [getTotalAmount, setTotalAmount] = useState(0);
  const [getTotalAmountLLDJ, setTotalAmountLLDJ] = useState(0);
  const [getPlay, setPlay] = useState(0);

  const [getBallPlayed, setBallPlayed] = useState([]);
  const [isHideBack, setIsHide] = useState(false);

  const [getDataGetting, setDataGetting] = useState("");
  const selectedBallsToShow = localStorage.getItem("selectedBalls");
  const [getCheckBuy, setCheckBuy] = useState(false);

  const [getDisabledBtn, setDisabledBtn] = useState(
    selectedBallsToShow == null || JSON.parse(selectedBallsToShow).length === 0
  );

  useEffect(() => {
    setDataGetting("");
    const searchParams = new URLSearchParams(window.location.search);
    const idParam = searchParams.get("goto");
    if (idParam) {
      setActiveButton({ name: idParam });
    }

    window.handleCheckout = (message) => {
      setDataGetting(message);
    };
  }, []);

  const [getModalName, setModalName] = useState("");
  const [modalShow, setModalShow] = useState(false);
  const [getSuccessModal, setSuccessModal] = useState({
    imgPath: "/build/images/Loto//build/images/Loto/success.png",
    title: "",
    desc: "",
    deviceType:""
  });
  const [getErrorModal, setErrorModal] = useState({
    img: "/build/images/Loto//build/images/Loto/error.png",
    title: "Error Modal",
    desc: "ErrorModal ErrorModal ErrorModal ErrorModal ErrorModal",
    path: "",
  });
  const [getWarningModal, setWarningModal] = useState({
    imgPath: "/build/images/Loto//build/images/Loto/warning.png",
    title: "Warning Modal",
    desc: "Warning Modal",
    path: "",
  });

  return (
    <>
      <Header
        setBackLink={setBackLink}
        activeButton={activeButton}
        setActiveButton={setActiveButton}
        getHeaderTitle={getHeaderTitle}
        getBackLink={getBackLink}
        parameters={parameters}
      />
      <div id="LotoBody">
        <div
          className={`scrolableView ${
            activeButton.name === "Result" && "resultScroll"
          }`}
          style={getPickYourGrid ? { position: "fixed" } : {}}
        >
          <img
            className="mt-5"
            src="/build/images/Loto/LibanaiseJeux.png"
            alt="La Libanaise des Jeux"
          />

          {activeButton.name === "LLDJ" && (
            <LLDJ
              setHeaderTitle={setHeaderTitle}
              setBackLink={setBackLink}
              parameters={parameters}
              setBallNumbers={setBallNumbers}
              setActiveButton={setActiveButton}
              setPlay={setPlay}
              setTotalAmountLLDJ={setTotalAmountLLDJ}
              setPickYourGrid={setPickYourGrid}
              setIsHide={setIsHide}
              isHideBack={isHideBack}
            />
          )}
          {activeButton.name === "Play" && (
            <Play
              parameters={parameters}
              setHeaderTitle={setHeaderTitle}
              setBackLink={setBackLink}
              setBallPlayed={setBallPlayed}
              setPickYourGrid={setPickYourGrid}
              getPickYourGrid={getPickYourGrid}
              setTotalAmount={setTotalAmount}
              setBallNumbers={setBallNumbers}
              setActiveButton={setActiveButton}
              setDisabledBtn={setDisabledBtn}
              getDisabledBtn={getDisabledBtn}
              getTotalAmount={getTotalAmount}
              setTotalAmountLLDJ={setTotalAmountLLDJ}
              setPlay={setPlay}
              setIsHide={setIsHide}
              setDataGetting={setDataGetting}
              setBallNumbersIndex={setBallNumbersIndex}
              getBallNumbersIndex={getBallNumbersIndex}
              setModalShow={setModalShow}
              setModalName={setModalName}
              setWarningModal={setWarningModal}
            />
          )}
          {activeButton.name === "Result" && (
            <Result
              setHeaderTitle={setHeaderTitle}
              setBackLink={setBackLink}
              parameters={parameters}
              getCheckBuy={getCheckBuy}
              setCheckBuy={setCheckBuy}
              getBallNumbersIndex={getBallNumbersIndex}
              setActiveButton={setActiveButton}
            />
          )}

          {activeButton.name === "Buy" && (
            <Buy
              setHeaderTitle={setHeaderTitle}
              setBackLink={setBackLink}
              parameters={parameters}
              getTotalAmount={getTotalAmount}
              setDisabledBtn={setDisabledBtn}
              setModalShow={setModalShow}
              setModalName={setModalName}
              setSuccessModal={setSuccessModal}
              setErrorModal={setErrorModal}
              setWarningModal={setWarningModal}
              getDataGetting={getDataGetting}
              setDataGetting={setDataGetting}
              setTotalAmount={setTotalAmount}
              setCheckBuy={setCheckBuy}
            />
          )}
        </div>

        {getPickYourGrid && (
          <PickYourGrid
            parameters={parameters}
            setPickYourGrid={setPickYourGrid}
            getBallNumbers={getBallNumbers}
            getTotalAmount={getTotalAmount}
            setTotalAmount={setTotalAmount}
            getTotalAmountLLDJ={getTotalAmountLLDJ}
            setTotalAmountLLDJ={setTotalAmountLLDJ}
            getBallPlayed={getBallPlayed}
            getPlay={getPlay}
            setIsHide={setIsHide}
            setModalShow={setModalShow}
            setModalName={setModalName}
            setErrorModal={setErrorModal}
            setBallNumbersIndex={setBallNumbersIndex}
            getBallNumbersIndex={getBallNumbersIndex}
          />
        )}
        <BottomNav
          activeButton={activeButton}
          setActiveButton={setActiveButton}
        />

        {getModalName === "SuccessModal" && (
          <SuccessModal
            getSuccessModal={getSuccessModal}
            show={modalShow}
            setActiveButton={setActiveButton}
            onHide={() => {
              setModalShow(false);
              setModalName("");
            }}
          />
        )}
        {getModalName === "ErrorModal" && (
          <ErrorModal
            getErrorModal={getErrorModal}
            show={modalShow}
            parameters={parameters}
            onHide={() => {
              setModalShow(false);
              setModalName("");
            }}
          />
        )}
        {getModalName === "WarningModal" && (
          <WarningModal
            setModalShow={setModalShow}
            setModalName={setModalName}
            getWarningModal={getWarningModal}
            show={modalShow}
            setActiveButton={setActiveButton}
            onHide={() => {
              setModalShow(false);
              setModalName("");
            }}
          />
        )}
      </div>
    </>
  );
};

export default App;
