import React, { useEffect, useState } from "react";
import Default from "./Default";
import Header from "./Header";
import ErrorModal from "./Modal/ErrorModal";
import SuccessModal from "./Modal/SuccessModal";
import UsernameForm from "./UsernameForm";
import ProductList from "./ProductList";
import SelectedProductInfo from "./SelectedProductInfo";
import LandlineForm from "./LandlineForm";

const App = ({ parameters }) => {
  const [activeButton, setActiveButton] = useState({ name: "" });
  const [getBackLink, setBackLink] = useState({ name: "" });
  const [getHeaderTitle, setHeaderTitle] = useState("TerraNet");

  const [getDataGetting, setDataGetting] = useState("");

  //Modal Variable
  const [getModalName, setModalName] = useState("");
  const [modalShow, setModalShow] = useState(false);
  const [apiUrl,setApiUrl] = useState(null);
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

  //products
  const [products, setProducts] = useState([]);
  const [selectedProduct, setSelectedProduct] = useState(null);

  useEffect(()=>{
    if(window.REACT_APP_API_URL == "prod"){
      setApiUrl("");
   }else{
      setApiUrl("http://localhost:3000/bills");
   }
  },[])

  useEffect(() => {
    setDataGetting("");
    const searchParams = new URLSearchParams(window.location.search);
    const idParam = searchParams.get("comp");
    if (idParam) {
      setActiveButton({ name: idParam });
      // searchParams.set("")
    }
    window.handleCheckout = (message) => {
      setDataGetting(message);
    };
  }, []);

  const handleReceiveMessage = (event) => {
    if (typeof event.data === "string") {
      setDataGetting(event.data);
    }
  };
  useEffect(() => {
    window.addEventListener("message", handleReceiveMessage);
    return () => {
      window.removeEventListener("message", handleReceiveMessage);
    };
  }, []);

  return (
    <div id="TerraNetBody">
      <Header
        parameters={parameters}
        activeButton={activeButton}
        setActiveButton={setActiveButton}
        getHeaderTitle={getHeaderTitle}
        getBackLink={getBackLink}
        apiUrl={apiUrl}
      />
      <div className="scrolableView">
        <>
          {activeButton.name === "" && (
            <Default
              activeButton={activeButton}
              setActiveButton={setActiveButton}
              setHeaderTitle={setHeaderTitle}
              setBackLink={setBackLink}
            />
          )}

          {/* TerraNet Usename */}
          {(activeButton.name === "UsernameForm" || activeButton.name === "username") && (
            <UsernameForm
              setProducts={setProducts}
              setActiveButton={setActiveButton}
              setBackLink={setBackLink}
              setModalShow={setModalShow}
              setErrorModal={setErrorModal}
              setModalName={setModalName}
              setHeaderTitle={setHeaderTitle}
            />
          )}
          {(activeButton.name === "LandlineForm" || activeButton.name === "landline") && (
            <LandlineForm
              setProducts={setProducts}
              setActiveButton={setActiveButton}
              setBackLink={setBackLink}
              setModalShow={setModalShow}
              setErrorModal={setErrorModal}
              setModalName={setModalName}
              setHeaderTitle={setHeaderTitle}
            />
          )}

          {activeButton.name === "inputValue" && (
            <div id="ReCharge">
              <ProductList
                products={products}
                setSelectedProduct={setSelectedProduct}
                setActiveButton={setActiveButton}
                setBackLink={setBackLink}
                setHeaderTitle={setHeaderTitle}
              />
            </div>
          )}

          {activeButton.name === "SelectedProductInfo" && (
            <SelectedProductInfo
              selectedProduct={selectedProduct}
              setActiveButton={setActiveButton}
              setModalShow={setModalShow}
              setErrorModal={setErrorModal}
              setSuccessModal={setSuccessModal}
              setModalName={setModalName}
              activeButton={activeButton}
              parameters={parameters}
              setBackLink={setBackLink}
              setHeaderTitle={setHeaderTitle}
              apiUrl={apiUrl}
            />
          )}
        </>
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
