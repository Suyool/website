import React, { useState, useEffect } from "react";
import Header from "./Component/Header";
import ApplyForCorporate from "./Component/ApplyForCorporate";
import SuccessfullySent from "./Component/SuccessfullySent";

const App = ({ parameters }) => {
    // console.log(parameters);

    const [getSent, steSent] = useState(false)
    return (
        <>
            <Header />

            <div id="LegalEnrollementBody">
                {getSent ?
                    <SuccessfullySent />
                    :
                    <ApplyForCorporate getSent={getSent} steSent={steSent} />
                }
            </div>

        </>
    );
};

export default App;