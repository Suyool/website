import React from "react";
import { createRoot } from "react-dom/client";
import App from "./App";

const container = document.getElementById('loto');
const root = createRoot(container);

const data = JSON.parse(container.dataset.data);

root.render(
    <React.StrictMode>
        <App v={data} />
    </React.StrictMode>
);