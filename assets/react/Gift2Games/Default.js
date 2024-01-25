import React, {useState, useEffect} from "react";
import axios from "axios";
import ContentLoader from "react-content-loader";

const Default = ({categories, desiredChildIdsMap, setActiveButton, setPrepaidVoucher}) => {
    const [loading, setLoading] = useState(true);
    const [filteredData, setFilteredData] = useState([]);
    // Convert category IDs to numbers
    const categoriesWithNumberIds = categories.map((category) => ({
        ...category,
        id: Number(category.id),
    }));
    const categoryIdsToDisplay = Object.keys(desiredChildIdsMap).map(Number);
    const [activeCategoryId, setActiveCategoryId] = useState(categoryIdsToDisplay[0]);
    const [activeSubCategoryId, setActiveSubCategoryId] = useState(
        0
    );

    useEffect(() => {
        setActiveCategoryId(categoryIdsToDisplay[0]);
    }, [desiredChildIdsMap]);

    // console.log("activeCategoryId", activeCategoryId);
    // console.log("activeSubCategoryId", activeSubCategoryId);
    // console.log("categoriesWithNumberIds", categoriesWithNumberIds);

    console.log("desiredChildIdsMap", desiredChildIdsMap);

    useEffect(() => {
        if (activeCategoryId && desiredChildIdsMap)
            setActiveSubCategoryId(
                desiredChildIdsMap[activeCategoryId][0]
            )
    }, [activeCategoryId])


    const handleCategoryClick = (categoryId) => {
        setActiveCategoryId(categoryId);
    };

    const fetchProducts = () => {
        setLoading(true);
        axios.get(`/gift2games/products/${activeSubCategoryId}`)
            .then((response) => {
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
        if (activeSubCategoryId) {
            fetchProducts();
        }
    }, [activeSubCategoryId]);

    console.log("filteredData", filteredData)

    return (
        <div id="Default_g2g">
            <div className="categories-scroll">
                {categoryIdsToDisplay.map((categoryId) => {
                    const categoryToDisplay = categoriesWithNumberIds.find(
                        (category) => category.id === Number(categoryId)
                    );

                    return (
                        categoryToDisplay && (
                            <div
                                key={categoryToDisplay.id}
                                className={`category-item ${activeCategoryId === Number(categoryId) ? "selected" : ""}`}
                                onClick={() => handleCategoryClick(Number(categoryId))}
                            >
                                <img src={categoryToDisplay.image} alt={categoryToDisplay.title}/>
                                <p className="SubTitleCat">{categoryToDisplay.title}</p>
                            </div>
                        )
                    );
                })}
            </div>

            {/* Display child categories for the active category */}
            <div className="child-categories">
                {categoriesWithNumberIds
                    .find((category) => category.id === activeCategoryId)?.childs.map((child) => {
                            if (desiredChildIdsMap[activeCategoryId].includes(child.id))
                                return (
                                    <div key={child.id} className={`child-category ${child.id === activeSubCategoryId? "active-sub" : ""}`} onClick={() => {
                                        setActiveSubCategoryId(child.id)
                                    }}>
                                        <p className="SubTitleCat">{child.short_title}</p>
                                    </div>
                                )
                        }
                    )}
            </div>

            <div id="ReCharge">
                <div className="bundlesSection">
                    <div className="mainTitle">Available Re-charge Packages</div>
                    <div className="mainDesc">* Excluding Taxes</div>
                    {loading ? (
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
                                        </div>
                                        <div className="bundleName">{record.title}</div>
                                    </div>
                                </div>
                            ))}
                        </>
                    )}
                </div>
            </div>
        </div>
    );
};

export default Default;
