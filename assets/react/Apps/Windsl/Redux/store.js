import { configureStore } from "@reduxjs/toolkit";
import AppReducer from "./Slices/AppSlice";

const store = configureStore({
  reducer: {
    appData: AppReducer,
  },
});

export default store;
