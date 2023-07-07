import React, { useState, useEffect } from "react";
import PayBill from "./PayBill";
import ReCharge from "./ReCharge";
import MyBill from "./MyBill";
import Default from "./Default";

const App = ({ parameters }) => {
    // console.log(parameters)
    const [activeButton, setActiveButton] = useState({ name: "" });

    return (
        <div id="AlfaBody">
            <div className="scrolableView">

                {activeButton.name === "" && <Default activeButton={activeButton} setActiveButton={setActiveButton} />}

                {activeButton.name === "PayBill" && <PayBill activeButton={activeButton} setActiveButton={setActiveButton} />}
                {activeButton.name === "ReCharge" && <ReCharge activeButton={activeButton} setActiveButton={setActiveButton} />}

                {activeButton.name === "MyBill" && <MyBill activeButton={activeButton} setActiveButton={setActiveButton}/>}

            </div>

        </div>
    );
};

export default App;