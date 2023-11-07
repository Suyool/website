import React, { useEffect } from "react";
import axios from "axios";

const Default = ({setHeaderTitle, setBackLink, setActiveButton }) => {

    setHeaderTitle("Alfa");
    setBackLink("default");

    const handleButtonClick = (name) => {
        setActiveButton({ name: name });
    };

    return (
        <div id="Default">
            <div className="MainTitle">What do you want to do?</div>

            <div
                className="Cards"
                onClick={() => {
                    handleButtonClick("PayBill");
                }}
            >
                <img
                    className="logoImg"
                    src="/build/images/sodetel/sodetel-logo.png"
                    alt="suyool-sedetelLogo"
                />
                <div className="Text">
                    <div className="SubTitle">Pay Bills</div>
                    <div className="description">
                        Settle your Sodetel bill quickly and securely
                    </div>
                </div>
            </div>

            <div
                className="Cards"
                // onClick={() => {
                //     handleButtonClick("ReCharge");
                //
                //     axios
                //         .post("/alfa/ReCharge")
                //         .then((response) => {
                //             SetVoucherData(response?.data?.message);
                //         })
                //         .catch((error) => {
                //             console.log(error);
                //         });
                // }}
            >
                <img
                    className="logoImg"
                    src="/build/images/sodetel/sodetel-logo.png"
                    alt="suyool-sedetelLogo"
                />
                <div className="Text">
                    <div className="SubTitle">Re-charge Sodetel</div>
                    <div className="description">Recharge your Sodetel Prepaid Plan</div>
                </div>
            </div>
        </div>
    );
};

export default Default;
