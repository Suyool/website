import React, { useEffect } from "react";
import Default from "./Pages/Default";
import Header from "./Component/Header";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "./Redux/Slices/AppSlice";
import PopupModal from "./Component/PopupModal";
import BottomSlider from "./Component/BottomSlider";
import { Spinner } from "react-bootstrap";
import Login from "./Pages/Login";
import Topup from "./Pages/Topup";

const App = ({ parameters }) => {
  const headerData = useSelector((state) => state.appData.headerData);
  const modalData = useSelector((state) => state.appData.modalData);
  const bottomSlider = useSelector((state) => state.appData.bottomSlider);
  const isLoading = useSelector((state) => state.appData.isloading);
  const dispatch = useDispatch();
  useEffect(() => {
    dispatch(settingData({ field: "parameters", value: parameters }));
    dispatch(
      settingData({
        field: "headerData",
        value: {
          title: "WinDSL",
          backLink: "",
          currentPage: "",
        },
      })
    );
  }, []);

  useEffect(() => {
    dispatch(settingData({ field: "mobileResponse", value: "" }));
    window.handleCheckout = (message) => {
      dispatch(settingData({ field: "mobileResponse", value: message }));
    };
  });

  return (
    <div id="PageBody">
      <Header />

      <div
        className={`${
          isLoading === true ? "hideBackk scrolableView" : "scrolableView"
        }`}
      >
        {isLoading === true && (
          <div id="spinnerLoader">
            <Spinner
              className="spinner"
              animation="border"
              variant="secondary"
            />
          </div>
        )}
        {headerData.currentPage === "" && <Default />}
        {headerData.currentPage === "Login" && <Login />}
        {headerData.currentPage === "Topup" && <Topup />}
        {bottomSlider.isShow && <BottomSlider />}
        {modalData.isShow && <PopupModal />}
      </div>
    </div>
  );
};

export default App;
