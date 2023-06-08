// import React from "react";
// import * as ReactDOM from "react-dom";

// ReactDOM.render(
//     <h1>Hello world ! tst</h1>,

//     document.getElementById("loto")
// );

// import React from "react";
// import {createRoot} from "react-dom/client";
// import App from "./components/app";

// const container = document.getElementById('loto');
// const root = createRoot(container);


// // const dataElement = document.getElementById('loto');
// // const data = JSON.parse(dataElement.dataset.data);


// root.render(
//     <React.StrictMode>
//         <App v={data} />
//     </React.StrictMode>
// );

import React from "react";
import { createRoot } from "react-dom/client";
import App from "./components/app";

const container = document.getElementById('loto');
const root = createRoot(container);

const data = JSON.parse(container.dataset.data);

root.render(
    <React.StrictMode>
        <App v={data} />
    </React.StrictMode>
);