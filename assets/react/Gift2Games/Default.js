import React, {useState, useEffect} from "react";
import axios from "axios";
import ContentLoader from "react-content-loader";

const Default = ({ setActiveButton, setPrepaidVoucher, setTypeID }) => {
    const [loading, setLoading] = useState(true);
    const [filteredData, setFilteredData] = useState([]);
    const [categoriesWithNumberIds, setCategoriesWithNumberIds] = useState([]);
    const [childCategories, setChildCategories] = useState([]);
    const [categories, setCategories] = useState([]);
    const [activeCategoryId, setActiveCategoryId] = useState();
    const [activeSubCategoryId, setActiveSubCategoryId] = useState(null);

    const handleSearch = (e) => {
        const searchValue = e.target.value;
        const filteredData = categories.filter((category) => {
            return category.title.toLowerCase().includes(searchValue.toLowerCase());
        });

        setCategoriesWithNumberIds(filteredData);
    };

    const fetchCategories = () => {
        axios
            .get(`/gift2games/categories/${setTypeID}`)
            .then((response) => {
                // console.log(response);
                if (response?.data?.status) {
                    const parsedData = response?.data?.Payload;
                    setCategories(parsedData);
                }
            })
            .catch((error) => {
                console.error("Error fetching categories:", error);
            });
    };

    const handleCategoryClick = (categoryId, id) => {
        setActiveCategoryId(id);
        fetchChildCategories(id);
    };

    const fetchChildCategories = (parentId) => {
        axios
            .get(`/gift2games/categories/${parentId}/childs`)
            .then((response) => {
                if (response?.data?.status) {
                    const childCategories = response?.data?.Payload;
                    setChildCategories(childCategories);
                }
            })
            .catch((error) => {
                console.log(error);
            });
    };

    const fetchProducts = () => {
        setLoading(true);
        if(activeSubCategoryId !=0){
            axios
                .get(`/gift2games/products/${activeSubCategoryId}`)
                .then((response) => {
                    if (response?.data?.status) {
                        const productData = response?.data?.Payload;
                        setFilteredData(productData);
                    }
                    setLoading(false);
                })
                .catch((error) => {
                    console.log(error);
                });
        }

    };

    useEffect(() => {
        fetchCategories();
    }, [setTypeID]);

    useEffect(() => {
        setCategoriesWithNumberIds(
            categories.map((category) => ({
                ...category,
                id: Number(category.id),
            }))
        );
    }, [categories]);

    useEffect(() => {
        // Select the first category when the component mounts
        if (categoriesWithNumberIds.length > 0) {
            const firstCategory = categoriesWithNumberIds[0];
            setActiveCategoryId(firstCategory.id);
            fetchChildCategories(firstCategory.id);
        }
    }, [categoriesWithNumberIds]);

    useEffect(() => {
        // Fetch products for the first child category when the component mounts
        if (childCategories.length > 0) {
            const firstChildCategory = childCategories[0];
            setActiveSubCategoryId(firstChildCategory.categoryId);
        }
    }, [childCategories]);

    useEffect(() => {

        if (activeSubCategoryId) {
            fetchProducts();
        }
    }, [activeSubCategoryId]);

    return (
        <div id="Default_g2g">
            <div className="search-bar">
                <div className="search-icon">
                    <img src="/build/images/g2g/search.svg" alt=""/>
                </div>
                <input type="text" placeholder="Search in gaming e-store" onChange={(event) => handleSearch(event)}/>
            </div>

            <div className="categories-scroll">
                {
                    categoriesWithNumberIds.map((category) => {
                        return (
                            <div
                                key={category.categoryId}
                                className={`category-item ${activeCategoryId === Number(category.id) ? "selected" : ""}`}
                                onClick={() => {
                                    handleCategoryClick(Number(category.categoryId),category.id)
                                    sessionStorage.setItem("categoryName", category.title)
                                }}
                            >
                                <img src={category.image} alt={category.title}/>
                                <p className="SubTitleCat">{category.title}</p>

                            </div>
                        );
                    })
                }
            </div>

            {/* Display child categories for the active category */}

            <div className="child-categories">
                {childCategories.map((child) => {
                    return (
                        <button
                            key={child.id}
                            className={`child-category ${
                                child.categoryId === activeSubCategoryId ? "active-sub" : ""
                            }`}
                            onClick={() => {
                                setActiveSubCategoryId(child.categoryId);
                            }}
                        >
                            <p className="SubTitleCat">{child.shortTitle}</p>
                        </button>
                    );
                })}
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
                                        record.instock == 0
                                            ? {display: "none"}
                                            : {display: "flex"}
                                    }
                                    onClick={() => {
                                        setPrepaidVoucher({
                                            price: record.sellPrice,
                                            displayPrice: record.displayPrice,
                                            currency: record.currency,
                                            title: record.title,
                                            image: record.image,
                                            productId: record.productId
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
                                            ${record?.displayPrice}{" "}
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
