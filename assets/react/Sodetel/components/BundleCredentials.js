import React, {useState} from 'react';
import {Spinner} from "react-bootstrap";
import {capitalizeFirstLetters, formatMobileNumber} from "../../functions";
import axios from "axios";

function BundleCredentials({
                               credential,
                               setCredential,
                               bundle,
                               activeButton,
                               setActiveButton,
                               setBundleData,
                               setModalDesc,
                               setBackLink,
                               setHeaderTitle,
                               credentialsArray
                           }) {

    const [currency, setCurrency] = useState("LBP");
    const [isButtonDisabled, setIsButtonDisabled] = useState(false);
    const [getSpinnerLoader, setSpinnerLoader] = useState(false);
    const [showCredentials, setShowCredentials] = useState(true);

    setBackLink("Default");
    setHeaderTitle(`Re-charge ${capitalizeFirstLetters(activeButton?.bundle)} Package`);

    const handleInputChange = (event) => {
        setIsButtonDisabled(false);
        const value = event.target.value;
        if (credential.type === "phone") {
            setCredential({...credential, [credential.name]: formatMobileNumber(value)});
        } else {
            setCredential({...credential, [credential.name]: value});
        }
    };

    const [getBtnDesign, setBtnDesign] = useState(false);

    const handleInputFocus = () => {
        setBtnDesign(true);
    };

    const handleContinue = () => {
        setIsButtonDisabled(true);
        localStorage.setItem(credential.name, credential[credential.name]);
        localStorage.setItem("billCurrency", currency);
        setSpinnerLoader(true);

        axios
            .post("/sodetel/bundles", {
                service: bundle,
                identifier: credential[credential.name],
            })
            .then((response) => {
                console.log(response);
                if (response?.data?.status) {
                    setActiveButton({...activeButton, name: "Services"});
                    setBundleData({id: response?.data?.data});
                } else if (
                    response?.data?.message === "Maximum allowed number of PIN requests is reached"
                ) {
                    setSpinnerLoader(false);
                    setModalDesc({
                        show: true,
                        name: "ErrorModal",
                        imgPath: "/build/images/alfa/error.png",
                        title: " PIN Tries Exceeded",
                        description: (
                            <div>
                                You have exceeded the allowed PIN requests.<br/> Kindly try again
                                later
                            </div>
                        ),
                    });
                } else if (
                    response?.data?.message === "Not Enough Balance Amount to be paid"
                ) {
                    setSpinnerLoader(false);
                    setModalDesc({
                        show: true,
                        name: "ErrorModal",
                        imgPath: "/build/images/alfa/error.png",
                        title: "Not Enough Balance Amount to be paid",
                        description: (
                            <div>
                                You have exceeded the allowed PIN requests.<br/> Kindly try again
                                later
                            </div>
                        ),
                    });
                } else {
                    setSpinnerLoader(false);
                    setModalDesc({
                        show: true,
                        name: "ErrorModal",
                        imgPath: "/build/images/alfa/error.png",
                        title: credential.label+" Not Found ",
                        description: (
                            <div>
                                The {credential.label} you entered was not found in the system.
                                <br/>
                                Kindly try another number.
                            </div>
                        ),
                    });
                }
            })
            .catch((error) => {
                console.log(error);
            });
        setBtnDesign(false);
    };

    return (
        <div id="PayBill" className={getSpinnerLoader ? "hideBack" : ""}>
            {getSpinnerLoader && (
                <div id="spinnerLoader">
                    <Spinner className="spinner" animation="border" variant="secondary"/>
                </div>
            )}
            {
                (credentialsArray.length > 1 && showCredentials) ? (
                    <div id="Default_Sodetel" className="py-0">
                        <div className="MainTitle">Re-charge your {capitalizeFirstLetters(activeButton.bundle)} Package using your:</div>

                        {credentialsArray.map((credential, index) => (
                            <div
                                className="Cards"
                                onClick={() => {
                                    setCredential(credential);
                                    setShowCredentials(false);
                                }}
                            >
                                <img
                                    className="logoImg"
                                    src="/build/images/sodetel/sodetel-bundle.png"
                                    alt="suyool-sedetelLogo"
                                />
                                <div className="Text">
                                    <div className="SubTitle">{credential.label}</div>
                                    <div className="description">Settle your Sodetel bill quickly and securely using your {credential.label}</div>
                                </div>
                            </div>
                        ))}
                    </div>
                ): (
                    <>
                        <div className="mainTitle">Enter your {credential.label} to recharge</div>
                        {
                            credential.type === "phone" ? (
                                <div className="MobileNbContainer mt-3">
                                    <div className="place">
                                        <img src="/build/images/alfa/flag.png" alt="flag"/>
                                        <div className="code">+961</div>
                                    </div>
                                    <input
                                        type="tel"
                                        className={getSpinnerLoader ? "nbInputHide" : "nbInput"}
                                        placeholder={credential.label}
                                        value={credential.mobileNumber}
                                        onChange={handleInputChange}
                                        onFocus={handleInputFocus}
                                    />
                                </div>
                            ) : (
                                <div className="MobileNbContainer mt-3">
                                    <input
                                        type="text"
                                        className={getSpinnerLoader ? "nbInputHide w-100" : "nbInput w-100"}
                                        placeholder={credential.label}
                                        value={credential.mobileNumber}
                                        onChange={handleInputChange}
                                        onFocus={handleInputFocus}
                                    />
                                </div>
                            )
                        }

                        { (!showCredentials && credentialsArray.length > 1) &&  credentialsArray.map((credentialObj, index) => {
                            if(credentialObj.name !== credential.name){
                                return (
                                    <button className="credential-link my-1" onClick={()=>{
                                        setCredential(credentialObj);
                                        setShowCredentials(false);
                                    }}>
                                        Login with {credentialObj.label}
                                    </button>
                                )
                            }
                        })}

                        <button
                            id="ContinueBtn"
                            className={`${!getBtnDesign ? "btnCont" : "btnContFocus"}`}
                            onClick={handleContinue}
                            // disabled={
                            //     credential[credential.name]?.replace(/\s/g, "").length !== 8 || isButtonDisabled
                            // }
                        >
                            Continue
                        </button>
                    </>
                )}
        </div>
    );
}

export default BundleCredentials;