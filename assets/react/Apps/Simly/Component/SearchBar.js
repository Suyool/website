// SearchBar.js
import React from "react";
import { settingObjectData } from "../Redux/Slices/AppSlice";
import { BiXCircle } from "react-icons/bi";

const SearchBar = ({
  searchQuery,
  handleSearchChange,
  dispatch,
  setSearchQuery,
}) => {
  return (
    <div className="search-bar mt-4">
      <div className="search-icon-left">
        <img src="/build/images/g2g/search.svg" alt="Search Icon" />
      </div>
      <div>
        <input
          type="search"
          placeholder="Search Destination"
          value={searchQuery}
          onChange={handleSearchChange}
          //   type="url"
        />
        {searchQuery && (
          <BiXCircle
            style={{
              backgroundColor: "#f4f4f4",
              position: "relative",
              right: "27px",
              cursor: "pointer",
              top:"-3px"
            }}
            onClick={() => setSearchQuery("")}
          />
        )}
      </div>
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
