import React, { useState, useEffect } from "react";
import Header from "./Component/Header";
import ApplyForCorporate from "./Component/ApplyForCorporate";

const App = ({ parameters }) => {
    console.log(parameters);

    return (
        <>
            <Header />

            <div id="LegalEnrollementBody">
                <ApplyForCorporate />

            </div>

        </>
    );
};

export default App;