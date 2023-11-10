import React, {useEffect, useState} from "react";
import axios from "axios";

const UsernameForm = ({setProducts, setActiveButton,setBackLink}) => {
    const [inputValue, setInputValue] = useState("");
    const [loading, setLoading] = useState(false);

    useEffect(()=>{
        setBackLink("")
    },[])

    const handleUsernameSubmit = () => {
        setLoading(true);
        axios
            .post("/terraNet/get_accounts", {username: inputValue})
            .then((response) => {
                setProducts(response.data.return);
                setActiveButton({name: "inputValue"})
            })
            .catch((error) => {
                console.error("Error:", error);
            }).finally(() => {
            setLoading(false);
        });
    };


    return (
        <div id="PayBill" className="username-form">
            <div className="mainTitle">Enter your Terranet username or number</div>
            <div className="MobileNbContainer mt-3">
                <div className="place">
                    <img src="/build/images/alfa/flag.png" alt="flag"/>
                    <div className="code">+961</div>
                </div>
                <input
                    type="tel"
                    className="nbInput"
                    placeholder="Username or Number"
                    value={inputValue}
                    onChange={(e) => setInputValue(e.target.value)}
                />
            </div>
            <button
                id="ContinueBtn"
                className={"btnContFocus"}
                disabled={inputValue == ""}
                onClick={handleUsernameSubmit}
            >
                Continue
            </button>
        </div>
    );
};

export default UsernameForm;
