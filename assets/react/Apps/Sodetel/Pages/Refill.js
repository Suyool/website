import React, {useEffect, useState} from "react";
import {Spinner} from "react-bootstrap";
import AppAPI from "../Api/AppAPI";
import {useSelector} from "react-redux";

const Refill = ({
                    setDataGetting,
                    getDataGetting,
                    parameters,
                }) => {
    const [isButtonDisabled, setIsButtonDisabled] = useState(false);
    const [getSpinnerLoader, setSpinnerLoader] = useState(false);

    const {planData, credential} = useSelector((state) => state.appData);

    const {Recharge} = AppAPI();

    useEffect(() => {
        setIsButtonDisabled(false);
    }, []);

    const handleShare = (shareCode) => {
        let object = [
            {
                Share: {
                    share: "share",
                    text: shareCode,
                },
            },
        ];
        if (parameters?.deviceType === "Android") {
            window.AndroidInterface.callbackHandler(JSON.stringify(object));
        } else if (parameters?.deviceType === "Iphone") {
            window.webkit.messageHandlers.callbackHandler.postMessage(object);
        }
    };

    const handleConfirmPay = () => {
        setSpinnerLoader(true);
        setIsButtonDisabled(true);
        if (parameters?.deviceType === "Android") {
            setTimeout(() => {
                window.AndroidInterface.callbackHandler("message");
            }, 2000);
        } else if (parameters?.deviceType === "Iphone") {
            setTimeout(() => {
                window.webkit.messageHandlers.callbackHandler.postMessage(
                    "fingerprint"
                );
            }, 2000);
        }
    };

    useEffect(() => {
        if (getDataGetting === "success") {
            Recharge();
        } else if (getDataGetting === "failed") {
            setSpinnerLoader(false);
            setIsButtonDisabled(false);
            setDataGetting("");
        }
    });

    const copyToClipboard = (value) => {
        const tempInput = document.createElement("input");
        tempInput.value = value;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
    };

    return (
        <>
            <div
                id="MyBundle"
            >
                {getSpinnerLoader && (
                    <div id="spinnerLoader">
                        <Spinner
                            className="spinner"
                            animation="border"
                            variant="secondary"
                        />
                    </div>
                )}
                <>
                    <div className="MyBundleBody">
                        <div className="mainTitle">The package related to your {credential.label} is:</div>
                        <div className="bundleTitle">{planData?.plandescription}</div>

                        {/* <div className="mainDesc">*All taxes excluded</div> */}
                        <img
                            className="BundleBigImg"
                            src={`/build/images/sodetel/${planData.plancode}.svg`}
                            alt="Bundle"
                        />
                        <div className="smlDesc">
                            <img
                                className="question"
                                src={`/build/images/alfa/attention.svg`}
                                alt="question"
                                style={{verticalAlign: "baseline"}}
                            />
                            &nbsp; Sodetel only accepts payments in LBP
                        </div>

                        <div className="MoreInfo">
                            <div className="label">Total before taxes</div>
                            <div className="value">L.L {parseInt(planData.priceht).toLocaleString()}</div>
                        </div>
                        <div className="MoreInfo">
                            <div className="label">+V.A.T & Stamp Duty</div>
                            <div
                                className="value">L.L {parseInt(planData?.price - planData?.priceht).toLocaleString()}</div>
                        </div>
                        <div className="br"></div>
                        <div className="MoreInfo">
                            <div className="label">Total after taxes</div>
                            <div className="value">L.L {parseInt(planData.price).toLocaleString()}</div>
                        </div>

                        <div className="br"></div>
                        <div className="MoreInfo">
                            <div className="label">Total amount to pay</div>
                            <div className="value1">
                                L.L {parseInt(planData.price).toLocaleString()}
                            </div>
                        </div>
                        <div className="smlDescSayrafa">
                            $1 = {parseInt(planData.sayrafa).toLocaleString()} L.L (Subject to change).
                        </div>
                    </div>

                    <button
                        id="ContinueBtn"
                        className="btnCont"
                        onClick={handleConfirmPay}
                        disabled={isButtonDisabled}
                    >
                        Re-charge Package
                    </button>
                </>
            </div>
        </>
    );
};

export default Refill;
