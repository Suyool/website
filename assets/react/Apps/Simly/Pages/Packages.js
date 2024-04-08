import React, { useState, useEffect } from "react";
import ContentLoader from "react-content-loader";
import PackageItems from "./PackageItems";
import { useDispatch, useSelector } from "react-redux";
import AppAPI from "../Api/AppAPI";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";
import Banner from "../Component/Banner";
import FilterButtons from "../Component/FilterButtons"; // Import the Banner component
import SearchBar from "../Component/SearchBar";
import CustomContentLoader from "../Component/ContentLoader";
import Regions from "./Regions";
import Global from "./Global";
import Countries from "./Countries";
import Offers from "./Offers";

const Packages = ({ setSelectedPlan, setSelectedPackage }) => {
  const dispatch = useDispatch();
  const {
    GetAllAvailableCountries,
    GetLocalAvailableCountries,
    GetPlansUsingISOCode,
  } = AppAPI();
  useEffect(() => {
    dispatch(
      settingData({
        field: "headerData",
        value: {
          title: "Global eSIM",
          backLink: "Packages",
          currentPage: "Packages",
        },
      })
    );
    GetAllAvailableCountries();
    GetLocalAvailableCountries();
    dispatch(settingData({ field: "isloading", value: false }));
    localStorage.setItem("parentPlanType", "Local");
  }, []);

  const simlyData = useSelector((state) => state.appData.simlyData);
  const isLoadingData = useSelector((state) => state.appData.isLoadingData);
  const parameters = useSelector((state) => state.appData.parameters);

  const { view } = useSelector((state) => state.appData.headerData);
  const [searchQuery, setSearchQuery] = useState("");
  const [filteredData, setFilteredData] = useState([]);

  const [xButton, setXButton] = useState(false);

  const filterData = (data, query) => {
    if (!Array.isArray(data)) {
      console.error("Data is not an array");
      return [];
    }

    return data
      .map((continentObj) => {
        if (typeof continentObj !== "object" || continentObj === null) {
          console.error("Invalid continent object:", continentObj);
          return null;
        }

        const continent = Object.keys(continentObj)[0];
        const countries =
          continentObj[continent]?.filter((country) =>
            country.name.toLowerCase().includes(query.toLowerCase())
          ) || [];

        if (countries.length > 0) {
          return { [continent]: countries };
        }

        return null;
      })
      .filter(Boolean);
  };

  useEffect(() => {
    dispatch(
      settingObjectData({
        mainField: "headerData",
        field: "view",
        value: "countries",
      })
    );
    if (searchQuery.length >= 3) {
      const filtered = filterData(
        simlyData?.AvailableCountriesLocal,
        searchQuery
      );
      setFilteredData(filtered);
    } else {
      dispatch(
        settingObjectData({
          mainField: "headerData",
          field: "view",
          value: "countries",
        })
      );
      const filtered = filterData(simlyData?.AvailableCountriesLocal, "");
      setFilteredData(filtered || []);
    }
  }, [searchQuery, simlyData?.AvailableCountriesLocal]);

  const handleSearchChange = (event) => {
    const query = event.target.value;
    setSearchQuery(query);
    setXButton(true);
  };

  const handleClick = (isoCode) => {
    GetPlansUsingISOCode(isoCode);
  };

  const displaycontinent = (continent) => {
    switch (continent) {
      case "EU":
        return "Europe";
      case "NA":
        return "North America";
      case "ME":
        return "Middle East";
      case "AS":
        return "Asia";
      case "AF":
        return "Africa";
      case "SA":
        return "South America";
      case "OC":
        return "Oceania";

      default:
        return continent;
    }
  };

  return (
    <>
      <div className="container">
        <SearchBar
          searchQuery={searchQuery}
          handleSearchChange={handleSearchChange}
          dispatch={dispatch}
          setSearchQuery={setSearchQuery}
        />
        {view !== "offers" && (
          <>
            <Banner havingCard={parameters?.havingCard} />
            <FilterButtons view={view} dispatch={dispatch} />
          </>
        )}
      </div>
      {simlyData.isPackageItem &&
        simlyData?.SelectedCountry &&
        view !== "offers" && (
          <PackageItems country={simlyData?.SelectedCountry} />
        )}

      {!simlyData.isPackageItem && (
        <>
          {view === "offers" && <Offers country="" />}
          {view === "regions" && (
            <Regions simlyData={simlyData} handleClick={handleClick} />
          )}
          {view === "global" && (
            <Global simlyData={simlyData} handleClick={handleClick} />
          )}
          {view === "countries" && (
            <>
              {isLoadingData ? (
                <CustomContentLoader />
              ) : (
                view === "countries" && (
                  <Countries
                    filteredData={filteredData}
                    handleClick={handleClick}
                    displaycontinent={displaycontinent}
                  />
                )
              )}
            </>
          )}
        </>
      )}
    </>
  );
};

export default Packages;
