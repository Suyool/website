import React, { useEffect } from "react";

const Default = ({ SetVoucherData, setActiveButton, setHeaderTitle, setBackLink, setShowUsernameInputForm }) => {

    useEffect(() => {
        setHeaderTitle("Suyool eSim");
        setBackLink("");
    }, []);

    const handleButtonClick = (name) => {
        setActiveButton({ name: name });
    };

    return (
        <div id="Default_simly">
            <div className="MainTitle">Suyool eSim for your travels</div>

            <div
                className="Cards"
                onClick={() => {
                    handleButtonClick("Packages");
                }}
            >
                <img
                    className="logoImg"
                    src="/build/images/simlyIcon.svg"
                    alt="simlyLogo"
                />
                <div className="Text">
                    <div className="SubTitle">Stay Connected When Abroad</div>
                    <div className="description">Buy your international eSim in just a few taps</div>
                </div>
            </div>
        </div>
    );
};

export default Default;
