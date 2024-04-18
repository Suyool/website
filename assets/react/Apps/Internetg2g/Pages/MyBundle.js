import React, {useEffect, useState} from "react";
import {useDispatch, useSelector} from "react-redux";
import {settingData} from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";

const MyBundle = () => {
    const dispatch = useDispatch();
    const {pay} = AppAPI();
    const parameters = useSelector((state) => state.appData.parameters);
    const mobileResponse = useSelector((state) => state.appData.mobileResponse);
    const getPrepaidVoucher = useSelector((state) => state.appData.productInfo);
    const providerName = useSelector((state) => state.appData.providerName);

    const [isButtonDisabled, setIsButtonDisabled] = useState(false);

    const handleConfirmPay = () => {
        dispatch(settingData({field: "isloading", value: true}));
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
            pay(getPrepaidVoucher)
        } else if (mobileResponse == "failed") {
            dispatch(settingData({field: "isloading", value: false}));
            setIsButtonDisabled(false);
            dispatch(settingData({field: "mobileResponse", value: ""}));
        }
    }, [mobileResponse]);

    return (
        <>
            <div id="MyBundle">
                <>
                    <div className="MyBundleBody">
                        <div className="mainTitle">{getPrepaidVoucher.title}</div>
                        <img
                            className="BundleBigImg"
                            src={getPrepaidVoucher.image}
                            alt="Bundle"
                            onError={(e) => {
                                e.target.src = '../build/images/g2g/freefire.png';
                            }}
                        />
                        <div className="smlDesc">
                            <img
                                className="question"
                                src={`/build/images/alfa/attention.svg`}
                                alt="question"
                                style={{verticalAlign: "baseline"}}
                            />
                            &nbsp;
                            {providerName} Only accept payments in {getPrepaidVoucher.currency}.
                        </div>

                        <div className="br"></div>

                        <div className="MoreInfo">
                            <div className="label">Total amount to pay</div>
                            {getPrepaidVoucher.currency === "LBP" ? (
                                <>L.L {getPrepaidVoucher.price}</>
                            ) : (
                                <>${getPrepaidVoucher.price}</>
                            )}
                        </div>
                    </div>
                    <div className="payNowBtnCont">
                        <button
                            id="ContinueBtn"
                            className="btnCont"
                            onClick={handleConfirmPay}
                            disabled={isButtonDisabled}
                        >
                            Pay Now
                        </button>
                    </div>
                </>
            </div>
        </>
    );
};

export default MyBundle;
