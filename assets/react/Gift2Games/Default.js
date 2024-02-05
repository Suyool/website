import React, {useState, useEffect} from "react";
import axios from "axios";
import ContentLoader from "react-content-loader";

const Default = ({setActiveButton, setPrepaidVoucher, setTypeID, setHeaderTitle, setDataGetting}) => {
    const [loading, setLoading] = useState(true);
    const [filteredData, setFilteredData] = useState([]);
    const [categoriesWithNumberIds, setCategoriesWithNumberIds] = useState([]);
    const [childCategories, setChildCategories] = useState([]);
    const [categories, setCategories] = useState([]);
    const [activeCategoryId, setActiveCategoryId] = useState();
    const [activeSubCategoryId, setActiveSubCategoryId] = useState(null);
    const [noProductsMessage, setNoProductsMessage] = useState('');

    const setHeaderTitleVar = setHeaderTitle;

    // Declare getDefaultImage outside of useEffect
    const getDefaultImage = (typeID) => {
        switch (parseInt(typeID, 10)) {
            case 1:
                setHeaderTitleVar('Gaming');
                return '/build/images/gameicon.svg';
            case 2:
                setHeaderTitleVar('Streaming');
                return '/build/images/streamicon.svg';
            case 3:
                setHeaderTitleVar('Gifts');
                return '/build/images/vouchersicon.svg';
            default:
                setHeaderTitleVar('Estore');

        }
    };

    useEffect(() => {
        setDataGetting("")
    }, [])
    useEffect(() => {
        const defaultImage = getDefaultImage(setTypeID);
    }, [setTypeID, setHeaderTitle]);


    const handleSearch = (e) => {
        setChildCategories([]);

        const searchValue = e.target.value.toLowerCase();
        const filteredCategories = categories.filter((category) => {
            // Check if the first 3 characters of the title match the searchValue
            return category.title.toLowerCase().startsWith(searchValue.slice(0, 2));
        });
        if (filteredCategories.length > 0)
            fetchChildCategories(filteredCategories[0]?.id);
        else
            setFilteredData([]);

        setNoProductsMessage('');
        setCategoriesWithNumberIds(filteredCategories);
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
        const childCategories = fetchChildCategories(id);

        // Check if childCategories is defined and an array
        if (childCategories && Array.isArray(childCategories) && childCategories.length > 0) {
            setChildCategories(childCategories);
        } else {
            setActiveSubCategoryId(Number(categoryId));
        }
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
        if (activeSubCategoryId != 0) {
            axios
                .get(`/gift2games/products/${activeSubCategoryId}`)
                .then((response) => {
                    if (response?.data?.status) {
                        const productData = response?.data?.Payload;
                        setFilteredData(productData);
                        if (productData.length === 0) {
                            // Set a message when there are no products
                            setNoProductsMessage('Products out of stock');
                        } else {
                            // Clear the message if products are available
                            setNoProductsMessage('');
                        }
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
            sessionStorage.setItem("categoryName", firstCategory.title)
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
                <input
                    type="text"
                    placeholder="Search in gaming e-store"
                    onChange={(event) => handleSearch(event)}
                    style={{fontWeight: 'bold', color: '#000000', fontFamily: 'PoppinsRegular'}}
                /></div>

            <div className="categories-scroll">
                {
                    categoriesWithNumberIds.map((category) => {
                        return (
                            <div
                                key={category.categoryId}
                                className={`category-item ${activeCategoryId === Number(category.id) ? "selected" : ""}`}
                                onClick={() => {
                                    handleCategoryClick(Number(category.categoryId), category.id)
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
            {noProductsMessage && (
                <div className="out-of-stock-message">
                    <div className="icon">
                        <img src="/build/images/outOfStock.svg" alt="outOfStock"/>
                    </div>
                    <div className="text">
                        <p className="title">Product out of stock</p>
                        <p className="desc">Once we re-stock, you will see the products here</p>
                    </div>
                </div>
            )}

            <div id="ReCharge">
                <div className="bundlesSection">
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
                                            price: record.displayPrice,
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
                                        src={record?.image || getDefaultImage(setTypeID)}
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
