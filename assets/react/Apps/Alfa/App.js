import React, { useEffect } from "react";
import Default from "./Pages/Default";
import MyBill from "./Pages/MyBill";
import MyBundle from "./Pages/MyBundle";
import PayBill from "./Pages/PayBill";
import ReCharge from "./Pages/ReCharge";
import Header from "./Component/Header";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "./Redux/Slices/AppSlice";
import PopupModal from "./Component/PopupModal";
import BottomSlider from "./Component/BottomSlider";

const App = ({ parameters }) => {
  const headerData = useSelector((state) => state.appData.headerData);
  const modalData = useSelector((state) => state.appData.modalData);
  const bottomSlider = useSelector((state) => state.appData.bottomSlider);
  const dispatch = useDispatch();
  useEffect(() => {
    dispatch(settingData({ field: "parameters", value: parameters }));
    dispatch(
      settingData({
        field: "headerData",
        value: {
          title: "Alfa",
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
      dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: idParam }));
    }
    window.handleCheckout = (message) => {
      dispatch(settingData({ field: "mobileResponse", value: message }));
    };
  });

  return (
    <div id="PageBody">
      <Header />

      <div className="scrolableView">
        {headerData.currentPage === "" && <Default />}
        {headerData.currentPage === "PayBill" && <PayBill />}
        {headerData.currentPage === "ReCharge" && <ReCharge />}
        {headerData.currentPage === "MyBill" && <MyBill />}
        {headerData.currentPage === "MyBundle" && <MyBundle />}
      </div>

      {bottomSlider.isShow && <BottomSlider />}
      {modalData.isShow && <PopupModal />}
    </div>
  );
};

export default App;
