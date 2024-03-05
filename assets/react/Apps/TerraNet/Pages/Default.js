import React from "react";
import {useDispatch} from "react-redux";
import {settingObjectData} from "../Redux/Slices/AppSlice";

const Default = () => {
    const dispatch = useDispatch();
    return (
        <div id="Default">
            <div className="MainTitle">Re-charge your TerraNet plan using your:</div>

            <div
                className="Cards"
                onClick={() => {
                    dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "LandlineForm" }));
                }}
            >
                <img
                    className="logoImg"
                    src="/build/images/terraNet/terraNetLogo.png"
                    alt="alfaLogo"
                />
                <div className="Text">
                    <div className="SubTitle">LANDLINE NUMBER</div>
                    <div className="description">
                        Settle your Terranet bill quickly and securely using your landline
                    </div>
                </div>
            </div>

            <div
                className="Cards"
                onClick={() => {
                    dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "UsernameForm" }));
                }}
            >
                <img
                    className="logoImg"
                    src="/build/images/terraNet/terraNetLogo.png"
                    alt="alfaLogo"
                />
                <div className="Text">
                    <div className="SubTitle">TERRANET USERNAME</div>
                    <div className="description">Settle your TerraNet bill quickly and securely using your username
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Default;
