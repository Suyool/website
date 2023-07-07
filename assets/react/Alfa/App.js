import React, { useState, useEffect } from "react";
import PayBill from "./PayBill";
import ReCharge from "./ReCharge";
import MyBill from "./MyBill";
import Default from "./Default";
import Header from "./Header";

const App = ({ parameters }) => {
    // console.log(parameters)
    const [activeButton, setActiveButton] = useState({ name: "" });
    const [getBackLink, setBackLink] = useState({ name: "" });
    const [getHeaderTitle, setHeaderTitle] = useState("Alfa");

    return (
        <div id="AlfaBody">
            <div className="scrolableView">

                <Header activeButton={activeButton} setActiveButton={setActiveButton} getHeaderTitle={getHeaderTitle} getBackLink={getBackLink} />

                {activeButton.name === "" && <Default activeButton={activeButton} setActiveButton={setActiveButton} setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />}

                {activeButton.name === "PayBill" && <PayBill activeButton={activeButton} setActiveButton={setActiveButton} setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />}
                {activeButton.name === "ReCharge" && <ReCharge activeButton={activeButton} setActiveButton={setActiveButton} setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />}

                {activeButton.name === "MyBill" && <MyBill activeButton={activeButton} setActiveButton={setActiveButton} setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />}

            </div>

        </div>
    );
};

export default App;