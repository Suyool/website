import React from "react";
import { createRoot } from "react-dom/client";
import App from "./App";
import { Provider } from "react-redux";
import store from "./Redux/store";

const container = document.getElementById("sodetel");
const root = createRoot(container);

const data = JSON.parse(container.dataset.data);

root.render(
  <Provider store={store}>
    <App parameters={data} />
  </Provider>
);
