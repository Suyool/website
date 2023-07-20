import axios from "axios";
import React, { useEffect, useState } from "react";

const MyBundle = ({ getPrepaidVoucher, setModalShow, setModalName, setSuccessModal, setErrorModal, setActiveButton, setHeaderTitle, setBackLink }) => {
  useEffect(() => {
    setHeaderTitle("Pay Mobile Bill")
    setBackLink("ReCharge")
    // console.log(getPrepaidVoucher)
  }, [])
  const [getPaymentConfirmation, setPaymentConfirmation] = useState(false);
  const [getSerialToClipboard, setSerialToClipboard] = useState("");

  const handleConfirmPay = () => {
    axios
      .post("/alfa/BuyPrePaid",
        {
          Token: "e3cd72df-6cb4-4afc-a687-9c66e84381c1",
          category: "MTC",
          // category: getPrepaidVoucher.vouchercategory,
          type: getPrepaidVoucher.vouchertype,
          amountLBP: getPrepaidVoucher.priceLBP,
          amountUSD: getPrepaidVoucher.priceUSD,
        })
      .then((response) => {
        console.log(response)
        if (response?.data.IsSuccess) {
          setPaymentConfirmation(true);
          setSerialToClipboard(response?.data?.message?.d?.voucherSerial);
        } else {

        }
        // console.log(response);
      })
      .catch((error) => {
        console.log(error);
      });
  };

  const copyToClipboard = () => {
    const tempInput = document.createElement("input");
    tempInput.value = getSerialToClipboard;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
  };


  return (
    <div id="MyBundle" className={`${getPaymentConfirmation && "hideBack"}`}>
      {getPaymentConfirmation ?
        <>
          <div id="PaymentConfirmationPrePaid">
            <div className="topSection">
              <div className="brBoucket"></div>
              <div className="titles">
                <div className="titleGrid"></div>
                <button onClick={() => { setActiveButton({ name: "MyBundle" }); setPaymentConfirmation(false) }}>Cancel</button>
              </div>
            </div>

            <div className="bodySection">
              <img className="SuccessImg" src="/build/images/Alfa/SuccessImg.png" alt="Bundle" />
              <div className="bigTitle">Payment Successful</div>
              <div className="descriptio">You have successfully purchased the ${getPrepaidVoucher.priceUSD} Alfa recharge card.</div>

              <div className="br"></div>

              <div className="copyTitle">To recharge your prepaid number: </div>
              <div className="copyDesc">Copy the secret code below</div>

              <button className="copySerialBtn" onClick={copyToClipboard}>
                <div></div>
                <div className="serial">{getSerialToClipboard}</div>
                <img className="copySerial" src="/build/images/Alfa/copySerial.png" alt="copySerial" />
              </button>

              <button id="ContinueBtn" className="mt-3" onClick={() => { console.log("share code") }} >Share Code</button>

              <div className="stepsToRecharge">

                <div className="steps">
                  <div className="dot"></div>
                  <div className="textStep">Go to your phone tab</div>
                </div>
                <div className="steps">
                  <div className="dot"></div>
                  <div className="textStep">Dial “ *14* ”</div>
                </div>
                <div className="steps">
                  <div className="dot"></div>
                  <div className="textStep">Paste the code</div>
                </div>
                <div className="steps">
                  <div className="dot"></div>
                  <div className="textStep">Press “ # ” </div>
                </div>
                <div className="steps">
                  <div className="dot"></div>
                  <div className="textStep">Tap Call</div>
                </div>
                <div className="steps">
                  <div className="dot"></div>
                  <div className="textStep">Your mobile prepaid line is now recharged</div>
                </div>

              </div>
            </div>
          </div>
        </>
        :
        <>
          <div className="MyBundleBody">
            <div className="mainTitle">Bundle Name</div>
            <div className="mainDesc">*All taxes excluded</div>
            <img className="BundleBigImg" src="/build/images/Alfa/Bundle.png" alt="Bundle" />

            <div className="MoreInfo">
              <div className="label">Amount in USD</div>
              <div className="value">$ {getPrepaidVoucher.priceUSD}</div>
            </div>

            <div className="MoreInfo">
              <div className="label">Amount in LBP (Sayrafa Rate)</div>
              <div className="value">LBP {getPrepaidVoucher.priceLBP}</div>
            </div>

            <div className="MoreInfo">
              <div className="label">+ V.A.T & Stamp Duty</div>
              <div className="value">LBP 10,000</div>
            </div>

            <div className="br"></div>
            <div className="MoreInfo">
              <div className="label">Total</div>
              <div className="value1">LBP 100,000</div>
            </div>
          </div>


          <button id="ContinueBtn" className="btnCont mt-5" onClick={handleConfirmPay} >Pay Now</button>
        </>
      }
    </div>
  );
};

export default MyBundle;


// import React, { useEffect, useState } from "react";
// const dummyData = [
//   {
//     imageSrc: "/build/images/Alfa/bundleImg1.png",
//     price: "$1.22",
//     bundleName: "ReCharge 1",
//   },
//   {
//     imageSrc: "/build/images/Alfa/bundleImg2.png",
//     price: "$2.45",
//     bundleName: "ReCharge 2",
//   },
//   {
//     imageSrc: "/build/images/Alfa/bundleImg1.png",
//     price: "$1.22",
//     bundleName: "ReCharge 1",
//   },
//   {
//     imageSrc: "/build/images/Alfa/bundleImg2.png",
//     price: "$2.45",
//     bundleName: "ReCharge 2",
//   },
//   {
//     imageSrc: "/build/images/Alfa/bundleImg1.png",
//     price: "$1.22",
//     bundleName: "ReCharge 1",
//   },
//   {
//     imageSrc: "/build/images/Alfa/bundleImg2.png",
//     price: "$2.45",
//     bundleName: "ReCharge 2",
//   }
// ];

// const MyBundle = ({ setModalShow, setModalName, setSuccessModal, setErrorModal, setActiveButton, setHeaderTitle, setBackLink }) => {
//   useEffect(() => {
//     setHeaderTitle("Pay Mobile Bill")
//     setBackLink("ReCharge")
//   }, [])
//   const [mobileNumber, setMobileNumber] = useState("");

//   return (
//     <div id="MyBundle">
//       <div className="mainTitle">Bundle Name</div>
//       <div className="mainDesc">*All taxes excluded</div>
//       <img className="BundleBigImg" src="/build/images/Alfa/Bundle.png" alt="Bundle" />

//       <div className="phNum">
//         <div className="title">Enter your phone number to recharge</div>
//         <div className="lbNum">
//           <div className="MobileNbContainer mt-1">
//             <div className="place">
//               <img src="/build/images/Alfa/flag.png" alt="flag" />
//               <div className="code">+961</div>
//             </div>
//             <input className="nbInput" placeholder="|" value={mobileNumber} onChange={(e) => setMobileNumber(e.target.value)} />
//           </div>
//         </div>
//       </div>

//       <div className="CurrencySec">
//         <div className="title">Enter your phone number to recharge</div>
//         <div className="switch">
//           <div className="selected">USD</div>
//           <div className="notSelected">LBP</div>
//         </div>
//       </div>

//       <button id="ContinueBtn" className="btnCont mt-5" >Pay Now</button>

//       <div className="SuggestedSection mt-5">
//         <div className="title">Suggested for you</div>
//         <div className="bundlesSection mt-3">
//           {dummyData.map((record, index) => (
//             <div className="bundleGrid" key={index} onClick={() => { setActiveButton({ name: "MyBundle" }) }}>
//               <img className="GridImg" src={record.imageSrc} alt="bundleImg" />
//               <div className="gridDesc">
//                 <div className="Price">{record.price}</div>
//                 <div className="bundleName">{record.bundleName}</div>
//               </div>
//             </div>
//           ))}
//         </div>
//       </div>

//     </div>
//   );
// };

// export default MyBundle;