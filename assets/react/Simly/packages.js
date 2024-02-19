import React, { useState, useEffect } from "react";
import axios from "axios";
import ContentLoader from "react-content-loader";
import PackageItems from "./PackageItems";

const Packages = ({ setSelectedPlan, setActiveButton, setSelectedPackage }) => {
  const [view, setView] = useState("countries");
  const [selectedData, setSelectedData] = useState([]);
  const [selectedDataLocal, setSelectedDataLocal] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  const [isPackageItem, setIsPackageItem] = useState(false);
  const [searchQuery, setSearchQuery] = useState("");
  const [filteredData, setFilteredData] = useState([]);
  const [selectedCountry, setSelectedCountry] = useState(null);

  useEffect(() => {
    setIsLoading(true);
    axios
      .get("/simly/getAllAvailableCountries")
      .then((response) => {
        setSelectedData(response.data.message);
        setIsLoading(false);
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
        setIsLoading(false);
      });
    axios
      .get("/simly/getLocalAvailableCountries")
      .then((response) => {
        setSelectedDataLocal(response.data.message);
        setIsLoading(false);
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
        setIsLoading(false);
      });
  }, []);

  const filterData = (data, query) => {
    const filtered = data
      .map((continentObj) => {
        const continent = Object.keys(continentObj)[0];
        const countries = continentObj[continent].filter((country) => country.name.toLowerCase().includes(query.toLowerCase()));
        if (countries.length > 0) {
          return { [continent]: countries };
        }
        return null;
      })
      .filter(Boolean);
    return filtered;
  };

  useEffect(() => {
    if (searchQuery.length >= 3) {
      const filtered = filterData(selectedDataLocal, searchQuery);
      setFilteredData(filtered);
    } else {
      setFilteredData(selectedDataLocal || {});
    }
  }, [searchQuery, selectedDataLocal]);

  const handleSearchChange = (event) => {
    const query = event.target.value;
    setSearchQuery(query);
  };

  const handleClick = (isoCode) => {
    setIsLoading(true);
    axios
      .get(`/simly/getPlansUsingISOCode?code=${isoCode}`)
      .then((response) => {
        setSelectedCountry(response.data.message);
        setIsPackageItem(true);
      })
      .catch((error) => {
        console.error("Error fetching items:", error);
      })
      .finally(() => {
        setIsLoading(false);
      });
  };

  const displaycontinent = (continent) => {
    switch (continent) {
      case "AF":
        return "Africa";
      case "AS":
        return "Asia";
      case "EU":
        return "Europe";
      case "NA":
        return "North America";
      case "SA":
        return "South America";
      case "OC":
        return "Oceania";
      case "ME":
        return "Middle East";

      default:
        return continent;
    }
  };

  return (
    <div className="container">
      <div className="search-bar mt-5">
        <div className="search-icon-left">
          <img src="/build/images/g2g/search.svg" alt="Search Icon" />
        </div>
        <input type="text" placeholder="Search Destination" value={searchQuery} onChange={handleSearchChange} style={{ fontWeight: "bold", color: "#000000", fontFamily: "PoppinsRegular" }} />
        <div className="search-icon-right">
          <img src="/build/images/topUpSimIcon.svg" alt="Icon" />
        </div>
      </div>
      <div className="row filter-btns-cont">
        <div className="col d-flex justify-content-center">
          <button className={"btn btn-primary " + (view === "countries" ? "active" : "")} onClick={() => setView("countries")}>
            Per Country
          </button>
        </div>
        <div className="col d-flex justify-content-center">
          <button className={"btn btn-primary " + (view === "regions" ? "active" : "")} onClick={() => setView("regions")}>
            Per Region
          </button>
        </div>
        <div className="col d-flex justify-content-center">
          <button className={"btn btn-primary " + (view === "global" ? "active" : "")} onClick={() => setView("global")}>
            Global
          </button>
        </div>
      </div>
      {isPackageItem ? (
        <PackageItems country={selectedCountry} setSelectedPlan={setSelectedPlan} setActiveButton={setActiveButton} setSelectedPackage={setSelectedPackage} />
      ) : (
        <>
          {view === "countries" && (
            <>
              {isLoading ? (
                <ContentLoader speed={2} width="100%" height="90vh" backgroundColor="#f3f3f3" foregroundColor="#ecebeb">
                  <rect x="0" y="0" rx="3" ry="3" width="100%" height="80" />
                  <rect x="0" y="90" rx="3" ry="3" width="100%" height="80" />
                  <rect x="0" y="180" rx="3" ry="3" width="100%" height="80" />
                  <rect x="0" y="270" rx="3" ry="3" width="100%" height="80" />
                  <rect x="0" y="360" rx="3" ry="3" width="100%" height="80" />
                  <rect x="0" y="450" rx="3" ry="3" width="100%" height="80" />
                </ContentLoader>
              ) : (
                <div className="row ps-3">
                  <div className="col">
                    <div className="card-columns continent-card-container">
                      {filteredData.map((continentObj, index) => (
                        <div key={index} className="continent-container">
                          {Object.keys(continentObj).map((continent) => (
                            <React.Fragment key={continent}>
                              <h5>{displaycontinent(continent)}</h5>
                              <div className="country-scroll-container">
                                <div className="row flex-nowrap">
                                  {continentObj[continent].map((country, idx) => (
                                    <div key={idx} className="col">
                                      <div className="card countryCard mb-3" onClick={() => handleClick(country.isoCode)}>
                                        <div className="card-body">
                                          <img src={country.countryImageURL} alt={country.name} width={50} />
                                        </div>
                                      </div>
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
              )}
            </>
          )}

          {view === "regions" && (
            <div className="row">
              <div className="col">
                {selectedData &&
                  selectedData.regional &&
                  selectedData.regional.map((region, index) => (
                    <div key={index} className="card mb-3" onClick={() => handleClick(region.isoCode)}>
                      <div className="card-body">
                        <img src={region.countryImageURL} alt={region.name} width={50} />
                        <h5 className="card-title mt-2">{region.name} Packages</h5>
                        <p className="card-text">{region.destinations} destinations</p>
                      </div>
                    </div>
                  ))}
              </div>
            </div>
          )}

          {view === "global" && (
            <div className="row">
              <div className="col">
                {selectedData &&
                  selectedData.global &&
                  selectedData.global.map((globalItem, index) => (
                    <div key={index} className="card mb-3" onClick={() => handleClick(globalItem.isoCode)}>
                      <div className="card-body">
                        <img src={globalItem.countryImageURL} alt={globalItem.name} width={50} />
                        <h5 className="card-title">{globalItem.name} Package</h5>
                        <p className="card-text">{globalItem.destinations} destinations</p>
                      </div>
                    </div>
                  ))}
              </div>
            </div>
          )}
        </>
      )}
    </div>
  );
};

export default Packages;
