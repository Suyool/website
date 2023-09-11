import React, { useState, useEffect } from "react";
import Header from "./Component/Header";
import ApplyForCorporate from "./Component/ApplyForCorporate";
import SuccessfullySent from "./Component/SuccessfullySent";

const App = ({ parameters }) => {

    const [getSent, steSent] = useState(false)
    return (
        <>
            <Header />

            <div id="LegalEnrollementBody">
                {getSent ?
                    <SuccessfullySent />
                    :
                    <ApplyForCorporate getSent={getSent} steSent={steSent} env={parameters.ENV} />
                }
            </div>

        </>
    );
};

export default App;