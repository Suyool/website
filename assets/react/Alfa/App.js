import React, { useState, useEffect } from "react";
import PayBill from "./PayBill";
import ReCharge from "./ReCharge";
import MyBill from "./MyBill";
import Default from "./Default";
import Header from "./Header";
import SuccessModal from "./Modal/SuccessModal";
import ErrorModal from "./Modal/ErrorModal";

const App = ({ parameters }) => {
    // console.log(parameters)
    const [activeButton, setActiveButton] = useState({ name: "" });
    const [getBackLink, setBackLink] = useState({ name: "" });
    const [getHeaderTitle, setHeaderTitle] = useState("Alfa");

    //Modal Variable
    const [getModalName, setModalName] = useState("");
    const [modalShow, setModalShow] = useState(false);
    const [getSuccessModal, setSuccessModal] = useState({ imgPath: "/build/images/Alfa//build/images/Alfa/SuccessImg.png", title: "", desc: "" });
    const [getErrorModal, setErrorModal] = useState({ imgPath: "/build/images/Alfa//build/images/Alfa/ErrorImg.png", title: "Error Modal", desc: "ErrorModal ErrorModal ErrorModal ErrorModal ErrorModal" });

    return (
        <div id="AlfaBody">

            <div className="scrolableView">

                <Header activeButton={activeButton} setActiveButton={setActiveButton} getHeaderTitle={getHeaderTitle} getBackLink={getBackLink} />
                {getModalName === "" &&
                    <>
                        {activeButton.name === "" && <Default activeButton={activeButton} setActiveButton={setActiveButton} setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />}

                        {activeButton.name === "PayBill" && <PayBill activeButton={activeButton} setActiveButton={setActiveButton} setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />}
                        {activeButton.name === "ReCharge" && <ReCharge activeButton={activeButton} setActiveButton={setActiveButton} setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />}

                        {activeButton.name === "MyBill" && <MyBill setModalShow={setModalShow} setErrorModal={setErrorModal} setSuccessModal={setSuccessModal} setModalName={setModalName} activeButton={activeButton} setActiveButton={setActiveButton} setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />}
                    </>
                }
            </div>


            {/* Modal */}
            {getModalName === "SuccessModal" && <SuccessModal getSuccessModal={getSuccessModal} show={modalShow} onHide={() => { setModalShow(false); setModalName("") }} />}
            {getModalName === "ErrorModal" && <ErrorModal getErrorModal={getErrorModal} show={modalShow} onHide={() => { setModalShow(false); setModalName("") }} />}
        </div>
    );
};

export default App;