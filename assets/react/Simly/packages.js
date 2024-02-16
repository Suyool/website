import React, {useState, useEffect} from "react";
import axios from "axios";
import ContentLoader from "react-content-loader";
import PackageItems from "./PackageItems";

const Packages = () => {
    const [view, setView] = useState('countries');
    const [selectedData, setSelectedData] = useState([]);
    const [isLoading, setIsLoading] = useState(false);
    const [isPackageItem, setIsPackageItem] = useState(false);
    const [items, setItems] = useState([]);
    const [searchQuery, setSearchQuery] = useState('');
    const [filteredData, setFilteredData] = useState([]);
    const [selectedCountry, setSelectedCountry] = useState(null);

    const handleViewChange = (selectedView) => {
        setView(selectedView);
    };
    useEffect(() => {
        setIsLoading(true);
        // Fetch data using Axios
        axios.get('http://10.20.80.79/simly/getAllAvailableCountries')
            .then(response => {
                // Set the fetched data to state
                setSelectedData(response.data.message);
                setIsLoading(false);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                setIsLoading(false);
            });
    }, []);

    // Function to filter data based on search query
    const filterData = (data, query) => {
        const filtered = Object.keys(data.local).reduce((acc, continent) => {
            const countries = data.local[continent].filter(country =>
                country.name.toLowerCase().includes(query.toLowerCase())
            );
            if (countries.length > 0) {
                acc[continent] = countries;
            }
            return acc;
        }, {});
        return filtered;
    };

    useEffect(() => {
        // Filter data based on search query
        if (searchQuery.length >= 3) {
            const filtered = filterData(selectedData, searchQuery);
            setFilteredData(filtered);
        } else {
            // Display all countries when search query is empty or less than 3 characters
            setFilteredData(selectedData.local || {});
        }
    }, [searchQuery, selectedData]);

    const handleSearchChange = (event) => {
        const query = event.target.value;
        setSearchQuery(query);
    };

    const handleClick = (isoCode) => {
        // Call the API to fetch items using the ISO code
        setIsLoading(true);
        axios.get(`http://10.20.80.74/simly/getPlansUsingISOCode?code=${isoCode}`)
            .then(response => {
                setItems(response.data); // Update the state with fetched items
                setSelectedCountry(response.data.message);
                setIsPackageItem(true)

            })
            .catch(error => {
                console.error('Error fetching items:', error);
            })
            .finally(() => {
                setIsLoading(false);
            });
    };

    return (
        <div className="container">
            <div className="search-bar mt-5">
                <div className="search-icon-left">
                    <img src="/build/images/g2g/search.svg" alt="Search Icon"/>
                </div>
                <input
                    type="text"
                    placeholder="Search Destination"
                    value={searchQuery}
                    onChange={handleSearchChange}
                    style={{fontWeight: 'bold', color: '#000000', fontFamily: 'PoppinsRegular'}}
                />
                <div className="search-icon-right">
                    <img src="/build/images/topUpSimIcon.svg" alt="Icon"/>
                </div>
            </div>
            <div className="row filter-btns-cont">
                <div className="col d-flex justify-content-center">
                    <button
                        className={"btn btn-primary " + (view === 'countries' ? "active" : "")}
                        onClick={() => setView('countries')}
                    >
                        Per Country
                    </button>
                </div>
                <div className="col d-flex justify-content-center">
                    <button
                        className={"btn btn-primary " + (view === 'regions' ? "active" : "")}
                        onClick={() => setView('regions')}
                    >
                        Per Region
                    </button>
                </div>
                <div className="col d-flex justify-content-center">
                    <button
                        className={"btn btn-primary " + (view === 'global' ? "active" : "")}
                        onClick={() => setView('global')}
                    >
                        Global
                    </button>
                </div>
            </div>
            {isPackageItem ? (
                // Render content when isPackageItem is true
                <PackageItems country={selectedCountry}/>
            ) : (
                // Render content when isPackageItem is false
                <>
                    {view === 'countries' && (
                        <>
                            {isLoading ? (
                                <ContentLoader speed={2} width="100%" height="90vh" backgroundColor="#f3f3f3"
                                               foregroundColor="#ecebeb">
                                    <rect x="0" y="0" rx="3" ry="3" width="100%" height="80"/>
                                    <rect x="0" y="90" rx="3" ry="3" width="100%" height="80"/>
                                    <rect x="0" y="180" rx="3" ry="3" width="100%" height="80"/>
                                    <rect x="0" y="270" rx="3" ry="3" width="100%" height="80"/>
                                    <rect x="0" y="360" rx="3" ry="3" width="100%" height="80"/>
                                    <rect x="0" y="450" rx="3" ry="3" width="100%" height="80"/>
                                </ContentLoader>
                            ) : (
                                <div className="row ps-3">
                                    <div className="col">
                                        <div className="card-columns continent-card-container">
                                            {Object.keys(filteredData).map((continent, index) => (
                                                <div key={index} className="continent-container">
                                                    {filteredData[continent].length > 0 && (
                                                        <>
                                                            <h5>{continent}</h5>
                                                            <div className="country-scroll-container">
                                                                <div className="row flex-nowrap">
                                                                    {filteredData[continent].map((country, idx) => (
                                                                        <div key={idx} className="col">
                                                                            <div className="card countryCard mb-3"
                                                                                 onClick={() => handleClick(country.isoCode)}>
                                                                                <div className="card-body">
                                                                                    <img src={country.countryImageURL}
                                                                                         alt={country.name} width={50}/>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    ))}
                                                                </div>
                                                            </div>
                                                        </>
                                                    )}
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            )}
                        </>
                    )}

                    {view === 'regions' && (
                        <div className="row">
                            <div className="col">
                                {selectedData && selectedData.regional && selectedData.regional.map((region, index) => (
                                    <div key={index} className="card mb-3" onClick={() => handleClick(region.isoCode)}>

                                        <div className="card-body">
                                            <img src={region.countryImageURL} alt={region.name} width={50}/>
                                            <h5 className="card-title mt-2">{region.name} Packages</h5>
                                            <p className="card-text">
                                                {region.destinations} destinations
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {view === 'global' && (
                        <div className="row">
                            <div className="col">
                                {selectedData && selectedData.global && selectedData.global.map((globalItem, index) => (
                                    <div key={index} className="card mb-3" onClick={() => handleClick(globalItem.isoCode)}>
                                        <div className="card-body">
                                            <img src={globalItem.countryImageURL} alt={globalItem.name} width={50}/>
                                            <h5 className="card-title">{globalItem.name} Package</h5>
                                            <p className="card-text">
                                                {globalItem.destinations} destinations
                                            </p>
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
