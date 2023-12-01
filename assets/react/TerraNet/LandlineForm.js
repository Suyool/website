import React, { useEffect, useState } from "react";
import axios from "axios";
import { Spinner } from "react-bootstrap";

const LandlineForm = ({ setProducts, setActiveButton, setBackLink, setErrorModal, setModalName, setModalShow,setHeaderTitle }) => {
    const [inputValue, setInputValue] = useState("");
    const [loading, setLoading] = useState(false);
    const [getSpinnerLoader, setSpinnerLoader] = useState(false);

    useEffect(() => {
        setHeaderTitle("Pay Landline Bill");
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
        localStorage.setItem(
            "UserAccount",
            number
        );
        localStorage.setItem(
            "Type",
            "Landline"
        );
        return number;
    };

    const handleUsernameSubmit = () => {
        setLoading(true);
        setSpinnerLoader(true);

        // Format the phone number before sending the request
        const formattedNumber = formatPhoneNumber(inputValue);

        axios
            .post("/terraNet/get_accounts", { username: formattedNumber })
            .then((response) => {
                setSpinnerLoader(false);
                if (response.data.flag === 2) {
                    // Show the ErrorModal with the response message
                    setModalName("ErrorModal");
                    setErrorModal({
                        img: "/build/images/alfa/error.png",
                        title: "Error",
                        desc: (
                            <div>
                                The number you entered was not found in the system.
                                <br />
                                Kindly try another number.
                            </div>
                        ),
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
                setSpinnerLoader(false);
                console.error("Error:", error);
            })
            .finally(() => {
                setLoading(false);
                setSpinnerLoader(false);
            });
    };

    // Check if the input starts with a letter
    const isInputLetter = /^[a-zA-Z]/.test(inputValue);

    return (
        <>
            {getSpinnerLoader && (
                <div id="spinnerLoader" className="overlay">
                    <Spinner
                        className="spinner"
                        animation="border"
                        variant="secondary"
                    />
                </div>
            )}
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
