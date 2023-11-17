import React, { useEffect, useState } from "react";
import axios from "axios";

const UsernameForm = ({ setProducts, setActiveButton, setBackLink, setErrorModal, setModalName, setModalShow,setHeaderTitle }) => {
    const [inputValue, setInputValue] = useState("");
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        setHeaderTitle("Pay Landline Bill");
        setBackLink("");
    }, []);


    const handleUsernameSubmit = () => {
        setLoading(true);

        localStorage.setItem(
            "UserAccount",
            inputValue
        );
        axios
            .post("/terraNet/get_accounts", { username: inputValue })
            .then((response) => {
                if (response.data.flag === 2) {
                    // Show the ErrorModal with the response message
                    setModalName("ErrorModal");
                    setErrorModal({
                        img: "/build/images/alfa/error.png",
                        title: "Account Not Found",
                        desc: (
                            <div>
                                The Username you entered was not found in the system.
                                <br />
                                Kindly try another one.
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
                console.error("Error:", error);
            })
            .finally(() => {
                setLoading(false);
            });
    };

    return (
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
    );
};

export default UsernameForm;
