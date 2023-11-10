import React from "react";

const ProductList = ({ products, setActiveButton, setSelectedProduct }) => {

    const onProductSelect = (product) => {
        setActiveButton({name: "SelectedProductInfo"});
        setSelectedProduct(product);
    }

    return (
        <div className="bundlesSection">
            <div className="mainTitle">Available Re-charge Packages</div>
            <div className="mainDesc">* Excluding Taxes</div>
            <div className="bundlesSection">
                {products.map((product, index) => (
                    <div
                        className="bundleGrid"
                        key={index}
                        onClick={() => {
                            onProductSelect(product);
                        }}
                    >
                        <img
                            className="GridImg"
                            src="/build/images/terraNet/terraNetLogo.png"
                            alt="bundleImg"
                        />
                        <div className="gridDesc">
                            <div className="Price">
                               LBP {parseInt(product.Price).toLocaleString()}
                            </div>
                            <div className="bundleName">{product.Description}</div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default ProductList;