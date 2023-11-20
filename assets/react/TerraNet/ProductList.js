import React, {useEffect} from "react";

const ProductList = ({ products, setActiveButton, setSelectedProduct,setHeaderTitle,setBackLink }) => {

    const onProductSelect = (product) => {
        setActiveButton({name: "SelectedProductInfo"});
        setSelectedProduct(product);
    }
    useEffect(() => {
        setHeaderTitle("Re-charge TerraNet");
        setBackLink("");
    }, []);
    return (
        <div className="bundlesSection">
            <div className="mainTitle">Available Re-charge Packages</div>
            <div className="mainDesc">* Excluding Taxes</div>
            <div className="bundlesSection mb-5">
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
                               L.L {parseInt(product.Price).toLocaleString()}
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