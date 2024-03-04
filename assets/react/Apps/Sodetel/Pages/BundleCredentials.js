import React, {useEffect, useState} from 'react';
import {Spinner} from "react-bootstrap";

import {useDispatch, useSelector} from "react-redux";
import {settingObjectData} from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";
import {settingData} from "../../Alfa/Redux/Slices/AppSlice";
import {capitalizeFirstLetters, formatMobileNumber} from "../../../functions";

function BundleCredentials() {
    const [currency, setCurrency] = useState("LBP");
    const [isButtonDisabled, setIsButtonDisabled] = useState(false);
    const [getSpinnerLoader, setSpinnerLoader] = useState(false);
    const [showCredentials, setShowCredentials] = useState(true);

    const {GetBundles} = AppAPI();
    const dispatch = useDispatch();

    const {credential, credentialsArray, bundle} = useSelector((state) => state.appData);

    useEffect(() => {
        dispatch(settingData({ field: "headerData", value: { title: `Re-charge ${capitalizeFirstLetters(bundle)} Package`, backLink: "Default", currentPage: "BundleCredentials" } }));
        setIsButtonDisabled(false);
    }, []);


    const handleInputChange = (event) => {
        setIsButtonDisabled(false);
        const value = event.target.value;
        if (credential.type === "phone") {
            dispatch(settingObjectData({ mainField: "credential", field: credential.name, value: formatMobileNumber(value) }));
        } else {
            dispatch(settingObjectData({ mainField: "credential", field: credential.name, value: value }));
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

        GetBundles(bundle, credential[credential.name]?.replace(/\s/g, ''), credential);
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
                        <div className="MainTitle">Re-charge your {capitalizeFirstLetters(bundle)} Package using your:</div>

                        {credentialsArray.map((credential, index) => (
                            <div
                                className="Cards"
                                onClick={() => {
                                    dispatch(settingData({ field: "credential", value: credential }));
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
                                    <button className="credential-link my-3" onClick={()=>{
                                        dispatch(settingData({ field: "credential", value: credentialObj }));
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