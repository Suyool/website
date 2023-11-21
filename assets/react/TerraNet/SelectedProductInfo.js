import React, { useState, useEffect } from "react";
import axios from "axios";
import { Spinner } from "react-bootstrap";

const SelectedProductInfo = ({
                                 selectedProduct,
                                 setBackLink,
                                 parameters,
                                 setModalShow,
                                 setErrorModal,
                                 setSuccessModal,
                                 setModalName,
                                 setHeaderTitle
                             }) => {

    useEffect(() => {
        setHeaderTitle("Buy DSL Package");
        setBackLink("inputValue");
    }, []);
    const [getPrepaidVoucher, setPrepaidVoucher] = useState({
        Price: "",
        Currency: "",
        Description: "",
        ProductId: "",
        Cost: "",
        OriginalHT: "",

    });

    const [isOverlayVisible, setIsOverlayVisible] = useState(false);
    const [paymentSuccess, setPaymentSuccess] = useState(false);
    const [isButtonDisabled, setIsButtonDisabled] = useState(false);
    const [getSpinnerLoader, setSpinnerLoader] = useState(false);

    const handleConfirmPay = () => {
        setIsOverlayVisible(true);
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

        window.handleCheckout = (message) => {
            if (message === "success") {
                console.log(getPrepaidVoucher);
                var type = localStorage.getItem("Type")

                axios
                    .post("/terraNet/refill_customer_terranet", {
                        productId: getPrepaidVoucher.ProductId,
                        productPrice: getPrepaidVoucher.Price,
                        productCurrency: getPrepaidVoucher.Currency,
                        productDescription: getPrepaidVoucher.Description,
                        productCost: selectedProduct?.Cost,
                        productOriginalHT: selectedProduct?.OriginalHT,
                        accountType: type,

                    })
                    .then((response) => {
                        console.log(response);
                        const jsonResponse = response?.data?.message;

                        if (response.data?.IsSuccess) {
                            setModalName("SuccessModal");
                            setSuccessModal({
                                imgPath: "/build/images/alfa/SuccessImg.png",
                                title: "TerraNet Bill Paid Successfully",
                                desc: `You have successfully paid your TerraNet bill of L.L ${parseInt(
                                    getPrepaidVoucher?.Price
                                ).toLocaleString()}.`,
                                deviceType: parameters?.deviceType,
                            });
                            setModalShow(true);
                        } else {
                            console.log(response.data.flagCode);
                            if (
                                response.data.IsSuccess === false &&
                                response.data.flagCode === 10
                            ) {
                                setModalName("ErrorModal");
                                setErrorModal({
                                    img: "/build/images/alfa/error.png",
                                    title: jsonResponse.Title,
                                    desc: jsonResponse.SubTitle,
                                    path: jsonResponse.ButtonOne.Flag,
                                    btn: jsonResponse.ButtonOne.Text,
                                });
                                setModalShow(true);
                            } else if (
                                !response.data.IsSuccess &&
                                response.data.flagCode === 11
                            ) {
                                setModalName("ErrorModal");
                                setErrorModal({
                                    img: "/build/images/alfa/error.png",
                                    title: jsonResponse.Title,
                                    desc: jsonResponse.SubTitle,
                                    path: jsonResponse.ButtonOne.Flag,
                                    btn: jsonResponse.ButtonOne.Text,
                                });
                                setModalShow(true);
                            } else {
                                setModalName("ErrorModal");
                                setErrorModal({
                                    img: "/build/images/alfa/error.png",
                                    title: "Please Try again",
                                    desc: `You cannot purchase now`,
                                    // path: response.data.path,
                                    btn: "OK",
                                });
                                setModalShow(true);
                            }
                        }
                    })
                    .catch((error) => {
                        console.error("Payment error:", error);
                    });
            }else if (message == "failed") {
                setSpinnerLoader(false);
                setIsButtonDisabled(false);
                setPaymentSuccess(false);
                setIsOverlayVisible(false);

            }
        };
    };

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
                                        {selectedProduct.Description}
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
                                            L.L {parseInt(selectedProduct?.OriginalHT).toLocaleString()}
                                        </div>
                                    </div>
                                    <div className="MoreInfo">
                                        <div className="label">+V.A.T & Stamp Duty</div>
                                        <div className="value">
                                            L.L {parseInt(selectedProduct?.Price - selectedProduct?.OriginalHT).toLocaleString()}
                                        </div>
                                    </div>
                                    <div className="br"></div>
                                    <div className="MoreInfo">
                                        <div className="label">Total after taxes</div>
                                        <div className="value">
                                            L.L {parseInt(selectedProduct?.Price).toLocaleString()}
                                        </div>
                                    </div>
                                    <div className="br"></div>
                                    <div className="MoreInfo">
                                        <div className="label">Total amount to pay</div>
                                        <div className="value1">
                                            L.L {parseInt(selectedProduct?.Price).toLocaleString()}
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
                    <div className="mainDesc text-center fw-bold my-3 fs-6">{selectedProduct?.Description}</div>

                    <img
                        className="BundleBigImg"
                        src={`/build/images/alfa/Bundle1.png`}
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
                            L.L {parseInt(selectedProduct?.OriginalHT).toLocaleString()}
                        </div>
                    </div>
                    <div className="MoreInfo">
                        <div className="label">+V.A.T & Stamp Duty</div>
                        <div className="value">
                            L.L {parseInt(selectedProduct?.Price - selectedProduct?.OriginalHT).toLocaleString()}
                        </div>
                    </div>
                    <div className="br"></div>
                    <div className="MoreInfo">
                        <div className="label">Total after taxes</div>
                        <div className="value">
                            L.L {parseInt(selectedProduct?.Price).toLocaleString()}
                        </div>
                    </div>
                    <div className="br"></div>
                    <div className="MoreInfo">
                        <div className="label">Total amount to pay</div>
                        <div className="value1">
                            L.L {parseInt(selectedProduct?.Price).toLocaleString()}
                        </div>
                    </div>
                </div>
                <button
                    id="ContinueBtn"
                    className="btnCont mt-5"
                    onClick={() => {
                        setPaymentSuccess(true);
                        setPrepaidVoucher({
                            Price: selectedProduct.Price,
                            Currency: selectedProduct.Currency,
                            Description: selectedProduct.Description,
                            ProductId: selectedProduct.ProductId,
                        });
                    }}
                >
                    Re-charge package
                </button>
            </div>
        </>
    );
};

export default SelectedProductInfo;
