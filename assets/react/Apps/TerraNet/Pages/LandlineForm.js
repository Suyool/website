import React, { useEffect, useState } from "react";
import AppAPI from "../../TerraNet/Api/AppAPI";
import {settingData, settingObjectData} from "../../Alfa/Redux/Slices/AppSlice";
import {useDispatch} from "react-redux";
import { formatPhoneNumber } from "../Utils/functions";

const LandlineForm = () => {
    const [inputValue, setInputValue] = useState("");
    const dispatch = useDispatch();
    const { getAccounts } = AppAPI();

    useEffect(() => {
        dispatch(
            settingData({
                field: "headerData",
                value: {
                    title: "Pay Landline Bill",
                    backLink: "",
                    currentPage: "LandlineForm",
                },
            })
        );
        dispatch(settingObjectData({ mainField: "bottomSlider", field: "isButtonDisable", value: false }));
    }, []);

    const handleUsernameSubmit = () => {
        const phoneNumber = formatPhoneNumber(inputValue)

        localStorage.setItem(
            "UserAccount",
            phoneNumber
        );
        localStorage.setItem(
            "Type",
            "Landline"
        );

        getAccounts(phoneNumber);


    };

    // Check if the input starts with a letter

    return (
        <>
            <div id="PayBill" className="username-form">
                <div className="mainTitle">
                    Enter your landline number to recharge
                </div>
                <div className="MobileNbContainer mt-3">
                    <div className="place">
                        <img src="/build/images/alfa/flag.png" alt="flag" />
                        <div className="code">+961</div>
                    </div>
                    <input
                        type="tel"
                        className={`nbInput`}
                        placeholder="Landline Number"
                        value={inputValue}
                        onChange={(e) => setInputValue(e.target.value)}
                    />
                </div>
                <button
                    id="ContinueBtn"
                    className={"btnContFocus"}
                    disabled={inputValue === ""}
                    onClick={handleUsernameSubmit}
                >
                    Continue
                </button>
            </div>

        </>
    );
};

export default LandlineForm;
