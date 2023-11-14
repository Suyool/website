import React, {useEffect, useState} from "react";
import ContentLoader from "react-content-loader";
import axios from "axios";

const ReCharge = ({
                      parameters,
                      setPrepaidVoucher,
                      getVoucherData,
                      setActiveButton,
                      setHeaderTitle,
                      setBackLink,
                      activeButton
                  }) => {
    const [filteredData, setFilteredData] = useState([]);
    const [getLoading, setLoading] = useState(true);

    const fetchProducts = () => {
        axios
            .get(`/gift2games/products/${activeButton.category}`)
            .then((response) => {
                console.log("response", response)
                if (response?.data?.status)
                    setFilteredData(JSON.parse(response?.data?.Payload)?.data);
                setLoading(false)
            })
            .catch((error) => {
                console.log(error);
            });
    }

    useEffect(() => {
        setHeaderTitle("Products");
        setBackLink("");
        fetchProducts();
        setFilteredData(Object.values(getVoucherData));
    }, [getVoucherData]);

    useEffect(() => {
        if (activeButton.name === "Products") {
            fetchProducts();
        }
    }, [activeButton]);

    console.log("filteredData", filteredData)

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
                        <rect x="0" y="0" rx="3" ry="3" width="100%" height="80"/>
                        <rect x="0" y="90" rx="3" ry="3" width="100%" height="80"/>
                        <rect x="0" y="180" rx="3" ry="3" width="100%" height="80"/>
                        <rect x="0" y="270" rx="3" ry="3" width="100%" height="80"/>
                        <rect x="0" y="360" rx="3" ry="3" width="100%" height="80"/>
                        <rect x="0" y="450" rx="3" ry="3" width="100%" height="80"/>
                    </ContentLoader>
                ) : (
                    <>
                        {filteredData.map((record, index) => (
                            <div
                                className="bundleGrid"
                                key={index}
                                style={
                                    record.isinstock == 0
                                        ? {display: "none"}
                                        : {display: "flex"}
                                }
                                onClick={() => {
                                    setActiveButton({name: "MyBundle"});
                                    setPrepaidVoucher({
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
                                        sayrafa: record.sayrafa
                                    });
                                }}
                            >
                                <img
                                    className="GridImg"
                                    src={record?.image}
                                    alt="bundleImg"
                                />
                                <div className="gridDesc">
                                    <div className="Price">
                                        ${record?.sellPrice}{" "}
                                        {/* <span>
                      (LBP {parseInt(record.priceLBP).toLocaleString()})
                    </span> */}
                                    </div>
                                    <div className="bundleName">{record.title}</div>
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
