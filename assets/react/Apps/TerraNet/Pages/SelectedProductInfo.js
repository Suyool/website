import React, { useState, useEffect } from "react";
import axios from "axios";
import { Spinner } from "react-bootstrap";
import {settingData, settingObjectData} from "../../TerraNet/Redux/Slices/AppSlice";
import {useDispatch, useSelector} from "react-redux";
import AppAPI from "../../TerraNet/Api/AppAPI";

const SelectedProductInfo = () => {
    const dispatch = useDispatch();

    const { payBills } = AppAPI();
    const parameters = useSelector((state) => state.appData.parameters);
    const mobileResponse = useSelector((state) => state.appData.mobileResponse);
    const getPrepaidVoucher = useSelector((state) => state.appData.productInfo);

    const [getSpinnerLoader, setSpinnerLoader] = useState(false);

    const [isButtonDisabled, setIsButtonDisabled] = useState(false);

    useEffect(() => {
        dispatch(settingData({ field: "headerData", value: { title: "Buy DSL Package", backLink: "ReCharge", currentPage: "SelectedProductInfo" } }));
        setIsButtonDisabled(false);
    }, []);


    const [isOverlayVisible, setIsOverlayVisible] = useState(false);
    const [paymentSuccess, setPaymentSuccess] = useState(false);

    const handleConfirmPay = () => {
        dispatch(settingData({ field: "isloading", value: true }));
        setIsButtonDisabled(true);
        if (parameters?.deviceType === "Android") {
            setTimeout(() => {
                window.AndroidInterface.callbackHandler("message");
            }, 2000);
        } else if (parameters?.deviceType === "Iphone") {
            setTimeout(() => {
                window.webkit.messageHandlers.callbackHandler.postMessage("fingerprint");
            }, 2000);
        }
    };

    useEffect(() => {
        if (mobileResponse == "success") {
            payBills(getPrepaidVoucher.productId,localStorage.getItem("Type"),getPrepaidVoucher?.price);
        } else if (mobileResponse == "failed") {
            dispatch(settingData({ field: "isloading", value: false }));
            setIsButtonDisabled(false);
            dispatch(settingData({ field: "mobileResponse", value: "" }));
        }
    }, [mobileResponse]);
    const imagePath = `/build/images/terraNet/product_${getPrepaidVoucher.productId}.svg`;

    return (
        <>
            {paymentSuccess && (
                <>
                    <div id="PaymentConfirmationPrePaid" className="overlay">
                        <div className="topSection">
                            <div className="brBoucket"></div>
                            <div className="titles">
                                <div className="titleGrid">Payment Confirmation</div>
                                <button
                                    onClick={() => {
                                        setPaymentSuccess(false);
                                        setIsOverlayVisible(false);
                                    }}
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>

                        <div className="bodySection">
                            <div className="row align-items-center w-100">
                                <div className="col-auto p-0">
                                    <img
                                        className="logoImg pt-3"
                                        src="/build/images/terraNet/terraNetLogo.png"
                                        alt="alfaLogo"
                                    />
                                </div>
                                <div className="col ps-0">
                                    <div className="bigTitle">Terranet Bill Payment</div>
                                    <div className="descriptio text-start pt-2">
                                        {getPrepaidVoucher.description}
                                    </div>
                                </div>
                            </div>

                            <div className="paymentConfirmation">
                                <div className="MyBundleBody">
                                    <div className="MoreInfo">
                                        <div className="label">Account</div>
                                        <div className="value">
                                            {localStorage.getItem("UserAccount")}
                                        </div>
                                    </div>
                                    <div className="br"></div>
                                    <div className="MoreInfo">
                                        <div className="label">Total before taxes</div>
                                        <div className="value">
                                            L.L {parseInt(getPrepaidVoucher?.originalHT).toLocaleString()}
                                        </div>
                                    </div>
                                    <div className="MoreInfo">
                                        <div className="label">+V.A.T & Stamp Duty</div>
                                        <div className="value">
                                            L.L {parseInt(getPrepaidVoucher?.price - getPrepaidVoucher?.originalHT).toLocaleString()}
                                        </div>
                                    </div>
                                    <div className="br"></div>
                                    <div className="MoreInfo">
                                        <div className="label">Total after taxes</div>
                                        <div className="value">
                                            L.L {parseInt(getPrepaidVoucher?.price).toLocaleString()}
                                        </div>
                                    </div>
                                    <div className="br"></div>
                                    <div className="MoreInfo">
                                        <div className="label">Total amount to pay</div>
                                        <div className="value1">
                                            L.L {parseInt(getPrepaidVoucher?.price).toLocaleString()}
                                        </div>
                                    </div>
                                </div>
                                <button
                                    id="ContinueBtn"
                                    className="btnCont mt-4"
                                    onClick={handleConfirmPay}
                                    disabled={isButtonDisabled}
                                >
                                    Confirm & Pay
                                </button>
                            </div>
                        </div>
                    </div>
                    {getSpinnerLoader && (
                        <div id="spinnerLoader" className="overlay">
                            <Spinner
                                className="spinner"
                                animation="border"
                                variant="secondary"
                            />
                        </div>
                    )}
                </>
            )}
            <div
                id="MyBundle"
                className={`${paymentSuccess ? "hideBackk overlay" : ""}`}
            >
                {getSpinnerLoader && (
                    <div id="spinnerLoader" className="overlay">
                        <Spinner className="spinner" animation="border" variant="secondary" />
                    </div>
                )}
                {isOverlayVisible && (
                    <div
                        className="modal-backdrop show"
                        onClick={() => setIsOverlayVisible(false)}
                    ></div>
                )}
                <div className="MyBundleBody">
                    <div className="mainTitle">
                        The package related to your landline is:
                    </div>
                    <div className="mainDesc text-center fw-bold my-3 fs-6">{getPrepaidVoucher?.description}</div>

                    <img
                        className="BundleBigImg"
                        src={imagePath} alt={getPrepaidVoucher.description}
                        alt="Bundle"
                    />
                    <div className="smlDesc">
                        <img
                            className="question"
                            src={`/build/images/alfa/attention.svg`}
                            alt="question"
                            style={{ verticalAlign: "baseline" }}
                        />
                        &nbsp;Terranet only accepts payments in LBP
                    </div>

                    <div className="MoreInfo">
                        <div className="label">Total before taxes</div>
                        <div className="value">
                            L.L {parseInt(getPrepaidVoucher?.originalHT).toLocaleString()}
                        </div>
                    </div>
                    <div className="MoreInfo">
                        <div className="label">+V.A.T & Stamp Duty</div>
                        <div className="value">
                            L.L {parseInt(getPrepaidVoucher?.price - getPrepaidVoucher?.originalHT).toLocaleString()}
                        </div>
                    </div>
                    <div className="br"></div>
                    <div className="MoreInfo">
                        <div className="label">Total after taxes</div>
                        <div className="value">
                            L.L {parseInt(getPrepaidVoucher?.price).toLocaleString()}
                        </div>
                    </div>
                    <div className="br"></div>
                    <div className="MoreInfo">
                        <div className="label">Total amount to pay</div>
                        <div className="value1">
                            L.L {parseInt(getPrepaidVoucher?.price).toLocaleString()}
                        </div>
                    </div>
                </div>
                <button
                    id="ContinueBtn"
                    className="btnCont mt-5"
                    onClick={() => {
                        setPaymentSuccess(true);
                    }}
                >
                    Re-charge package
                </button>
            </div>
        </>
    );
};

export default SelectedProductInfo;
