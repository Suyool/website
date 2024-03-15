import React, { useEffect } from "react";
import Account from "./Pages/Account";
import PlanDetail from "./Pages/PlanDetail";
import RechargeThePayment from "./Pages/RechargeThePayment";
import Packages from "./Pages/Packages";
import PackagesInfo from "./Pages/PackagesInfo";
import Header from "./Component/Header";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "./Redux/Slices/AppSlice";
import PopupModal from "./Component/PopupModal";
import BottomSlider from "./Component/BottomSlider";
import { Spinner } from "react-bootstrap";

const App = ({ parameters }) => {
  const headerData = useSelector((state) => state.appData.headerData);
  const modalData = useSelector((state) => state.appData.modalData);
  const bottomSlider = useSelector((state) => state.appData.bottomSlider);
  const isLoading = useSelector((state) => state.appData.isloading);
  const dispatch = useDispatch();
  useEffect(() => {
    dispatch(settingData({ field: "parameters", value: parameters }));
    if(parameters?.isPopup){
    dispatch(
      settingData({
        field: "modalData",
        value: {
          isShow: true,
          name: "WarningModal",
          img: "/build/images/simly/warning.svg",
          title: "eSIM Compatibility",
          desc: (
            <div>
              Dial *#06# on your phone to verify eSIM compatibility. If you see the EID barcode, your device is compatible
            </div>
          ),
          btn: "Dial *#06#",
          flag: "",
        },
      })
    );
    }
    dispatch(
      settingData({
        field: "headerData",
        value: {
          title: "Global eSIM",
          backLink: "",
          currentPage: "Packages",
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

      <div className={`${(isLoading === true || bottomSlider.isShow === true) ? "hideBackk scrolableView" : "scrolableView"}`}>
        {isLoading === true && (
          <div id="spinnerLoader">
            <Spinner className="spinner" animation="border" variant="secondary" />
          </div>
        )}

        {(headerData.currentPage === "Packages") && <Packages />}
        {headerData.currentPage === "PackagesInfo" && <PackagesInfo />}
        {headerData.currentPage === "Account" && <Account />}
        {headerData.currentPage === "PlanDetail" && <PlanDetail />}
        {headerData.currentPage === "RechargeThePayment" && <RechargeThePayment />}

        
        {modalData.isShow && <PopupModal />}
      </div>
      {bottomSlider.isShow && <BottomSlider />}
    </div>
  );
};

export default App;
