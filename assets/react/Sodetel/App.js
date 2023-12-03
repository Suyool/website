import React, {useEffect, useState} from 'react';
import Default from "./components/Default";
import Header from "./components/Header";
import BundleCredentials from "./components/BundleCredentials";
import ReCharge from "./components/ReCharge";
import MyBundle from "./components/MyBundle";
import ErrorModal from "./components/Modal/ErrorModal";
import SuccessModal from "./components/Modal/SuccessModal";

function App({parameters}) {
    const [activeButton, setActiveButton] = useState({name: "Default"});
    const [getBackLink, setBackLink] = useState({name: "Default"});
    const [getHeaderTitle, setHeaderTitle] = useState("Sodetel");
    const [getDataGetting, setDataGetting] = useState({id: ""});
    const [planData, setPlanData] = useState({});

    const [bundleData, setBundleData] = useState({id: ""});
    const [credential, setCredential] = useState({
        name: "",
        type: "",
    });
    const [credentialsArray, setCredentialsArray] = useState([]);

    const [modalDesc, setModalDesc] = useState({
        name: "",
        imgPath: "/build/images/alfa/SuccessImg.png",
        title: "Success Modal",
        desc: "Success Modal",
    });

    useEffect(() => {
        setDataGetting("");
        window.handleCheckout = (message) => {
            setDataGetting(message);
        };
    });

    return (
        <div>
            <Header
                parameters={parameters}
                activeButton={activeButton}
                setActiveButton={setActiveButton}
                getHeaderTitle={getHeaderTitle}
                getBackLink={getBackLink}
            />
            {activeButton.name === "Default" &&
                <Default
                    setActiveButton={setActiveButton}
                    setBackLink={setBackLink}
                    setHeaderTitle={setHeaderTitle}
                    setCredential={setCredential}
                    setCredentialsArray={setCredentialsArray}
                />
            }

            {activeButton.name === "BundleCredentials" && (
                <BundleCredentials
                    credential={credential} setCredential={setCredential}
                    activeButton={activeButton} setActiveButton={setActiveButton}
                    setBundleData={setBundleData}
                    setModalDesc={setModalDesc}
                    bundle={activeButton.bundle}
                    setBackLink={setBackLink}
                    setHeaderTitle={setHeaderTitle}
                    credentialsArray={credentialsArray}
                />
            )}

            {activeButton.name === "Services" && (
                <ReCharge
                parameters={parameters}
                setPrepaidVoucher={setPlanData}
                getVoucherData={bundleData}
                activeButton={activeButton}
                setActiveButton={setActiveButton}
                setHeaderTitle={setHeaderTitle}
                setBackLink={setBackLink}
                />
            )}

            {activeButton.name === "MyBundle" && (
                <MyBundle
                    setDataGetting={setDataGetting}
                    parameters={parameters}
                    credential={credential}
                    getDataGetting={getDataGetting}
                    getPrepaidVoucher={planData}
                    activeButton={activeButton}
                    setActiveButton={setActiveButton}
                    setHeaderTitle={setHeaderTitle}
                    setBackLink={setBackLink}
                    setModalDesc={setModalDesc}
                />
            )}

            {/* Modal */}
            {modalDesc.name === "SuccessModal" && (
                <SuccessModal
                    getSuccessModal={modalDesc}
                    show={modalDesc.show}
                    onHide={() => {
                        setModalDesc({
                            ...modalDesc,
                            name: ""
                        });
                        setActiveButton({...activeButton, name: "Default" });
                    }}
                />
            )}
            {modalDesc.name === "ErrorModal" && (
                <ErrorModal
                    parameters={parameters}
                    getErrorModal={modalDesc}
                    show={modalDesc.show}
                    onHide={() => {
                        setModalDesc({
                            ...modalDesc,
                            name: "",
                            show: false
                        });
                        setActiveButton({...activeButton, name: "Default" });
                    }}
                />
            )}

        </div>
    );
}

export default App;