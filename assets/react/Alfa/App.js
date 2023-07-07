import React, { useState, useEffect } from "react";
import PayBill from "./PayBill";
import ReCharge from "./ReCharge";
import Default from "./Default";

const App = ({ parameters }) => {
    // console.log(parameters)
    const [activeButton, setActiveButton] = useState({ name: "" });

    return (
        <div id="AlfaBody">
            <div className="scrolableView">

                {activeButton.name === "" && <Default activeButton={activeButton} setActiveButton={setActiveButton} />}

                {activeButton.name === "PayBill" && <PayBill />}
                {activeButton.name === "ReCharge" && <ReCharge />}


            </div>

        </div>
    );
};

export default App;