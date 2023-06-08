import React, { useState } from "react";
import BottomNav from "./components/BottomNav";

const App = ({ v }) => {
    const [activeButton, setActiveButton] = useState({ name: "LLDJ" });


    return (
        <div id="LotoBody">
            <div>
                {activeButton.name === "LLDJ" && "LLDJ"}
                {activeButton.name === "Play" && "Play"}
                {activeButton.name === "Result" && "Result"}
            </div>

            <BottomNav activeButton={activeButton} setActiveButton={setActiveButton} />
        </div>
    );
};

export default App;