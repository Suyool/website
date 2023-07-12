import React, { useEffect, useState } from "react";

const ReCharge = ({ getVoucherData, setActiveButton, setHeaderTitle, setBackLink }) => {
  const [searchValue, setSearchValue] = useState("");
  const [filteredData, setFilteredData] = useState([]);

  console.log(filteredData);

  useEffect(() => {
    setHeaderTitle("Re-charge Alfa");
    setBackLink("");
    setFilteredData(Object.values(getVoucherData));
  }, [getVoucherData]);

  const handleSearchChange = (event) => {
    const value = event.target.value;
    setSearchValue(value);
    filterData(value);
  };

  const filterData = (value) => {
    const filtered = Object.values(getVoucherData).filter((record) =>
      record.desc.toLowerCase().includes(value.toLowerCase())
    );
    setFilteredData(filtered);
  };

  return (
    <div id="ReCharge">
      <div className="mainTitle">Available Re-charge Packages</div>
      <div className="mainDesc">*All taxes excluded</div>

      <div className="searchSection">
        <input
          className="searchInput"
          placeholder="Search Package"
          value={searchValue}
          onChange={handleSearchChange}
        />
        <img className="searchImg" src="/build/images/Alfa/alfaLogo.png" alt="flag" />
      </div>

      <div className="bundlesSection">
        {filteredData.map((record, index) => (
          <div className="bundleGrid" key={index} onClick={() => setActiveButton({ name: "MyBundle" })}>
            <img className="GridImg" src="/build/images/Alfa/bundleImg1.png" alt="bundleImg" />
            <div className="gridDesc">
              <div className="Price">{record.priceLBP} LBP</div>
              <div className="bundleName">{record.desc}</div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default ReCharge;
