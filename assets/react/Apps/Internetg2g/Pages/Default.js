import React, {useState} from "react";
import {useDispatch, useSelector} from "react-redux";
import {settingData, settingObjectData} from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";

const Default = () => {
    const dispatch = useDispatch();
    const {fetchProducts} = AppAPI();
    const typeID = useSelector((state) => state.appData.typeID);
    let subTitle1, description1, subTitle2, description2, subTitle3, description3,imgSrc;
    if (typeID == 4) {
        subTitle1 = "Buy DSL Plan";
        description1 = "Choose your DSL plan & buy the recharge code for your internet";
        subTitle2 = "Buy Fiber Plan";
        description2 = "Choose your Fiber plan & buy the recharge code for your internet";
        subTitle3 = "Buy Prepaid Wireless Plan";
        description3 = "Choose your prepaid wireless plan & buy the recharge code for your internet";
        imgSrc = "/build/images/g2g/connectLogo.png";
    } else if (typeID == 5) {
        subTitle1 = "Buy ADSL/VDSL";
        description1 = "Choose your ADSL/VDSL package & buy the recharge code for your internet";
        subTitle2 = "Buy MOBI 3G";
        description2 = "Choose your MOBI 3G package & buy the recharge code for your prepaid number";
        imgSrc = "/build/images/g2g/mobiLogo.png";
    } else if (typeID == 6) {
        subTitle1 = "Buy ADSL";
        description1 = "Choose your ADSL package & buy the recharge code for your internet";
        subTitle2 = "Buy Fiber Plan";
        description2 = "Choose your Fiber plan & buy the recharge code for your internet";
        imgSrc = "/build/images/g2g/terraNetLogo.png";
    }
    return (
        <div id="Default">
            <div className="MainTitle">What do you want to do?</div>

            <div
                className="Cards"
                onClick={() => {
                    dispatch(settingObjectData({mainField: "headerData", field: "currentPage", value: "ReCharge"}));
                    dispatch(settingData({field: "headerTitle", value: subTitle1}));
                    fetchProducts(typeID,1);
                }}
            >
                <img className="logoImg" src={imgSrc} alt="alfaLogo1" />
                <div className="Text">
                    <div className="SubTitle">{subTitle1}</div>
                    <div className="description">{description1}</div>
                </div>
            </div>

            <div
                className="Cards"
                onClick={() => {
                    dispatch(settingObjectData({mainField: "headerData", field: "currentPage", value: "ReCharge"}));
                    dispatch(settingData({field: "headerTitle", value: subTitle2}));
                    fetchProducts(typeID,2);
                }}
            >
                <img className="logoImg" src={imgSrc} alt="alfaLogo1" />
                <div className="Text">
                    <div className="SubTitle">{subTitle2}</div>
                    <div className="description">{description2}</div>
                </div>
            </div>
            {subTitle3 && description3 && (
                <div
                    className="Cards"
                    onClick={() => {
                        dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "ReCharge" }));
                        dispatch(settingData({field: "headerTitle", value: subTitle3}));
                        fetchProducts(typeID,3);
                    }}
                >
                <img className="logoImg" src={imgSrc} alt="alfaLogo1" />
                    <div className="Text">
                        <div className="SubTitle">{subTitle3}</div>
                        <div className="description">{description3}</div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default Default;
