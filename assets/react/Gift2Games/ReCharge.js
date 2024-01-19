import React, {useEffect, useState} from "react";
import ContentLoader from "react-content-loader";
import axios from "axios";

const ReCharge = ({
                      parameters,
                      getVoucherData,
                      setActiveButton,
                      setHeaderTitle,
                      setBackLink,
                      activeButton,
                      setPrepaidVoucher
                  }) => {
    const [filteredData, setFilteredData] = useState([]);
    const [getLoading, setLoading] = useState(true);

    const fetchProducts = () => {
        axios
            .get(`/gift2games/products/${activeButton.category}`)
            .then((response) => {
                console.log("response", response)
                if (response?.data?.status) {
                    const productData = JSON.parse(response?.data?.Payload)?.data;
                    setFilteredData(productData);
                }
                setLoading(false)
            })
            .catch((error) => {
                console.log(error);
            });
    }

    useEffect(() => {
        setHeaderTitle();
        setBackLink("");
        fetchProducts();
    }, [getVoucherData]);


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
                                    setPrepaidVoucher({
                                        price: record.price,
                                        currency: record.currency,
                                        title: record.title,
                                        image: record.image,
                                        productId: record.id
                                    });
                                    setActiveButton({name: "MyBundle"});
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
