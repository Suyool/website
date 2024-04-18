import React from "react";
import {useDispatch, useSelector} from "react-redux";
import {handleShare} from "../Utils/functions";
import {settingData, settingObjectData} from "../Redux/Slices/AppSlice";

const BottomSlider = () => {
    const dispatch = useDispatch();
    const bottomSlider = useSelector((state) => state.appData.bottomSlider);
    const parameters = useSelector((state) => state.appData.parameters);
    const productInfo = useSelector((state) => state.appData.productInfo);

    const copyToClipboard = () => {
        const tempInput = document.createElement("input");
        tempInput.value = bottomSlider?.data?.data?.serialCode;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
    };
    return (
        <div id="BottomSliderContainer">
            <div className="topSection">
                <div className="brBoucket"></div>
                <div className="titles">
                    <div className="titleGrid"></div>
                    <button
                        onClick={() => {
                            dispatch(settingData({field: "bottomSlider", value: {isShow: false, name: "", data: {}}}));
                        }}
                    >
                        Cancel
                    </button>
                </div>
            </div>

            <div className="bodySection">
                {bottomSlider?.name == "PaymentDoneG2G" && (
                    <div id={bottomSlider?.name}>
                        <img
                            className="SuccessImg"
                            src="/build/images/alfa/SuccessImg.png"
                            alt="Bundle"
                        />
                        <div className="bigTitle">Payment Successful</div>
                        <div className="descriptio">
                            You have successfully purchased the {productInfo.title} for $
                            {productInfo.displayPrice}
                        </div>
                        <div className="br"></div>

                        <div className="copyTitle">To use your voucher:</div>
                        <div className="copyDesc">
                            Copy the code below
                        </div>

                        <button className="copySerialBtn" onClick={copyToClipboard}>
                            <div></div>
                            <div className="serial">{bottomSlider?.data?.data?.serialCode}</div>
                            <img
                                className="copySerial"
                                src="/build/images/alfa/copySerial.png"
                                alt="copySerial"
                            />
                        </button>

                        <button
                            id="ContinueBtn"
                            className="mt-4"
                            onClick={() => {
                                handleShare(bottomSlider?.data?.data?.serialCode, parameters.deviceType);
                            }}
                        >
                            Share Code
                        </button>
                    </div>
                )}
            </div>
        </div>
    );
};

export default BottomSlider;
