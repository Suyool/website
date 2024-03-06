import React, {useEffect, useState} from "react";
import {useDispatch, useSelector} from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";
import ContentLoader from "react-content-loader";

const Default = () => {
  const dispatch = useDispatch();
  const { fetchCategories,fetchProducts ,fetchChildCategories} = AppAPI();

    const categories = useSelector((state) => state.appData.categories);
    const childCategories = useSelector((state) => state.appData.childCategories);
    const filteredData = useSelector((state) => state.appData.products);
    const isloadingData = useSelector((state) => state.appData.isloadingData);

    const [loading, setLoading] = useState(true);
    const [categoriesWithNumberIds, setCategoriesWithNumberIds] = useState([]);
    const [activeCategoryId, setActiveCategoryId] = useState();
    const [activeSubCategoryId, setActiveSubCategoryId] = useState(null);
    const [noProductsMessage, setNoProductsMessage] = useState('');
    const typeID = localStorage.getItem("typeID")

    const getDefaultImage = (typeID) => {
        switch (parseInt(typeID, 10)) {
            case 1:
                dispatch(settingData({field: "Gaming", value: {title: "TerraNet", backLink: "", currentPage: "",},}));
                return '/build/images/gameicon.svg';
            case 2:
                dispatch(settingData({field: "headerData", value: {title: "Streaming", backLink: "", currentPage: "",},}));
                return '/build/images/streamicon.svg';
            case 3:
                dispatch(settingData({field: "headerData", value: {title: "Gifts", backLink: "", currentPage: "",},}));
                return '/build/images/vouchersicon.svg';
            default:
                dispatch(settingData({field: "headerData", value: {title: "Estore", backLink: "", currentPage: "",},}));
        }
    };

    useEffect(() => {
        fetchCategories(typeID);

    }, []);

    useEffect(() => {
        if(categories.length > 1){
            setCategoriesWithNumberIds(
                categories.map((category) => ({
                    ...category,
                    id: Number(category.id),
                }))
            );
        }

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
            fetchProducts(activeSubCategoryId);

        }
    }, [activeSubCategoryId]);

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

    let typingTimeout;
    const handleChange = (e) => {
        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(() => {
            handleSearch(e);
        }, 500);
    };

    const handleSearch = (e) => {
        const searchValue = e.target.value;
        const filteredCategories = categories.filter((category)=>{
            return category.title.toLowerCase().includes(searchValue.toLowerCase())
        })

        setCategoriesWithNumberIds(filteredCategories);

        if (filteredCategories.length > 0){
            const childCategories = fetchChildCategories(filteredCategories[0]?.id);

            if (childCategories && Array.isArray(childCategories) && childCategories.length > 0){
                setActiveCategoryId(filteredCategories[0]?.id);
            }else{
                setActiveSubCategoryId(Number(filteredCategories[0]?.categoryId));
            }
        }else{
            childCategories([]);
            filteredData([]);
            setActiveSubCategoryId(null);
        }

    }

  return (
      <div id="Default_g2g">
          <div className="search-bar">
              <div className="search-icon">
                  <img src="/build/images/g2g/search.svg" alt=""/>
              </div>
              <input
                  type="text"
                  placeholder="Search in gaming e-store"
                  onChange={(event) => handleChange(event)}
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

          {childCategories.length > 0 && (
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
          )}
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
                  {isloadingData ? (
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
                              <button
                                  className="bundleGrid"
                                  key={index}
                                  onClick={() => {
                                      dispatch(settingObjectData({ mainField: "headerData", field: "currentPage", value: "MyBundle" }));
                                      dispatch(
                                          settingData({
                                              field: "productInfo",
                                              value: {
                                                  price: record.displayPrice,
                                                  displayPrice: record.displayPrice,
                                                  currency: record.currency,
                                                  title: record.title,
                                                  image: record.image,
                                                  productId: record.productId

                                              },
                                          })
                                      );
                                  }}
                                  disabled={!record.inStock}
                              >
                                  <img
                                      className="GridImg"
                                      src={record?.image || getDefaultImage(typeID)}
                                      alt="bundleImg"
                                      onError={(e) => {
                                          e.target.src = '../build/images/g2g/freefireicon2.png';
                                      }}
                                  />
                                  <div className="gridDesc" style={{ opacity: record.inStock === false ? 0.5 : 1 }}>
                                      <div className="Price">
                                          ${record?.displayPrice}{" "}
                                          {!record.inStock && <span className="outstock">Out of Stock</span>}
                                      </div>
                                      <div className="bundleName">{record.title}</div>
                                  </div>
                              </button>
                          ))}
                      </>
                  )}
              </div>
          </div>
      </div>
  );
};

export default Default;
