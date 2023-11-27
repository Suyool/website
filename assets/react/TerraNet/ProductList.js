import React, { useEffect } from "react";

const ProductList = ({
                         products,
                         setActiveButton,
                         setSelectedProduct,
                         setHeaderTitle,
                         setBackLink,
                     }) => {
    useEffect(() => {
        setHeaderTitle("Re-charge TerraNet");
        setBackLink("");
    }, []);

    const onProductSelect = (product) => {
        setActiveButton({ name: "SelectedProductInfo" });
        setSelectedProduct(product);
    };

    return (
        <div className="bundlesSection">
            <div className="mainTitle">Available Re-charge Packages</div>
            <div className="mainDesc">* Excluding Taxes</div>
            <div className="bundlesSection mb-5">
                {products.map((product, index) => {
                    const imagePath = `/build/images/terraNet/circle_product_${product.ProductId}.svg`;

                    return (
                        <div
                            className="bundleGrid"
                            key={index}
                            onClick={() => {
                                onProductSelect(product);
                            }}
                        >
                            <img
                                className="GridImg"
                                src={imagePath}
                                alt={product.Description}
                            />
                            <div className="gridDesc">
                                <div className="Price">
                                    L.L {parseInt(product.OriginalHT).toLocaleString()}
                                </div>
                                <div className="bundleName">{product.Description}</div>
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
};

export default ProductList;
