import React, {useState, useEffect} from "react";
import axios from "axios";
import ContentLoader from "react-content-loader";

const Default = ({categories, setActiveButton, setPrepaidVoucher}) => {
    const [loading, setLoading] = useState(true);
    const [filteredData, setFilteredData] = useState([]);
    // Convert category IDs to numbers
    const [categoriesWithNumberIds, setCategoriesWithNumberIds] = useState([]);
    const [childCategories, setChildCategories] = useState([]);

    const [activeCategoryId, setActiveCategoryId] = useState();
    const [activeSubCategoryId, setActiveSubCategoryId] = useState(
        0
    );

    useEffect(() => {
        setCategoriesWithNumberIds(
            categories.map((category) => ({
                ...category,
                id: Number(category.id),
            }))
        );
    }, [categories]);



    const handleCategoryClick = (categoryId,id) => {
        setActiveCategoryId(categoryId);
        fetchChildCategories(id);
    };

    const fetchChildCategories = (parentId) => {
        axios.get(`/gift2games/categories/${parentId}/childs`)
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
        axios.get(`/gift2games/products/${activeSubCategoryId}`)
            .then((response) => {
                if (response?.data?.status) {
                    const productData = response?.data?.Payload;
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


    const handleSearch = (e) => {
        const searchValue = e.target.value;
        const filteredData = categories.filter((category) => {
            return category.title.toLowerCase().includes(searchValue.toLowerCase())
        })

        setCategoriesWithNumberIds(filteredData)

    }

    console.log("categories", categories);

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
                    categories.map((category) => {
                        return (
                            <div
                                key={category.id}
                                className={`category-item ${activeCategoryId === Number(category.categoryId) ? "selected" : ""}`}
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
                        <div
                            key={child.id}
                            className={`child-category ${child.id === activeSubCategoryId ? "active-sub" : ""}`}
                            onClick={() => {
                                setActiveSubCategoryId(child.categoryId)
                            }}
                        >
                            <p className="SubTitleCat">{child.shortTitle}</p>
                        </div>
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
