import React, { useEffect, useState } from "react";
import ContentLoader from "react-content-loader";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const ReCharge = () => {
  const dispatch = useDispatch();
  const [filteredData, setFilteredData] = useState([]);
  const [getLoading, setLoading] = useState(true);
  const getVoucherData = useSelector((state) => state.appData.prepaidData.vouchers);

  useEffect(() => {
    dispatch(settingData({ field: "headerData", value: { title: "Recharge Touch", backLink: "", currentPage: "ReCharge" } }));
    if (getVoucherData !== null) {
      setFilteredData(Object.values(getVoucherData));
    }
  }, [getVoucherData]);

  useEffect(() => {
    if (filteredData.length > 0) {
      setLoading(false);
    }
  }, [filteredData]);

  return (
    <div id="ReCharge">
      <div className="bundlesSection">
        <div className="mainTitle">Available Re-charge Packages</div>
        <div className="mainDesc">* Excluding Taxes</div>
        {getLoading ? (
          <ContentLoader speed={2} width="100%" height="90vh" backgroundColor="#f3f3f3" foregroundColor="#ecebeb">
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
              <button
                // className="bundleGrid"
                className={`${record.isinstock == 0 ? "bundleGrid outofstock" : "bundleGrid"}`}
                key={index}
                disabled={record.isinstock == 0}
                // style={record.isinstock == 0 ? { display: "none" } : { display: "flex" }}
                onClick={() => {
                  dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "MyBundle" }));
                  dispatch(
                    settingObjectData({
                      mainField: "prepaidData",
                      field: "prepaidVoucher",
                      value: {
                        vouchercategory: record.vouchercategory,
                        vouchertype: record.vouchertype,
                        priceLBP: record.priceLBP,
                        priceUSD: record.priceUSD,
                        desc: record.desc,
                        isavailable: record.isavailable,
                        desc1: record.desc1,
                        desc2: record.desc2,
                        beforeTaxes: record.beforeTaxes,
                        fees: record.fees,
                        sayrafa: record.sayrafa,
                      },
                    })
                  );
                }}
              >
                <img className="GridImg" src={`/build/images/touch/bundleImg${record.vouchertype}h.png`} alt="bundleImg" style={{ opacity: record.isinstock == 0 ? 0.5 : 1 }} />
                <div className="gridDesc">
                  <div className="Price">
                    <div style={{ opacity: record.isinstock == 0 ? 0.5 : 1 }}>${record.beforeTaxes}</div>
                    {record.isinstock == 0 ? <span className="outstock">Out of Stock</span> : ""}
                  </div>
                  <div className="bundleName" style={{ opacity: record.isinstock == 0 ? 0.5 : 1 }}>
                    {record.desc3}
                  </div>
                </div>
              </button>
            ))}
          </>
        )}
      </div>
    </div>
  );
};

export default ReCharge;
