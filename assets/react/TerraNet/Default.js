import React, { useEffect } from "react";
import axios from "axios";

const Default = ({ SetVoucherData, setActiveButton, setHeaderTitle, setBackLink, setShowUsernameInputForm }) => {

    useEffect(() => {
        setHeaderTitle("TerraNet");
        setBackLink("default");
    }, []);

    const handleButtonClick = (name) => {
        setActiveButton({ name: name });
    };

    return (
        <div id="Default">
            <div className="MainTitle">Re-charge your TerraNet plan using your:</div>
            {/* <div
                className="Cards"
                onClick={() => {
                    handleButtonClick("AccountForm");
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
            </div> */}

            <div
                className="Cards"
                onClick={() => {
                    handleButtonClick("UsernameForm");
                }}
            >
                <img
                    className="logoImg"
                    src="/build/images/terraNet/terraNetLogo.png"
                    alt="alfaLogo"
                />
                <div className="Text">
                    <div className="SubTitle">TERRANET USERNAME</div>
                    <div className="description">Settle your TerraNet bill quickly and securely using your username</div>
                </div>
            </div>
        </div>
    );
};

export default Default;
