import React, { useEffect, useState } from "react";
import Default from "./components/Default";
import Header from "./components/Header";
import BundleCredentials from "./components/BundleCredentials";
import ReCharge from "./components/ReCharge";
import MyBundle from "./components/MyBundle";
import ErrorModal from "./components/Modal/ErrorModal";
import SuccessModal from "./components/Modal/SuccessModal";

function App({ parameters }) {
  const dslCredentials = [
    {
      name: "landlineNumber",
      label: "Landline Number",
      type: "phone",
    },
    {
      name: "ldNumber",
      label: "L/D Number",
      type: "text",
    },
  ];

  const fiberCredentials = [
    {
      name: "hfNumber",
      label: "H/F Number",
      type: "text",
    },
  ];

  const fourGCredentials = [
    {
      name: "simNumber",
      label: "Sim Card Number",
      type: "phone",
    },
    {
      name: "username",
      label: "Username",
      type: "text",
    },
  ];

  const [activeButton, setActiveButton] = useState({ name: "Default" });
  const [getBackLink, setBackLink] = useState({ name: "Default" });
  const [getHeaderTitle, setHeaderTitle] = useState("Sodetel");
  const [getDataGetting, setDataGetting] = useState({ id: "" });
  const [planData, setPlanData] = useState({});
  const [identifier, setIdentifier] = useState(null);
  const [apiUrl, setApiUrl] = useState(null);

  const [bundleData, setBundleData] = useState({ id: "" });
  const [credential, setCredential] = useState({
    name: "",
    type: "",
  });
  const [credentialsArray, setCredentialsArray] = useState([]);

  const [modalDesc, setModalDesc] = useState({
    name: "",
    imgPath: "/build/images/alfa/SuccessImg.png",
    title: "Success Modal",
    desc: "Success Modal",
  });
  const handleButtonClick = (name, bundle) => {
    setActiveButton({ name: name, bundle: bundle });
  };
  useEffect(() => {
    if (window.REACT_APP_API_URL == "prod") {
      setApiUrl("");
    } else {
      setApiUrl("http://localhost:3000/bills");
    }
  }, []);

  useEffect(() => {
    setDataGetting("");
    const searchParams = new URLSearchParams(window.location.search);
    const idParam = searchParams.get("comp");
    if (idParam) {
        if(idParam == "4g"){
            setCredential(fourGCredentials[0]);
            setCredentialsArray(fourGCredentials);
        }else if(idParam == "dsl"){
            setCredential(dslCredentials[0]);
            setCredentialsArray(dslCredentials);
        }else{
            setCredential(fiberCredentials[0]);
            setCredentialsArray(fiberCredentials);
        }
      handleButtonClick("BundleCredentials", idParam);
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
    <div id="SodetelBody">
      <Header
        parameters={parameters}
        activeButton={activeButton}
        setActiveButton={setActiveButton}
        getHeaderTitle={getHeaderTitle}
        getBackLink={getBackLink}
        apiUrl={apiUrl}
      />
      {activeButton.name === "Default" && (
        <Default
          setActiveButton={setActiveButton}
          setBackLink={setBackLink}
          setHeaderTitle={setHeaderTitle}
          setCredential={setCredential}
          setCredentialsArray={setCredentialsArray}
        />
      )}

      {activeButton.name === "BundleCredentials" && (
        <BundleCredentials
          credential={credential}
          setCredential={setCredential}
          activeButton={activeButton}
          setActiveButton={setActiveButton}
          setBundleData={setBundleData}
          setModalDesc={setModalDesc}
          bundle={activeButton.bundle}
          setBackLink={setBackLink}
          setHeaderTitle={setHeaderTitle}
          credentialsArray={credentialsArray}
        />
      )}

      {activeButton.name === "Services" && (
        <ReCharge
          parameters={parameters}
          setPrepaidVoucher={setPlanData}
          getVoucherData={bundleData}
          activeButton={activeButton}
          setActiveButton={setActiveButton}
          setHeaderTitle={setHeaderTitle}
          setBackLink={setBackLink}
          setIdentifier={setIdentifier}
        />
      )}

      {activeButton.name === "MyBundle" && (
        <MyBundle
          setDataGetting={setDataGetting}
          parameters={parameters}
          credential={credential}
          getDataGetting={getDataGetting}
          getPrepaidVoucher={planData}
          activeButton={activeButton}
          setActiveButton={setActiveButton}
          setHeaderTitle={setHeaderTitle}
          setBackLink={setBackLink}
          setModalDesc={setModalDesc}
          identifier={identifier}
          apiUrl={apiUrl}
        />
      )}

      {/* Modal */}
      {modalDesc.name === "SuccessModal" && (
        <SuccessModal
          getSuccessModal={modalDesc}
          show={modalDesc.show}
          onHide={() => {
            setModalDesc({
              ...modalDesc,
              name: "",
            });
            setActiveButton({ ...activeButton, name: "Default" });
          }}
        />
      )}
      {modalDesc.name === "ErrorModal" && (
        <ErrorModal
          parameters={parameters}
          getErrorModal={modalDesc}
          show={modalDesc.show}
          onHide={() => {
            setModalDesc({
              ...modalDesc,
              name: "",
              show: false,
            });
            setActiveButton({ ...activeButton, name: "Default" });
          }}
        />
      )}
    </div>
  );
}

export default App;
