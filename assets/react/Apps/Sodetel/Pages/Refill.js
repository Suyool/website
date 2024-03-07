import React, {useEffect, useState} from "react";
import {Spinner} from "react-bootstrap";
import AppAPI from "../Api/AppAPI";
import {useDispatch, useSelector} from "react-redux";
import {settingData} from "../../Alfa/Redux/Slices/AppSlice";

const Refill = () => {
    const [isButtonDisabled, setIsButtonDisabled] = useState(false);

    const {planData, credential, mobileResponse, parameters} = useSelector((state) => state.appData);

    const {Recharge} = AppAPI();
    const dispatch = useDispatch();

    useEffect(() => {
        setIsButtonDisabled(false);
    }, []);

    const handleConfirmPay = () => {
        dispatch(settingData({field: "isloading", value: true}));
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
        if (mobileResponse === "success") {
            Recharge();
        } else if (mobileResponse === "failed") {
            dispatch(settingData({field: "isloading", value: false}));
            setIsButtonDisabled(false);
            dispatch(settingData({ field: "mobileResponse", value: "" }));
        }
    }, [mobileResponse]);

    return (
        <>
            <div
                id="MyBundle"
            >
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
