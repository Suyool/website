import React, { useEffect } from "react";
import Default from "./Pages/Default";
import MyBill from "./Pages/MyBill";
import MyBundle from "./Pages/MyBundle";
import PayBill from "./Pages/PayBill";
import ReCharge from "./Pages/ReCharge";
import Header from "./Pages/Header";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "./Redux/Slices/AppSlice";
import PopupModal from "./Component/PopupModal";

const App = ({ parameters }) => {
  const headerData = useSelector((state) => state.appData.headerData);
  const modalData = useSelector((state) => state.appData.modalData);
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
    <div id="AlfaBody">
      <Header />
      {modalData.isShow && <PopupModal />}
      <div className="scrolableView">
        {headerData.currentPage === "" && <Default />}
        {headerData.currentPage === "PayBill" && <PayBill />}
        {headerData.currentPage === "ReCharge" && <ReCharge />}
        {headerData.currentPage === "MyBill" && <MyBill />}
        {headerData.currentPage === "MyBundle" && <MyBundle />}
      </div>
    </div>
  );
};

export default App;
