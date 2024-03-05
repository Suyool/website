import React, { useEffect, useState } from "react";
import AppAPI from "../../TerraNet/Api/AppAPI";
import {settingData, settingObjectData} from "../../TerraNet/Redux/Slices/AppSlice";
import {useDispatch} from "react-redux";

const UsernameForm = () => {
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
                    currentPage: "UsernameForm",
                },
            })
        );
        dispatch(settingObjectData({ mainField: "bottomSlider", field: "isButtonDisable", value: false }));
    }, []);

    const handleUsernameSubmit = () => {

        localStorage.setItem(
            "UserAccount",
            inputValue
        );

        localStorage.setItem(
            "Type",
            "username"
        );
        getAccounts(inputValue);

    };

    return (
        <>
            <div id="PayBill" className="username-form">
                <div className="mainTitle">
                    Enter your Terranet username to recharge
                </div>
                <div className="MobileNbContainer mt-3">

                    <input
                        type="text"
                        className={`nbInput w-100`}
                        placeholder="Username"
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

export default UsernameForm;
