import React, {useEffect} from "react";
import Default from "./Pages/Default";
import MyBundle from "./Pages/MyBundle";
import Header from "./Component/Header";
import {useDispatch, useSelector} from "react-redux";
import {settingData, settingObjectData} from "./Redux/Slices/AppSlice";
import PopupModal from "./Component/PopupModal";
import BottomSlider from "./Component/BottomSlider";
import {Spinner} from "react-bootstrap";

const App = ({parameters}) => {
    const headerData = useSelector((state) => state.appData.headerData);
    const modalData = useSelector((state) => state.appData.modalData);
    const bottomSlider = useSelector((state) => state.appData.bottomSlider);
    const isLoading = useSelector((state) => state.appData.isloading);
    const dispatch = useDispatch();
    useEffect(() => {
        dispatch(settingData({field: "parameters", value: parameters}));
    }, []);
    const typeID = parameters.typeID;
    dispatch(settingData({field: "typeID", value: typeID}));

    const getDefaultImage = (typeID) => {
        switch (parseInt(typeID, 10)) {
            case 1:
                dispatch(settingData({field: "headerData", value: {title: "Gaming", backLink: "", currentPage: "",},}));
                return '/build/images/gameicon.svg';
            case 2:
                dispatch(settingData({
                    field: "headerData",
                    value: {title: "Streaming", backLink: "", currentPage: "",},
                }));
                return '/build/images/streamicon.svg';
            case 3:
                dispatch(settingData({field: "headerData", value: {title: "Gifts", backLink: "", currentPage: "",},}));
                return '/build/images/vouchersicon.svg';
            default:
                dispatch(settingData({
                    field: "headerData",
                    value: {title: "Gift2Games", backLink: "", currentPage: "",},
                }));
        }
    };
    useEffect(() => {
        getDefaultImage(typeID);
    }, [typeID]);
    useEffect(() => {
        dispatch(settingData({field: "mobileResponse", value: ""}));
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
            dispatch(settingData({field: "mobileResponse", value: message}));
        };
    });

    return (
        <div id="PageBody" className="G2GBody">
            <Header/>

            <div
                className={`${
                    (isLoading === true || bottomSlider.isShow === true) ? "hideBackk scrolableView" : "scrolableView"
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
                {headerData.currentPage === "" && <Default/>}
                {headerData.currentPage === "MyBundle" && <MyBundle/>}
                {modalData.isShow && <PopupModal/>}
            </div>
            {bottomSlider.isShow && <BottomSlider/>}

        </div>
    );
};

export default App;
