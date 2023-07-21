// import React, { useEffect, useState } from "react";

// const ReCharge = ({ getVoucherData, setActiveButton, setHeaderTitle, setBackLink }) => {
//   const [searchValue, setSearchValue] = useState("");
//   const [filteredData, setFilteredData] = useState([]);

//   console.log(filteredData);

//   useEffect(() => {
//     setHeaderTitle("Re-charge Alfa");
//     setBackLink("");
//     setFilteredData(Object.values(getVoucherData));
//   }, [getVoucherData]);

//   const handleSearchChange = (event) => {
//     const value = event.target.value;
//     setSearchValue(value);
//     filterData(value);
//   };

//   const filterData = (value) => {
//     const filtered = Object.values(getVoucherData).filter((record) =>
//       record.desc.toLowerCase().includes(value.toLowerCase())
//     );
//     setFilteredData(filtered);
//   };

//   return (
//     <div id="ReCharge">
//       <div className="mainTitle">Available Re-charge Packages</div>
//       <div className="mainDesc">*All taxes excluded</div>

//       <div className="searchSection">
//         <input
//           className="searchInput"
//           placeholder="Search Package"
//           value={searchValue}
//           onChange={handleSearchChange}
//         />
//         <img className="searchImg" src="/build/images/Alfa/alfaLogo.png" alt="flag" />
//       </div>

//       <div className="bundlesSection">
//         {filteredData.map((record, index) => (
//           <div className="bundleGrid" key={index} onClick={() => setActiveButton({ name: "MyBundle" })}>
//             <img className="GridImg" src="/build/images/Alfa/bundleImg1.png" alt="bundleImg" />
//             <div className="gridDesc">
//               <div className="Price">{record.priceLBP} LBP</div>
//               <div className="bundleName">{record.desc}</div>
//             </div>
//           </div>
//         ))}
//       </div>
//     </div>
//   );
// };

// export default ReCharge;

import React, { useEffect, useState } from "react";
import ContentLoader from "react-content-loader"

const ReCharge = ({ setPrepaidVoucher, getVoucherData, setActiveButton, setHeaderTitle, setBackLink }) => {
  const [filteredData, setFilteredData] = useState([]);
  const [getLoading, setLoading] = useState(true);
  // console.log(filteredData);

  useEffect(() => {
    setHeaderTitle("Re-charge Alfa");
    setBackLink("");
    setFilteredData(Object.values(getVoucherData));
  }, [getVoucherData]);

  useEffect(() => {
    if (filteredData.length > 0) {
      setLoading(false);
    }
  }, [filteredData]);

  return (
    <div id="ReCharge">
      <div className="mainTitle">Available Re-charge Packages</div>
      <div className="mainDesc">*All taxes excluded</div>

      <div className="bundlesSection">
        {
          getLoading ?
            <ContentLoader
              speed={2}
              width="100%"
              height="90vh"
              backgroundColor="#f3f3f3"
              foregroundColor="#ecebeb"
            >
              <rect x="0" y="0" rx="3" ry="3" width="100%" height="80" />
              <rect x="0" y="90" rx="3" ry="3" width="100%" height="80" />
              <rect x="0" y="180" rx="3" ry="3" width="100%" height="80" />
              <rect x="0" y="270" rx="3" ry="3" width="100%" height="80" />
              <rect x="0" y="360" rx="3" ry="3" width="100%" height="80" />
              <rect x="0" y="450" rx="3" ry="3" width="100%" height="80" />
            </ContentLoader>
            :
            <>
              {filteredData.map((record, index) => (
                <div className="bundleGrid" key={index} onClick={() => { setActiveButton({ name: "MyBundle" }); setPrepaidVoucher({ vouchercategory: record.vouchercategory, vouchertype: record.vouchertype, priceLBP: record.priceLBP, priceUSD: record.priceUSD, desc: record.desc, isavailable: record.isavailable }); }}>
                  <img className="GridImg" src={`/build/images/Alfa/bundleImg${record.vouchertype}.png`} alt="bundleImg" />
                  <div className="gridDesc">
                    <div className="Price">${record.priceUSD} <span>(LBP {parseInt(record.priceLBP).toLocaleString()})</span></div>
                    <div className="bundleName">{record.desc}</div>
                  </div>
                </div>
              ))}
            </>
        }
      </div>
    </div>
  );
};

export default ReCharge;
