import React from "react";
import { createRoot } from "react-dom/client";
import App from "./App";

const container = document.getElementById("sodetel");
const root = createRoot(container);

const data = JSON.parse(container.dataset.data);

root.render(
    <React.StrictMode>
        <App parameters={data} />
    </React.StrictMode>
);