import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";
import SearchBar from "../Component/SearchBar";

const Offers = ({ country }) => {
  const dispatch = useDispatch();
  const [searchQuery, setSearchQuery] = useState("");
  const offre = useSelector((state) => state.appData.offre);
  const [getOffre,setOffre] = useState([]);

  console.log(offre);
  useEffect(()=>{
    setOffre(offre);
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
},[]);
  const handleSearchChange = (event) => {
    const query = event.target.value;
    setSearchQuery(query);
  };

  const handleCardClick = (plan, packages) => {
    dispatch(
      settingObjectData({
        mainField: "headerData",
        field: "currentPage",
        value: "PackagesInfo",
      })
    );
    dispatch(
      settingObjectData({
        mainField: "simlyData",
        field: "SelectedPlan",
        value: plan,
      })
    );
    dispatch(
      settingObjectData({
        mainField: "simlyData",
        field: "SelectedPackage",
        value: packages,
      })
    );
  };

  return (
    <div className="container itemsPackageCont">
      <SearchBar
        searchQuery={searchQuery}
        handleSearchChange={handleSearchChange}
        dispatch={dispatch}
      />
      <div className="title">Special Offers</div>
      {getOffre?.map((offreItem, index) => {
        <>
        hi
        </>
      })}
    </div>
  );
};

export default Offers;
