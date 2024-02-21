import React, { useEffect, useState } from "react";
import Default from "./Default";
import Header from "./Header";
import ErrorModal from "./Modal/ErrorModal";
import SuccessModal from "./Modal/SuccessModal";
import Packages from "./packages";
import PackagesInfo from "./PackagesInfo";
import Account from "./Account";
import PlanDetail from "./PlanDetail";
import RechargeThePayment from "./RechargeThePayment";

const App = ({ parameters }) => {
  const [selectedPlan, setSelectedPlan] = useState(null); // State to store the selected plan
  const [selectedPackage, setSelectedPackage] = useState(null); // State to store the selected plan
  const [activeButton, setActiveButton] = useState({ name: "" });
  const [isPackageItem, setIsPackageItem] = useState(false);
  const [getBackLink, setBackLink] = useState({ name: "" });
  const [getHeaderTitle, setHeaderTitle] = useState("Suyool eSIM");
  const [getModalName, setModalName] = useState("");
  const [modalShow, setModalShow] = useState(false);
  const [getDataGetting, setDataGetting] = useState("");
  const [getSpinnerLoader, setSpinnerLoader] = useState(false);
  const [getEsimDetail, setEsimDetail] = useState({});

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
      setDataGetting(message);
    };
  }, []);

  return (
    <div id="SimlyBody">
      <Header
        parameters={parameters}
        activeButton={activeButton}
        setIsPackageItem={setIsPackageItem}
        isPackageItem={isPackageItem}
        setActiveButton={setActiveButton}
        getHeaderTitle={getHeaderTitle}
        getBackLink={getBackLink}
        getSpinnerLoader={getSpinnerLoader}
      />
      <div className="scrolableView">
        {getModalName === "" && (
          <>
            {/* {activeButton.name === "" && (
                            <Default
                                setActiveButton={setActiveButton}
                                setHeaderTitle={setHeaderTitle}
                                setBackLink={setBackLink}
                                categories={categories}
                                setPrepaidVoucher={setPrepaidVoucher}
                                setTypeID ={parameters.typeID}
                                setDataGetting={setDataGetting}
                            />
                        )} */}
            {activeButton.name === "" && (
              <Packages
                setDataGetting={setDataGetting}
                parameters={parameters}
                getDataGetting={getDataGetting}
                setModalShow={setModalShow}
                setModalName={setModalName}
                setSuccessModal={setSuccessModal}
                setErrorModal={setErrorModal}
                setIsPackageItem={setIsPackageItem}
                isPackageItem={isPackageItem}
                setActiveButton={setActiveButton}
                setHeaderTitle={setHeaderTitle}
                setBackLink={setBackLink}
                setSelectedPlan={setSelectedPlan}
                setSelectedPackage={setSelectedPackage}
                setSpinnerLoader={setSpinnerLoader}
              />
            )}
            {activeButton.name === "PackagesInfo" && (
              <PackagesInfo
                setDataGetting={setDataGetting}
                parameters={parameters}
                getDataGetting={getDataGetting}
                setModalShow={setModalShow}
                setModalName={setModalName}
                setSuccessModal={setSuccessModal}
                setErrorModal={setErrorModal}
                setActiveButton={setActiveButton}
                setHeaderTitle={setHeaderTitle}
                setBackLink={setBackLink}
                selectedPlan={selectedPlan}
                selectedPackage={selectedPackage}
                setSpinnerLoader={setSpinnerLoader}
                getSpinnerLoader={getSpinnerLoader}
              />
            )}
            {activeButton.name === "Account" && (
              <Account
                setDataGetting={setDataGetting}
                parameters={parameters}
                getDataGetting={getDataGetting}
                setModalShow={setModalShow}
                setModalName={setModalName}
                setSuccessModal={setSuccessModal}
                setErrorModal={setErrorModal}
                setActiveButton={setActiveButton}
                setHeaderTitle={setHeaderTitle}
                setBackLink={setBackLink}
                selectedPlan={selectedPlan}
                selectedPackage={selectedPackage}
                setSpinnerLoader={setSpinnerLoader}
                getSpinnerLoader={getSpinnerLoader}
                setEsimDetail={setEsimDetail}
              />
            )}
            {activeButton.name === "PlanDetail" && <PlanDetail getEsimDetail={getEsimDetail} setBackLink={setBackLink} />}

            {activeButton.name === "RechargeThePayment" && <RechargeThePayment setHeaderTitle={setHeaderTitle} setBackLink={setBackLink} />}

          </>
        )}
      </div>

      {/* Modal */}
      {getModalName === "SuccessModal" && (
        <SuccessModal
          getSuccessModal={getSuccessModal}
          show={modalShow}
          onsetActiveButton={() => {
            setModalShow(false);
            setModalName("");
            setActiveButton({ name: "RechargeThePayment" });
            console.log(activeButton);
          }}
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
          setActiveButton={() => {
            setActiveButton({ name: "Account" });
            console.log("clicked");
          }}
          onHide={() => {
            setSpinnerLoader(false);
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
