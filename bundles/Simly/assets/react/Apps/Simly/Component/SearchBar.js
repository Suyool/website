// SearchBar.js
import React from "react";
import {settingObjectData} from "../Redux/Slices/AppSlice";

const SearchBar = ({ searchQuery, handleSearchChange, dispatch }) => {
    return (
        <div className="search-bar mt-4">
            <div className="search-icon-left">
                <img src="/build/images/g2g/search.svg" alt="Search Icon" />
            </div>
            <input
                type="text"
                placeholder="Search Destination"
                value={searchQuery}
                onChange={handleSearchChange}
            />
            <div
                className="search-icon-right"
                onClick={() => {
                    dispatch(
                        settingObjectData({
                            mainField: "headerData",
                            field: "currentPage",
                            value: "Account",
                        })
                    );
                    dispatch(
                        settingObjectData({
                            mainField: "simlyData",
                            field: "isPackageItem",
                            value: false,
                        })
                    );
                    dispatch(
                        settingObjectData({
                            mainField: "simlyData",
                            field: "SelectedCountry",
                            value: null,
                        })
                    );
                }}
            >
                <img src="/build/images/topUpSimIcon.svg" alt="Icon" />
            </div>
        </div>
    );
};

export default SearchBar;
