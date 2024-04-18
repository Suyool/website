import React, { useEffect, useState } from "react";
import ContentLoader from "react-content-loader";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const ReCharge = () => {
  const dispatch = useDispatch();
  const [filteredData, setFilteredData] = useState([]);
  const [getLoading, setLoading] = useState(true);
  const getVoucherData = useSelector((state) => state.appData.products);
  const headerTitle = useSelector((state) => state.appData.headerTitle);
  const typeID = useSelector((state) => state.appData.typeID);

  useEffect(() => {
    if( typeID == 7) {
      dispatch(settingData({ field: "headerData", value: { title: "WISE", backLink: "ReCharge", currentPage: "ReCharge" } }));
      dispatch(settingData({field: "productInfo", value: {}}))
    }else {
      dispatch(settingData({field: "headerData", value: {title: headerTitle, backLink: "", currentPage: "ReCharge"}}));
    }
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
        <div className="mainTitle">Available Vouchers</div>
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
                className={`${record.inStock == 0 ? "bundleGrid outofstock" : "bundleGrid"}`}
                key={index}
                disabled={record.inStock == 0}
                onClick={() => {
                  dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "MyBundle" }));
                  dispatch(
                      settingData({
                        field: "productInfo",
                        value: {
                          price: record.displayPrice,
                          displayPrice: record.displayPrice,
                          currency: record.currency,
                          title: record.title,
                          image: record.image,
                          productId: record.productId
                        },
                      })
                  );
                }}
              >
                <img className="GridImg" src={record?.image} alt="bundleImg" style={{ opacity: record.inStock == 0 ? 0.5 : 1 }} />
                <div className="gridDesc">
                  <div className="Price">
                    {record.currency === "LBP" ? (
                        <div style={{ opacity: record.isinstock === 0 ? 0.5 : 1 }}>
                          L.L {parseInt(record.price).toLocaleString()}
                        </div>
                    ) : (
                        <div style={{ opacity: record.isinstock === 0 ? 0.5 : 1 }}>
                          ${record.price}
                        </div>
                    )}
                    {record.inStock == 0 ? <span className="outstock">Out of Stock</span> : ""}
                  </div>
                  <div className="bundleName" style={{ opacity: record.inStock == 0 ? 0.5 : 1 }}>
                    {record.title}
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
