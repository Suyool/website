import React, { useEffect, useState } from "react";
import ContentLoader from "react-content-loader";
import {capitalizeFirstLetters} from "../../../functions";
import {useDispatch, useSelector} from "react-redux";
import {settingData} from "../Redux/Slices/AppSlice";

const ReCharge = () => {
  const [ filteredData, setFilteredData ] = useState([]);
  const [ getLoading, setLoading ] = useState(true);

  const {sodetelData, bundle} = useSelector((state) => state.appData);
  const dispatch = useDispatch();

  useEffect(() => {
    dispatch(settingData({ field: "headerData", value: { title: `Re-charge ${capitalizeFirstLetters(bundle)} Package`, backLink: "BundleCredentials", currentPage: "ReCharge" } }));
    setFilteredData(Object.values(sodetelData));

    console.log("sodetelData", sodetelData);

    if (sodetelData) {
      const dataObj = JSON.parse(sodetelData);
      dispatch(settingData({ field: "identifier", value: dataObj?.customerid }));
    }
    const values = sodetelData? Object.values(JSON.parse(sodetelData))?.filter(item => typeof item !== 'string') : [];
    setFilteredData(values);

  }, [ sodetelData ]);

  useEffect(() => {
    if (filteredData.length > 0) {
      setLoading(false);
    }
  }, [ filteredData ]);

  return (
    <div id="ReCharge">
      <div className="bundlesSection">
        <div className="mainTitle">Available ${capitalizeFirstLetters(bundle)} Re-charge Packages</div>
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
                onClick={() => {
                  dispatch(settingData({ field: "headerData", value: { title: `Re-charge ${capitalizeFirstLetters(bundle)} Package`, backLink: "ReCharge", currentPage: "Refill" } }));
                  dispatch(settingData({ field: "planData", value: record }));
                }}
              >
                <img
                  className="GridImg"
                  src={record?.plancode ? `/build/images/sodetel/${record.plancode}-cir.svg` : "/build/images/sodetel/sodetel-bundle.png"}
                  alt="bundle"
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
