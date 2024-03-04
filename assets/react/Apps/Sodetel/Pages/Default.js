import React from "react";
import {useDispatch} from "react-redux";
import {settingData, settingObjectData} from "../Redux/Slices/AppSlice";

const Default = () => {

    const dslCredentials = [
        {
            name: "landlineNumber",
            label: "Landline Number",
            type: "phone",
        },
        {
            name: "ldNumber",
            label: "L/D Number",
            type: "text",
        }
    ];

    const fiberCredentials = [
        {
            name: "hfNumber",
            label: "H/F Number",
            type: "text",
        }
    ];

    const fourGCredentials = [
        {
            name: "simNumber",
            label: "Sim Card Number",
            type: "phone",
        },
        {
            name: "username",
            label: "Username",
            type: "text",
        }
    ];

    const dispatch = useDispatch();

    return (
        <div id="Default_Sodetel">
            <div className="MainTitle">What do you want to do?</div>

            <div
                className="Cards"
                onClick={() => {
                    dispatch(settingData({field: "credential", value: dslCredentials[0]}));
                    dispatch(settingData({field: "credentialsArray", value: dslCredentials}));
                    dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "BundleCredentials" }));
                    dispatch(settingData({field: "bundle", value: "dsl"}));
                }}
            >
                <img
                    className="logoImg"
                    src="/build/images/sodetel/sodetel-bundle.png"
                    alt="suyool-sedetelLogo"
                />
                <div className="Text">
                    <div className="SubTitle">Re-charge DSL Package</div>
                    <div className="description">
                        Settle your Sodetel DSL Package bill quickly and securely
                    </div>
                </div>
            </div>

            <div
                className="Cards"
                onClick={() => {
                    setCredential(fiberCredentials[0]);
                    setCredentialsArray(fiberCredentials);
                    handleButtonClick("BundleCredentials", "fiber");
                }}
            >
                <img
                    className="logoImg"
                    src="/build/images/sodetel/sodetel-bundle.png"
                    alt="suyool-sedetelLogo"
                />
                <div className="Text">
                    <div className="SubTitle">Re-charge Fiber Package</div>
                    <div className="description">Settle your Sodetel Fiber Package bill quickly and securely</div>
                </div>
            </div>

            <div
                className="Cards"
                onClick={() => {
                    setCredential(fourGCredentials[0]);
                    setCredentialsArray(fourGCredentials);
                    handleButtonClick("BundleCredentials", "4g");
                }}
            >
                <img
                    className="logoImg"
                    src="/build/images/sodetel/sodetel-bundle.png"
                    alt="suyool-sedetelLogo"
                />
                <div className="Text">
                    <div className="SubTitle">Re-charge 4G Package</div>
                    <div className="description">Settle your Sodetel 4G Package bill quickly and securely</div>
                </div>
            </div>

        </div>
    );

};

export default Default;
