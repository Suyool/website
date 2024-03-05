import React, { useEffect, useState } from "react";
import {useDispatch, useSelector} from "react-redux";
import { settingData, settingObjectData } from "../../TerraNet/Redux/Slices/AppSlice";
import ContentLoader from "react-content-loader";

const ProductList = () => {
    const dispatch = useDispatch();
    const [filteredData, setFilteredData] = useState([]);
    const [getLoading, setLoading] = useState(true);
    const products = useSelector((state) => state.appData.landlineForm);


    useEffect(() => {
        dispatch(
            settingData({
                field: "headerData",
                value: {
                    title: "Products",
                    backLink: "",
                    currentPage: "ReCharge",
                },
            })
        );

        // Set filteredData directly to products array
        setFilteredData(products);
    }, [dispatch]);

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
                                className="bundleGrid"
                                key={index}
                                onClick={() => {
                                    dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "SelectedProductInfo" }));
                                    dispatch(
                                        settingData({
                                            field: "productInfo",
                                            value: {
                                                productId: record.productId,
                                                description: record.description,
                                                originalHT: record.originalHT,
                                                imagePath: record.imagePath,
                                                price: record.price,

                                            },
                                        })
                                    );
                                }}
                            >
                                <img className="GridImg" src={`/build/images/terraNet/circle_product_${record.productId}.png`} alt="bundleImg" />
                                <div className="gridDesc">
                                    <div className="Price">
                                        <div>
                                        L.L {parseInt(record.originalHT).toLocaleString()}</div>
                                    </div>
                                    <div className="bundleName">
                                        {record.description}
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

export default ProductList;
