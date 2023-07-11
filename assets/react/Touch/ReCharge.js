import React, { useEffect, useState } from "react";
const dummyData = [
  {
    imageSrc: "/build/images/Touch/bundleImg1.png",
    price: "$1.22",
    bundleName: "ReCharge 1",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg2.png",
    price: "$2.45",
    bundleName: "ReCharge 2",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg3.png",
    price: "$3.68",
    bundleName: "ReCharge 3",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg1.png",
    price: "$4.91",
    bundleName: "ReCharge 4",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg.png",
    price: "$6.14",
    bundleName: "ReCharge 5",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg2.png",
    price: "$7.37",
    bundleName: "ReCharge 6",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg1.png",
    price: "$8.60",
    bundleName: "ReCharge 7",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg1.png",
    price: "$1.22",
    bundleName: "ReCharge 8",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg2.png",
    price: "$2.45",
    bundleName: "ReCharge 9",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg3.png",
    price: "$3.68",
    bundleName: "ReCharge 10",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg1.png",
    price: "$4.91",
    bundleName: "ReCharge 11",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg.png",
    price: "$6.14",
    bundleName: "ReCharge 12",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg2.png",
    price: "$7.37",
    bundleName: "ReCharge 13",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg1.png",
    price: "$8.60",
    bundleName: "ReCharge 14",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg1.png",
    price: "$1.22",
    bundleName: "ReCharge 15",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg2.png",
    price: "$2.45",
    bundleName: "ReCharge 16",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg3.png",
    price: "$3.68",
    bundleName: "ReCharge 17",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg1.png",
    price: "$4.91",
    bundleName: "ReCharge 18",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg.png",
    price: "$6.14",
    bundleName: "ReCharge 19",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg2.png",
    price: "$7.37",
    bundleName: "ReCharge 20",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg1.png",
    price: "$8.60",
    bundleName: "ReCharge 21",
  },
];


const ReCharge = ({ activeButton, setActiveButton, setHeaderTitle, setBackLink }) => {
  const [searchValue, setSearchValue] = useState("");
  const [filteredData, setFilteredData] = useState(dummyData);

  useEffect(() => {
    setHeaderTitle("Re-charge Touch");
    setBackLink("");
  }, []);

  const handleSearchChange = (event) => {
    const value = event.target.value;
    setSearchValue(value);
    filterData(value);
  };

  const filterData = (value) => {
    const filtered = dummyData.filter((record) =>
      record.bundleName.toLowerCase().includes(value.toLowerCase())
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
        <img className="searchImg" src="/build/images/Touch/TouchLogo.png" alt="flag" />
      </div>

      <div className="bundlesSection">
        {filteredData.map((record, index) => (
          <div className="bundleGrid" key={index} onClick={() => { setActiveButton({ name: "MyBundle" }) }}>
            <img className="GridImg" src={record.imageSrc} alt="bundleImg" />
            <div className="gridDesc">
              <div className="Price">{record.price}</div>
              <div className="bundleName">{record.bundleName}</div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default ReCharge;

