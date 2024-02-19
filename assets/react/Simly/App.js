import React, {useEffect, useState} from "react";
import Default from "./Default";
import Header from "./Header";
import ErrorModal from "./Modal/ErrorModal";
import SuccessModal from "./Modal/SuccessModal";
//import MyBundle from "./MyBundle";
import Packages from "./packages";
import PackagesInfo from "./PackagesInfo";
import Account from "./Account";

const App = ({parameters}) => {
    const [selectedPlan, setSelectedPlan] = useState(null); // State to store the selected plan
    const [activeButton, setActiveButton] = useState({name: ""});
    const [getBackLink, setBackLink] = useState({name: ""});
    const [getHeaderTitle, setHeaderTitle] = useState("Suyool eSim");
    const [getModalName, setModalName] = useState("");
    const [modalShow, setModalShow] = useState(false);
    const [categories, setCategories] = useState([]);
    const [getDataGetting, setDataGetting] = useState("");
    const [getPrepaidVoucher, setPrepaidVoucher] = useState(true);

    const [getSuccessModal, setSuccessModal] = useState({
        imgPath: "/build/images/alfa/SuccessImg.png",
        title: "Success Modal",
        desc: "Success Modal",
    });
    const [getErrorModal, setErrorModal] = useState({
        img: "/build/images/alfa/error.png",
        title: "Error Modal",
        btn: "Top Up",
        desc: "Error Modal",
    });


    useEffect(() => {
        setDataGetting("");
        window.handleCheckout = (message) => {
            setDataGetting(message);
        };

    }, []);


    return (
        <div id="SimlyBody">
            <Header
                parameters={parameters}
                activeButton={activeButton}
                setActiveButton={setActiveButton}
                getHeaderTitle={getHeaderTitle}
                getBackLink={getBackLink}
            />
            <div className="scrolableView">
                {getModalName === "" && (
                    <>
                        {activeButton.name === "" && (
                            <Default
                                setActiveButton={setActiveButton}
                                setHeaderTitle={setHeaderTitle}
                                setBackLink={setBackLink}
                                categories={categories}
                                setPrepaidVoucher={setPrepaidVoucher}
                                setTypeID ={parameters.typeID}
                                setDataGetting={setDataGetting}
                            />
                        )}
                        {activeButton.name === "Packages" && (
                            <Packages
                                setDataGetting={setDataGetting}
                                parameters={parameters}
                                getDataGetting={getDataGetting}
                                getPrepaidVoucher={getPrepaidVoucher}
                                setModalShow={setModalShow}
                                setModalName={setModalName}
                                setSuccessModal={setSuccessModal}
                                setErrorModal={setErrorModal}
                                setActiveButton={setActiveButton}
                                setHeaderTitle={setHeaderTitle}
                                setBackLink={setBackLink}
                                setSelectedPlan={setSelectedPlan}
                            />
                        )}
                        {activeButton.name === "PackagesInfo" && (
                            <PackagesInfo
                                setDataGetting={setDataGetting}
                                parameters={parameters}
                                getDataGetting={getDataGetting}
                                getPrepaidVoucher={getPrepaidVoucher}
                                setModalShow={setModalShow}
                                setModalName={setModalName}
                                setSuccessModal={setSuccessModal}
                                setErrorModal={setErrorModal}
                                setActiveButton={setActiveButton}
                                setHeaderTitle={setHeaderTitle}
                                setBackLink={setBackLink}
                                selectedPlan={selectedPlan}

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
                        setActiveButton({name: ""});
                    }}
                />
            )}
            {getModalName === "ErrorModal" && (
                <ErrorModal
                    parameters={parameters}
                    getErrorModal={getErrorModal}
                    show={modalShow}
                    onHide={() => {
                        setModalShow(false);
                        setModalName("");
                        setActiveButton({name: ""});
                    }}
                />
            )}
        </div>
    );
};

export default App;
