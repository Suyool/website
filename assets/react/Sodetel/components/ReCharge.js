import React, { useEffect, useState } from "react";
import ContentLoader from "react-content-loader";
import {capitalizeFirstLetters} from "../../functions";

const ReCharge = ({
  parameters,
  setPrepaidVoucher,
  getVoucherData, activeButton,
  setActiveButton,
  setHeaderTitle,
  setBackLink,
}) => {
  console.log("getVoucherData", getVoucherData)
  const [ filteredData, setFilteredData ] = useState([]);
  const [ getLoading, setLoading ] = useState(true);

  useEffect(() => {
    setHeaderTitle(`Re-charge ${capitalizeFirstLetters(activeButton?.bundle)} Package`);
    setBackLink("BundleCredentials");
    setFilteredData(Object.values(getVoucherData));
    // console.log("values",Object.values(getVoucherData));
    // console.log("voucherData", getVoucherData);

    const values = getVoucherData?.id? Object.values(getVoucherData.id)?.filter(item => typeof item !== 'string') : [];
    setFilteredData(values);

  }, [ getVoucherData ]);

  useEffect(() => {
    if (filteredData.length > 0) {
      setLoading(false);
    }
  }, [ filteredData ]);
  // console.log(filteredData);

  return (
    <div id="ReCharge">
      <div className="bundlesSection">
        <div className="mainTitle">Available Re-charge Packages</div>
        <div className="mainDesc">* Excluding Taxes</div>
        {getLoading ? (
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
        ) : (
          <>
            {filteredData.map((record, index) => (
              <div
                className="bundleGrid"
                key={index}
                // style={
                //   record.isinstock == 0
                //     ? { display: "none" }
                //     : { display: "flex" }
                // }
                onClick={() => {
                  setActiveButton({ ...activeButton, name: "MyBundle" });
                  setPrepaidVoucher(record);
                }}
              >
                <img
                  className="GridImg"
                  src={`/build/images/sodetel/sodetel-bundle.png`}
                  alt="bundleImg"
                />
                <div className="gridDesc">
                  <div className="Price">
                    L.L {parseInt(record?.price).toLocaleString()}
                  </div>
                  <div className="bundleName">{record?.plandescription}</div>
                </div>
              </div>
            ))}
          </>
        )}
      </div>
    </div>
  );
};

export default ReCharge;
