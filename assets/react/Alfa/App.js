import React, { useState, useEffect } from "react";
import PayBill from "./PayBill";
import ReCharge from "./ReCharge";
import MyBill from "./MyBill";
import Default from "./Default";
import Header from "./Header";
import SuccessModal from "./Modal/SuccessModal";
import ErrorModal from "./Modal/ErrorModal";
import MyBundle from "./MyBundle";

const App = ({ parameters }) => {
    // console.log(parameters)
    const [activeButton, setActiveButton] = useState({ name: "" });
    const [getBackLink, setBackLink] = useState({ name: "" });
    const [getHeaderTitle, setHeaderTitle] = useState("Alfa");
    const [getPrepaidVoucher, setPrepaidVoucher] = useState({ vouchercategory: "", vouchertype: "", priceLBP: "", priceUSD: "", desc: "", isavailable: "" });
    const [getPostpaidData, setPostpaidData] = useState({ id: "" });
    const [getDataGetting,setDataGetting] = useState("");


    //Modal Variable
    const [getModalName, setModalName] = useState("");
    const [modalShow, setModalShow] = useState(false);
    const [getSuccessModal, setSuccessModal] = useState({ imgPath: "/build/images/alfa/SuccessImg.png", title: "SuccessModal", desc: "SuccessModalSuccessModalSuccessModal" });
    const [getErrorModal, setErrorModal] = useState({ img: "/build/images/alfa/error.png", title: "Error Modal",btn:"Top Up", desc: "ErrorModal ErrorModal ErrorModal ErrorModal ErrorModal" });

    const [getVoucherData, SetVoucherData] = useState([]);

    
    useEffect(() => {
        setDataGetting("");
        window.handleCheckout = (message) => {
          setDataGetting(message);
        };
      });

    return (
        <div id="AlfaBody">

            <div className="scrolableView">

                <Header parameters={parameters} activeButton={activeButton} setActiveButton={setActiveButton} getHeaderTitle={getHeaderTitle} getBackLink={getBackLink} />
                {getModalName === "" &&
                    <>
                        {activeButton.name === "" && <Default SetVoucherData={SetVoucherData} activeButton={activeButton} setActiveButton={setActiveButton} setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />}

                        {activeButton.name === "PayBill" && <PayBill setPostpaidData={setPostpaidData} setModalShow={setModalShow} setErrorModal={setErrorModal} setModalName={setModalName} activeButton={activeButton} setActiveButton={setActiveButton} setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />}
                        {activeButton.name === "ReCharge" && <ReCharge setPrepaidVoucher={setPrepaidVoucher} getVoucherData={getVoucherData} activeButton={activeButton} setActiveButton={setActiveButton} setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />}

                        {activeButton.name === "MyBill" && <MyBill setDataGetting={setDataGetting}  parameters={parameters} getDataGetting={getDataGetting}  getPostpaidData={getPostpaidData} setModalShow={setModalShow} setErrorModal={setErrorModal} setSuccessModal={setSuccessModal} setModalName={setModalName} activeButton={activeButton} setActiveButton={setActiveButton} setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />}
                        {activeButton.name === "MyBundle" && <MyBundle setDataGetting={setDataGetting} parameters={parameters} getDataGetting={getDataGetting}  getPrepaidVoucher={getPrepaidVoucher} setModalShow={setModalShow} setErrorModal={setErrorModal} setSuccessModal={setSuccessModal} setModalName={setModalName} activeButton={activeButton} setActiveButton={setActiveButton} setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />}
                    </>
                }
            </div>


            {/* Modal */}
            {getModalName === "SuccessModal" && <SuccessModal getSuccessModal={getSuccessModal} show={modalShow} onHide={() => { setModalShow(false); setModalName(""); setActiveButton({ name: "" }); }} />}
            {getModalName === "ErrorModal" && <ErrorModal parameters={parameters} getErrorModal={getErrorModal} show={modalShow} onHide={() => { setModalShow(false); setModalName(""); setActiveButton({ name: "" }); }} />}
        </div>
    );
};

export default App;