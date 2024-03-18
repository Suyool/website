import React, {useEffect, useState} from "react";
import {useDispatch, useSelector} from "react-redux";
import {settingData, settingObjectData} from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";
import SearchBar from "../Component/SearchBar";

const Offers = ({country}) => {
    const dispatch = useDispatch();
    const [searchQuery, setSearchQuery] = useState("");
    const offre = useSelector((state) => state.appData.offre);
    const [filteredOffers, setFilteredOffers] = useState(offre); // Initialize with all offers

    useEffect(() => {
        dispatch(
            settingData({
                field: "headerData",
                value: {
                    title: "Global eSIM",
                    backLink: "Packages",
                    currentPage: "Offers",
                },
            })
        );
    }, []);
    const handleSearchChange = (event) => {
        const query = event.target.value.toLowerCase();
        setSearchQuery(query);

        const filteredOffers = offre.filter((item) =>
            item.country.toLowerCase().includes(query)
        );
        setFilteredOffers(filteredOffers);
    };

    const handleCardClick = (plan, packages) => {
        dispatch(settingObjectData({mainField: "headerData", field: "currentPage", value: "PackagesInfo"}));
        dispatch(settingObjectData({mainField: "simlyData", field: "SelectedPlan", value: packages}));
        dispatch(settingObjectData({mainField: "simlyData", field: "SelectedPackage", value: packages}));
    };

    return (
        <div className="container offersCont itemsPackageCont">
            <SearchBar
                searchQuery={searchQuery}
                handleSearchChange={handleSearchChange}
                dispatch={dispatch}
            />
            <div className="title">Special Offers</div>
            {filteredOffers?.map((item, index) => (
                <div key={index} className="offerItem">
                    {!item?.bought && (
                        <div className="card mb-3 bg-package1" onClick={() => handleCardClick(filteredOffers, item)}>
                            <div className="card-body">
                                <div style={{display: "flex", justifyContent: "space-between", alignItems: "center"}}>
                                    <div className="itemsList">
                                        <h6 className="card-title">
                                            <img src={item.countryImageURL} alt={item.name} width={17}/>
                                            <span className="ms-2" style={{fontFamily: "PoppinsMedium"}}>{item?.name}</span>
                                        </h6>
                                        <p className="card-text itemSize">{item?.size} GB</p>
                                        <p className="card-text desc">Valid for {item?.duration}</p>
                                    </div>
                                    <div>
                                        <p className="card-text price">{item?.initial_price_free}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            ))}
        </div>
    );

};

export default Offers;
