import React, { useEffect } from "react";
import Default from "./Pages/Default";
import Header from "./Component/Header";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "./Redux/Slices/AppSlice";
import PopupModal from "./Component/PopupModal";
import BottomSlider from "./Component/BottomSlider";
import { Spinner } from "react-bootstrap";
import BundleCredentials from "./Pages/BundleCredentials";
import ReCharge from "./Pages/ReCharge";
import Refill from "./Pages/Refill";

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
          title: "Sodetel",
          backLink: "",
          currentPage: "",
        },
      })
    );
  }, []);

  useEffect(() => {
    dispatch(settingData({ field: "mobileResponse", value: "" }));
    const searchParams = new URLSearchParams(window.location.search);
    const idParam = searchParams.get("comp");
    if (idParam) {
      dispatch(
        settingObjectData({
          mainField: "headerData",
          field: "currentPage",
          value: idParam,
        })
      );
    }
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
        {headerData.currentPage === "BundleCredentials" && <BundleCredentials />}
        {headerData.currentPage === "ReCharge" && <ReCharge />}
        {headerData.currentPage === "Refill" && <Refill />}
        {bottomSlider.isShow && <BottomSlider />}
        <PopupModal />
      </div>
    </div>
  );
};

export default App;
