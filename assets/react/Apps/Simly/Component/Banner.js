import React from "react";
import {useDispatch, useSelector} from "react-redux";
import {settingObjectData} from "../Redux/Slices/AppSlice";

const Banner = ({havingCard}) => {
    const headerData = useSelector((state) => state.appData.headerData);
    const parameters = useSelector((state) => state.appData.parameters);
    const dispatch = useDispatch();



    const requestCard = () => {
        if (headerData.currentPage === "") {
            if (parameters?.deviceType === "Android") {
                window.AndroidInterface.callbackHandler("GoToApp");
            } else if (parameters?.deviceType === "Iphone") {
                window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
            }
        }
    };

    const activateEsim = () => {
        dispatch(
            settingObjectData({
                mainField: "headerData",
                field: "view",
                value: "offers",
            })
        );
    };

    return (
        <div className="banner" style={{textAlign: "center"}}>
            {havingCard ? (
                <img
                    src="build/images/simly/activate.png"
                    alt="Activate"
                    style={{cursor: "pointer"}}
                    onClick={activateEsim}
                />
            ) : (
                <img
                    src="build/images/simly/request.png"
                    alt="Request"
                    onClick={requestCard}
                    style={{cursor: "pointer"}}
                />
            )}
        </div>
    );
}
;

export default Banner;
