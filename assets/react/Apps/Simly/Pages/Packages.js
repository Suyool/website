import React, { useState, useEffect } from "react";
import ContentLoader from "react-content-loader";
import PackageItems from "./PackageItems";
import { useDispatch, useSelector } from "react-redux";
import AppAPI from "../Api/AppAPI";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const Packages = ({ setSelectedPlan, setSelectedPackage }) => {
  const dispatch = useDispatch();
  const { GetAllAvailableCountries, GetLocalAvailableCountries, GetPlansUsingISOCode } = AppAPI();
  useEffect(() => {
    dispatch(
      settingData({
        field: "headerData",
        value: {
          title: "Simly",
          backLink: "",
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

  const [view, setView] = useState("countries");
  const [searchQuery, setSearchQuery] = useState("");
  const [filteredData, setFilteredData] = useState([]);

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
        const countries = continentObj[continent]?.filter((country) => country.name.toLowerCase().includes(query.toLowerCase())) || [];

        if (countries.length > 0) {
          return { [continent]: countries };
        }

        return null;
      })
      .filter(Boolean);
  };

  useEffect(() => {
    if (searchQuery.length >= 3) {
      setView("countries");
      dispatch(settingObjectData({ mainField: "simlyData", field: "isPackageItem", value: false }));
      const filtered = filterData(simlyData?.AvailableCountriesLocal, searchQuery);
      setFilteredData(filtered);
    } else {
      setView("countries");
      dispatch(settingObjectData({ mainField: "simlyData", field: "isPackageItem", value: false }));
      const filtered = filterData(simlyData?.AvailableCountriesLocal, "");
      setFilteredData(filtered || []);
    }
  }, [searchQuery, simlyData?.AvailableCountriesLocal]);

  const handleSearchChange = (event) => {
    const query = event.target.value;
    setSearchQuery(query);
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
        <div className="search-bar mt-4">
          <div className="search-icon-left">
            <img src="/build/images/g2g/search.svg" alt="Search Icon" />
          </div>
          <input type="text" placeholder="Search Destination" value={searchQuery} onChange={handleSearchChange} />
          <div
            className="search-icon-right"
            onClick={() => {
              dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "Account" }));
            }}
          >
            <img src="/build/images/topUpSimIcon.svg" alt="Icon" />
          </div>
        </div>
        <div className="filter-btns-cont">
          <div className="btnCon  d-flex justify-content-center">
            <button
              className={"btn btn-primary " + (view === "countries" ? "active" : "")}
              onClick={() => {
                localStorage.setItem("parentPlanType", "Local");
                setView("countries");
                dispatch(settingObjectData({ mainField: "simlyData", field: "isPackageItem", value: false }));
              }}
            >
              Per Country
            </button>
          </div>
          <div className="btnCon  d-flex justify-content-center">
            <button
              className={"btn btn-primary " + (view === "regions" ? "active" : "")}
              onClick={() => {
                localStorage.setItem("parentPlanType", "Regional");
                setView("regions");
                dispatch(settingObjectData({ mainField: "simlyData", field: "isPackageItem", value: false }));
              }}
            >
              Per Region
            </button>
          </div>
          <div className="btnCon d-flex justify-content-center">
            <button
              className={"btn btn-primary " + (view === "global" ? "active" : "")}
              onClick={() => {
                localStorage.setItem("parentPlanType", "Global");
                setView("global");
                dispatch(settingObjectData({ mainField: "simlyData", field: "isPackageItem", value: false }));
              }}
            >
              Global
            </button>
          </div>
        </div>
      </div>
      {simlyData.isPackageItem && simlyData?.SelectedCountry ? (
        <PackageItems country={simlyData?.SelectedCountry} />
      ) : (
        <>
          {
            <>
              {view === "regions" && (
                <div className="row" style={{ margin: "0 10px", width: "100%" }}>
                  <div className="col">
                    {simlyData?.AvailableCountries &&
                      simlyData?.AvailableCountries.regional &&
                      simlyData?.AvailableCountries.regional.map((region, index) => (
                        <div key={index} className="card mb-3" onClick={() => handleClick(region.isoCode)}>
                          <div className="card-body">
                            <div id="Topp">
                              <img src={region.countryImageURL} alt={region.name} width={50} />
                              <div className="noTopp">
                                <div className="card-title">{region.name} Packages</div>
                                <p className="card-text">{region.destinations} destinations</p>
                              </div>
                            </div>
                          </div>
                        </div>
                      ))}
                  </div>
                </div>
              )}

              {view === "global" && (
                <div className="row" style={{ margin: "0 10px", width: "100%" }}>
                  <div className="col">
                    {simlyData?.AvailableCountries &&
                      simlyData?.AvailableCountries.global &&
                      simlyData?.AvailableCountries.global.map((globalItem, index) => (
                        <div key={index} className="card mb-3" onClick={() => handleClick(globalItem.isoCode)}>
                          <div className="card-body">
                            <div id="Topp">
                              <img src={globalItem.countryImageURL} alt={globalItem.name} width={50} />
                              <div className="noTopp">
                                <div className="card-title">{globalItem.name} Packages</div>
                                <p className="card-text">{globalItem.destinations} destinations</p>
                              </div>
                            </div>
                          </div>
                        </div>
                      ))}
                  </div>
                </div>
              )}
            </>
          }
          {view === "countries" && (
            <>
              {isLoadingData ? (
                <div className="row ps-3" style={{ width: "100%", overflow: "hidden" }}>
                  <ContentLoader speed={2} width="100%" height="90vh" backgroundColor="#f3f3f3" foregroundColor="#ecebeb">
                    <rect x="0" y="0" rx="3" ry="3" width="100%" height="80" />
                    <rect x="0" y="90" rx="3" ry="3" width="100%" height="80" />
                    <rect x="0" y="180" rx="3" ry="3" width="100%" height="80" />
                    <rect x="0" y="270" rx="3" ry="3" width="100%" height="80" />
                    <rect x="0" y="360" rx="3" ry="3" width="100%" height="80" />
                    <rect x="0" y="450" rx="3" ry="3" width="100%" height="80" />
                  </ContentLoader>
                </div>
              ) : (
                <div style={{ width: "100%", overflow: "hidden" }}>
                  <div className="row ps-3">
                    <div className="col">
                      <div className="card-columns continent-card-container">
                        {filteredData?.map((continentObj, index) => (
                          <div key={index} className="continent-container">
                            {Object.keys(continentObj)?.map((continent) => (
                              <React.Fragment key={continent}>
                                <div className="title">{displaycontinent(continent)}</div>
                                <div className="country-scroll-container">
                                  <div className="row flex-nowrap">
                                    {continentObj[continent].map((country, idx) => (
                                      <div className="imgText">
                                        <div key={idx} className="">
                                          <div className="card countryCard" onClick={() => handleClick(country.isoCode)}>
                                            <div className="card-body">
                                              <img src={country.countryImageURL} alt={country.name} width={50} />
                                            </div>
                                          </div>
                                        </div>
                                        <h5 className="card-title">{country.name}</h5>
                                      </div>
                                    ))}
                                  </div>
                                </div>
                              </React.Fragment>
                            ))}
                          </div>
                        ))}
                      </div>
                    </div>
                  </div>
                </div>
              )}
            </>
          )}
        </>
      )}
    </>
  );
};

export default Packages;
