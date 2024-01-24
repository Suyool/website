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

  const desiredChildIdsMap  = {
    "1111": ["646"],
    "1110": ["414"],
    "1091": ["1123", "730"],
    "454": ["514", "504"],
    "448": ["633", "624", "298"],
    "446": ["582", "581", "413"],
    "445": ["567", "562", "558"],
    "444": ["575"],
    "442": ["704", "703", "644", "642", "641", "636"],
    "441": ["664", "656", "617", "496"],
    "439": ["477", "472"],
    "434": ["469", "462", "457", "455", "428"],
    "406": ["645"],
    "302": ["417"],
    "282": ["647"],
    "277": ["905"],
    "76": ["343"],
  };
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
    const categoryWithChildren = categories.find((category) => category.id === categoryId);

    if (categoryWithChildren && categoryWithChildren.childs && categoryWithChildren.childs.length > 0) {
      const childIds = desiredChildIdsMap[categoryId] || [];
      const filteredChildCategories = categoryWithChildren.childs.filter((child) => childIds.includes(child.id));

      setChildCategories(filteredChildCategories);
      setActiveButton({ name: "ChildCategories", category: categoryId });
    } else {
      setActiveButton({ name: "Products", category: categoryId });
      setChildCategories([]);
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
                    desiredChildIdsMap={desiredChildIdsMap} // Pass desiredChildIdsMap here
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
