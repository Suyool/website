import React from "react";

const Header = ({
                    parameters,
                    activeButton,
                    setActiveButton,
                    getHeaderTitle,
                    getBackLink,
                    getSpinnerLoader,
                    setIsPackageItem,
                    isPackageItem
                }) => {
    const handleButtonClick = (getBackLink) => {
        if (isPackageItem) {
            setIsPackageItem(false)
        } else {
            if (activeButton.name == "") {
                if (parameters?.deviceType === "Android") {
                    window.AndroidInterface.callbackHandler("GoToApp");
                } else if (parameters?.deviceType === "Iphone") {
                    window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
                }
            }
            setActiveButton({name: getBackLink});
        }

    };

    return (
        <div id="MobileHeader" className={` ${
            getSpinnerLoader ? "packagesinfo hideBackk" : ""
        }`}>
            <div
                className="back"
                onClick={() => {
                    handleButtonClick(getBackLink);
                }}
            >
                <img src="/build/images/alfa/Back.png" alt="Back"/>
            </div>
            <div className="headerTitle">{getHeaderTitle}</div>
            <div className="empty"></div>
        </div>
    );
};

export default Header;
