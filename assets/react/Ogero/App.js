import React, { useState, useEffect } from "react";
import PayBill from "./PayBill";
import MyBill from "./MyBill";
import Default from "./Default";
import Header from "./Header";
import SuccessModal from "./Modal/SuccessModal";
import ErrorModal from "./Modal/ErrorModal";

const App = ({ parameters }) => {
  const [activeButton, setActiveButton] = useState({ name: "" });
  const [getBackLink, setBackLink] = useState({ name: "" });
  const [getHeaderTitle, setHeaderTitle] = useState("Ogero");
  const [getLandlineData, setLandlineData] = useState({ id: "" });
  const [getLandlineDisplayedData, setLandlineDisplayedData] = useState({});
  const [getLandlineMobile, setLandlineMobile] = useState("");
  const [getDataGetting, setDataGetting] = useState("");

  //Modal Variable
  const [getModalName, setModalName] = useState("");
  const [modalShow, setModalShow] = useState(false);
  const [getSuccessModal, setSuccessModal] = useState({
    imgPath: "/build/images/Ogero//build/images/Ogero/SuccessImg.png",
    title: "",
    desc: "",
  });
  const [getErrorModal, setErrorModal] = useState({
    imgPath: "/build/images/Ogero/ErrorImg.png",
    title: "Error Modal",
    desc: "ErrorModal ErrorModal ErrorModal ErrorModal ErrorModal",
  });

  useEffect(() => {
    setDataGetting("");

    window.handleCheckout = (message) => {
      setDataGetting(message);
    };
  });
  return (
    <div id="OgeroBody">
       <Header
          activeButton={activeButton}
          setActiveButton={setActiveButton}
          getHeaderTitle={getHeaderTitle}
          getBackLink={getBackLink}
          parameters={parameters}
        />
      <div className="scrolableView">
       
        {getModalName === "" && (
          <>
            {activeButton.name === "" && (
              <Default
                activeButton={activeButton}
                setActiveButton={setActiveButton}
                setHeaderTitle={setHeaderTitle}
                setBackLink={setBackLink}
              />
            )}

            {activeButton.name === "PayBill" && (
              <PayBill
                setLandlineMobile={setLandlineMobile}
                setLandlineDisplayedData={setLandlineDisplayedData}
                setLandlineData={setLandlineData}
                setModalShow={setModalShow}
                setErrorModal={setErrorModal}
                setModalName={setModalName}
                activeButton={activeButton}
                setActiveButton={setActiveButton}
                setHeaderTitle={setHeaderTitle}
                setBackLink={setBackLink}
              />
            )}

            {activeButton.name === "MyBill" && (
              <MyBill
                getLandlineMobile={getLandlineMobile}
                getLandlineDisplayedData={getLandlineDisplayedData}
                getLandlineData={getLandlineData}
                setModalShow={setModalShow}
                setErrorModal={setErrorModal}
                setSuccessModal={setSuccessModal}
                setModalName={setModalName}
                activeButton={activeButton}
                setActiveButton={setActiveButton}
                setHeaderTitle={setHeaderTitle}
                setBackLink={setBackLink}
                getDataGetting={getDataGetting}
                parameters={parameters}
              />
            )}
          </>
        )}
      </div>

      {/* Modal */}
      {getModalName === "SuccessModal" && (
        <SuccessModal
          getSuccessModal={getSuccessModal}
          show={modalShow}
          onHide={() => {
            setModalShow(false);
            setModalName("");
            setActiveButton({ name: "" });
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
            setActiveButton({ name: "" });
          }}
        />
      )}
    </div>
  );
};

export default App;
