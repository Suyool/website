import React, { useEffect, useState } from "react";
const dummyData = [
  {
    imageSrc: "/build/images/Touch/bundleImg1.png",
    price: "$1.22",
    bundleName: "ReCharge 1",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg2.png",
    price: "$2.45",
    bundleName: "ReCharge 2",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg1.png",
    price: "$1.22",
    bundleName: "ReCharge 1",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg2.png",
    price: "$2.45",
    bundleName: "ReCharge 2",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg1.png",
    price: "$1.22",
    bundleName: "ReCharge 1",
  },
  {
    imageSrc: "/build/images/Touch/bundleImg2.png",
    price: "$2.45",
    bundleName: "ReCharge 2",
  }
];

const MyBundle = ({ setModalShow, setModalName, setSuccessModal, setErrorModal, setActiveButton, setHeaderTitle, setBackLink }) => {
  useEffect(() => {
    setHeaderTitle("Pay Mobile Bill")
    setBackLink("ReCharge")
  }, [])
  const [mobileNumber, setMobileNumber] = useState("");

  return (
    <div id="MyBundle">
      <div className="mainTitle">Bundle Name</div>
      <div className="mainDesc">*All taxes excluded</div>
      <img className="BundleBigImg" src="/build/images/Touch/Bundle.png" alt="Bundle" />

      <div className="phNum">
        <div className="title">Enter your phone number to recharge</div>
        <div className="lbNum">
          <div className="MobileNbContainer mt-1">
            <div className="place">
              <img src="/build/images/Touch/flag.png" alt="flag" />
              <div className="code">+961</div>
            </div>
            <input className="nbInput" placeholder="|" value={mobileNumber} onChange={(e) => setMobileNumber(e.target.value)} />
          </div>
        </div>
      </div>

      <div className="CurrencySec">
        <div className="title">Enter your phone number to recharge</div>
        <div className="switch">
          <div className="selected">USD</div>
          <div className="notSelected">LBP</div>
        </div>
      </div>

      <button id="ContinueBtn" className="btnCont mt-5" >Pay Now</button>

      <div className="SuggestedSection mt-5">
        <div className="title">Suggested for you</div>
        <div className="bundlesSection mt-3">
          {dummyData.map((record, index) => (
            <div className="bundleGrid" key={index} onClick={() => { setActiveButton({ name: "MyBundle" }) }}>
              <img className="GridImg" src={record.imageSrc} alt="bundleImg" />
              <div className="gridDesc">
                <div className="Price">{record.price}</div>
                <div className="bundleName">{record.bundleName}</div>
              </div>
            </div>
          ))}
        </div>
      </div>

    </div>
  );
};

export default MyBundle;
