import React, { useEffect, useState } from "react";
import Default from "./Default";
import Header from "./Header";
import ErrorModal from "./Modal/ErrorModal";
import SuccessModal from "./Modal/SuccessModal";
import MyBundle from "./MyBundle";
import ReCharge from "./ReCharge";
import ChildCategories from "./ChildCategories";
import axios from "axios";

const App = ({ parameters }) => {
  const [activeButton, setActiveButton] = useState({ name: "" });
  const [getBackLink, setBackLink] = useState({ name: "" });
  const [getHeaderTitle, setHeaderTitle] = useState("Gift2Games");
  const [getModalName, setModalName] = useState("");
  const [modalShow, setModalShow] = useState(false);
  const [categories, setCategories] = useState([]);
  const [childCategories, setChildCategories] = useState([]);
  const [getDataGetting, setDataGetting] = useState("");
  const [getPrepaidVoucher, setPrepaidVoucher] = useState(true);
  const [getSuccessModal, setSuccessModal] = useState({
    imgPath: "/build/images/alfa/SuccessImg.png",
    title: "Success Modal",
    desc: "Success Modal",
  });
  const [getErrorModal, setErrorModal] = useState({
    img: "/build/images/alfa/error.png",
    title: "Error Modal",
    btn: "Top Up",
    desc: "Error Modal",
  });
  useEffect(() => {
    setDataGetting("");
    window.handleCheckout = (message) => {
      console.log("Handling checkout:", message);
      setDataGetting(message);
    };

    fetchCategories();
  }, []);

  const fetchCategories = () => {
    axios
        .get("/gift2games/categories")
        .then((response) => {
          if (response?.data?.status) {
            const parsedData = JSON.parse(response?.data?.Payload);
            setCategories(parsedData?.data);
          }
        })
        .catch((error) => {
          console.error("Error fetching categories:", error);
        });
  };

  const handleCategoryClick = (categoryId, hasChild) => {
    // Check if the category has children based on the 'childs' array
    const categoryWithChildren = categories.find((category) => category.id === categoryId);
    if (categoryWithChildren && categoryWithChildren.childs && categoryWithChildren.childs.length > 0) {
      // If 'childs' array is not empty, navigate to child categories
      setChildCategories(categoryWithChildren.childs);
      setActiveButton({ name: "ChildCategories", category: categoryId });
    } else {
      // Navigate directly to products for the selected category
      setActiveButton({ name: "Products", category: categoryId });
    }
  };


  const handleChildCategoryClick = (childCategoryId) => {
    setActiveButton({ name: "Products", category: childCategoryId });
  };

  return (
    <div id="G2GBody">
      <Header
        parameters={parameters}
        activeButton={activeButton}
        setActiveButton={setActiveButton}
        getHeaderTitle={getHeaderTitle}
        getBackLink={getBackLink}
      />
      <div className="scrolableView">
        {getModalName === "" && (
          <>
            {activeButton.name === "" && (
                <Default
                    SetVoucherData={() => {}}
                    setActiveButton={setActiveButton}
                    setHeaderTitle={setHeaderTitle}
                    setBackLink={setBackLink}
                    categories={categories}
                    handleCategoryClick={handleCategoryClick}
                />
            )}

            {activeButton.name === "ChildCategories" && (
                <ChildCategories
                    childCategories={childCategories}
                    handleChildCategoryClick={handleChildCategoryClick}
                    setBackLink={setBackLink}

                />
            )}
            {activeButton.name === "Products" && (
              <ReCharge
                parameters={parameters}
                activeButton={activeButton}
                setActiveButton={setActiveButton}
                setHeaderTitle={setHeaderTitle}
                setBackLink={setBackLink}
                setPrepaidVoucher={setPrepaidVoucher}
              />
            )}

            {activeButton.name === "MyBundle" && (
                <MyBundle
                    setDataGetting={setDataGetting}
                    parameters={parameters}
                    getDataGetting={getDataGetting}
                    getPrepaidVoucher={getPrepaidVoucher}
                    setModalShow={setModalShow}
                    setModalName={setModalName}
                    setSuccessModal={setSuccessModal}
                    setErrorModal={setErrorModal}
                    setActiveButton={setActiveButton}
                    setHeaderTitle={setHeaderTitle}
                    setBackLink={setBackLink}
                    setDataGetting={setDataGetting}

                />
            )}
          </>
        )}
      </div>

      {/* Modal */}
      {getModalName === "SuccessModal" && (
        <SuccessModal
          getSuccessModal={getSuccessModal}
          show={modalShow}
          onHide={() => {
            setModalShow(false);
            setModalName("");
            setActiveButton({ name: "" });
          }}
        />
      )}
      {getModalName === "ErrorModal" && (
        <ErrorModal
          parameters={parameters}
          getErrorModal={getErrorModal}
          show={modalShow}
          onHide={() => {
            setModalShow(false);
            setModalName("");
            setActiveButton({ name: "" });
          }}
        />
      )}
    </div>
  );
};

export default App;
// const helloYou    = (name)=> {name = "you" || name   ;console.log("hello" + name + "!" );};
{
  /* <MyBundle setDataGetting={setDataGetting} parameters={parameters} getDataGetting={getDataGetting} getPrepaidVoucher={getPrepaidVoucher} setModalShow={setModalShow} setErrorModal={setErrorModal} setSuccessModal={setSuccessModal} setModalName={setModalName} activeButton={activeButton} setActiveButton={setActiveButton} setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />;  */
}

{
  /* <MyBundle
  setDataGetting={setDataGetting}
  parameters={parameters}
  getDataGetting={getDataGetting}
  getPrepaidVoucher={getPrepaidVoucher}
  setModalShow={setModalShow}
  setErrorModal={setErrorModal}
  setSuccessModal={setSuccessModal}
  setModalName={setModalName}
  activeButton={activeButton}
  setActiveButton={setActiveButton}
  setHeaderTitle={setHeaderTitle}
  setBackLink={setBackLink}
/> */
}
