import React, {useState} from 'react';
import Default from "./components/Default";

function App({ parameters }) {
    const [activeButton, setActiveButton] = useState({ name: "" });
    const [getBackLink, setBackLink] = useState({ name: "" });
    const [getHeaderTitle, setHeaderTitle] = useState("Alfa");

    console.log(parameters)

    return (
        <Default setActiveButton={setActiveButton} setBackLink={setBackLink} setHeaderTitle={setHeaderTitle}  />
    );
}

export default App;