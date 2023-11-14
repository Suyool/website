import React, { useEffect, useState } from "react";
import axios from "axios";

const UsernameForm = ({ setProducts, setActiveButton, setBackLink, setErrorModal, setModalName, setModalShow }) => {
    const [inputValue, setInputValue] = useState("");
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        setBackLink("");
    }, []);

    const formatPhoneNumber = (number) => {
        // If the number is 6 digits and starts with 3, add 0 at the beginning
        if (number.length === 7 && number.startsWith("3")) {
            return "0" + number;
        }

        // If the number is greater than 8 digits and starts with 961, remove the 961
        if (number.length > 8 && number.startsWith("961")) {
            // Remove 961
            number = number.substring(3);

            // If it starts with 3, add 0 at the beginning
            if (number.startsWith("3")) {
                number = "0" + number;
            }
        }

        return number;
    };

    const handleUsernameSubmit = () => {
        setLoading(true);

        // Format the phone number before sending the request
        const formattedNumber = formatPhoneNumber(inputValue);

        axios
            .post("/terraNet/get_accounts", { username: formattedNumber })
            .then((response) => {
                if (response.data.flag === 2) {
                    // Show the ErrorModal with the response message
                    setModalName("ErrorModal");
                    setErrorModal({
                        img: "/build/images/alfa/error.png",
                        title: "Error",
                        desc: response.data.return,
                        btn: "OK",
                    });
                    setModalShow(true);
                } else {
                    // Proceed with the normal flow
                    setProducts(response.data.return);
                    setActiveButton({ name: "inputValue" });
                }
            })
            .catch((error) => {
                console.error("Error:", error);
            })
            .finally(() => {
                setLoading(false);
            });
    };

    // Check if the input starts with a letter
    const isInputLetter = /^[a-zA-Z]/.test(inputValue);

    return (
        <div id="PayBill" className="username-form">
            <div className="mainTitle">
                Enter your Terranet username or Lebanese phone number
            </div>
            <div className="MobileNbContainer mt-3">
                {isInputLetter ? null : (
                    <div className="place">
                        <img src="/build/images/alfa/flag.png" alt="flag" />
                        <div className="code">+961</div>
                    </div>
                )}
                <input
                    type="text"
                    className={`nbInput${isInputLetter ? ' w-100' : ''}`}
                    placeholder="Username or Number"
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
    );
};

export default UsernameForm;
