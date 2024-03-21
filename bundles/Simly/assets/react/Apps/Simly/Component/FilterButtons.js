// FilterButtons.js
import React from "react";
import {settingObjectData} from "../Redux/Slices/AppSlice";
import {useSelector} from "react-redux";

const FilterButtons = ({ dispatch}) => {
    const {view} = useSelector((state) => state.appData.headerData);

    return (
        <div className="filter-btns-cont">
            <div className="btnCon  d-flex justify-content-center">
                <button
                    className={
                        "btn btn-primary " + (view === "countries" ? "active" : "")
                    }
                    onClick={() => {
                        localStorage.setItem("parentPlanType", "Local");
                        dispatch(
                            settingObjectData({
                                mainField: "headerData",
                                field: "view",
                                value: "countries",
                            })
                        );
                        dispatch(
                            settingObjectData({
                                mainField: "simlyData",
                                field: "isPackageItem",
                                value: false,
                            })
                        );
                    }}
                >
                    Per Country
                </button>
            </div>
            <div className="btnCon  d-flex justify-content-center">
                <button
                    className={
                        "btn btn-primary " + (view === "regions" ? "active" : "")
                    }
                    onClick={() => {
                        localStorage.setItem("parentPlanType", "Regional");
                        dispatch(
                            settingObjectData({
                                mainField: "headerData",
                                field: "view",
                                value: "regions",
                            })
                        );
                        dispatch(
                            settingObjectData({
                                mainField: "simlyData",
                                field: "isPackageItem",
                                value: false,
                            })
                        );
                    }}
                >
                    Per Region
                </button>
            </div>
            <div className="btnCon d-flex justify-content-center">
                <button
                    className={
                        "btn btn-primary " + (view === "global" ? "active" : "")
                    }
                    onClick={() => {
                        localStorage.setItem("parentPlanType", "Global");
                        dispatch(
                            settingObjectData({
                                mainField: "headerData",
                                field: "view",
                                value: "global",
                            })
                        );
                        dispatch(
                            settingObjectData({
                                mainField: "simlyData",
                                field: "isPackageItem",
                                value: false,
                            })
                        );
                    }}
                >
                    Global
                </button>
            </div>
        </div>

    );
};

export default FilterButtons;
