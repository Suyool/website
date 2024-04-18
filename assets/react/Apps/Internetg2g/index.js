import React from "react";
import {createRoot} from "react-dom/client";
import App from "./App";
import store from "./Redux/store";
import {Provider} from "react-redux";

const container = document.getElementById("ineternetg2g");
const root = createRoot(container);

const data = JSON.parse(container.dataset.data);

root.render(
    <Provider store={store}>
        <App parameters={data}/>
    </Provider>
);
