import axios from "axios";
import React, { useEffect, useState } from "react";

const MyBundle = ({setDataGetting,parameters,getDataGetting, getPrepaidVoucher, setModalShow, setModalName, setSuccessModal, setErrorModal, setActiveButton, setHeaderTitle, setBackLink }) => {
  const [getPaymentConfirmation, setPaymentConfirmation] = useState(false);
  const [getSerialToClipboard, setSerialToClipboard] = useState("");
  const [isButtonDisabled, setIsButtonDisabled] = useState(false);

  useEffect(() => {
    setHeaderTitle("Re-charge Touch")
    setBackLink("ReCharge")
    setIsButtonDisabled(false);
    // console.log(getPrepaidVoucher)
  }, [])

  const handleShare= () => {
    let object = [
      {
        Share: {
          share: "share",
          text: shareCode,
        },
      },
    ];
    if (parameters?.deviceType === "Android") {
      setTimeout(() => {
        window.AndroidInterface.callbackHandler(JSON.stringify(object));
      }, 2000);
    } else if (parameters?.deviceType === "Iphone") {
      setTimeout(() => {
        window.webkit.messageHandlers.callbackHandler.postMessage(
          object
        );
      }, 2000);
    }
  }

  const handleConfirmPay = () => {
    // console.log("clicked");
    setIsButtonDisabled(true);
    if (parameters?.deviceType === "Android") {
      setTimeout(() => {
        window.AndroidInterface.callbackHandler("message");
      }, 2000);
    } else if (parameters?.deviceType === "Iphone") {
      // const message = "data";

      setTimeout(() => {
        // window.webkit.messageHandlers.postMessage(function(message){alert("oki");}+"");
        //window.webkit.messageHandlers.callbackHandler.postMessage(function(){alert("oki");}+"");

        window.webkit.messageHandlers.callbackHandler.postMessage(
          "fingerprint"
        );
      }, 2000);
    }
    
  };

  useEffect(()=>{
    if(getDataGetting == "success"){
      axios
      .post("/touch/BuyPrePaid",
        {
          Token: "",
          category: "MTC",
          // category: getPrepaidVoucher.vouchercategory,
          desc: getPrepaidVoucher.desc,
          type: getPrepaidVoucher.vouchertype,
          amountLBP: getPrepaidVoucher.priceLBP,
          amountUSD: getPrepaidVoucher.priceUSD,
        })
      .then((response) => {
        const jsonResponse = response?.data?.message;
        console.log(jsonResponse)
        // console.log()
        if (response?.data.IsSuccess) {
          setPaymentConfirmation(true);
          setSerialToClipboard("*14*" + response?.data?.data?.voucherSerial + "#");
        } else {
          console.log(response.data.flagCode)
          // console.log(!response.data.IsSuccess && response.data.flagCode == 10)
          if (response.data.IsSuccess == false && response.data.flagCode == 10) {
            // console.log("step 3")
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/alfa/error.png",
              title: jsonResponse.Title,
              desc: jsonResponse.SubTitle,
              path: 50,
              btn: jsonResponse.ButtonOne.Text,
            });
            setModalShow(true);
          } else if (
            !response.data.IsSuccess &&
            response.data.flagCode == 11
          ) {
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/alfa/error.png",
              title: jsonResponse.Title,
              desc: jsonResponse.SubTitle,
              path: jsonResponse.ButtonOne.Flag,
              btn: jsonResponse.ButtonOne.Text,
            });
            setModalShow(true);
          }else if(jsonResponse == "There are no vouchers of this type currently availalble."){
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/alfa/error.png",
              title: "Recharge Card Unavailable ",
              desc: `The ${getPrepaidVoucher.priceUSD}$ Touch Recharge card is unavailable. 
              Kindly choose another one.`,
              // path: response.data.path,
              btn:'OK'
            });
            setModalShow(true);
          }
          else{
            setModalName("ErrorModal");
            setErrorModal({
              img: "/build/images/alfa/error.png",
              title: "Please Try again",
              desc: "you cannot purchase now",
              // path: response.data.path,
              btn:'OK'
            });
            setModalShow(true);
          }
        }
        // console.log(response);
      })
      .catch((error) => {
        console.log(error);
      });
    } 
    else if(getDataGetting == "failed"){
      setIsButtonDisabled(false);
      setDataGetting("");

    }
  })

  const copyToClipboard = () => {
    const tempInput = document.createElement("input");
    tempInput.value = getSerialToClipboard;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
  };

// console.log(getPrepaidVoucher);
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
              <img className="SuccessImg" src="/build/images/touch/SuccessImg.png" alt="Bundle" />
              <div className="bigTitle">Payment Successful</div>
              <div className="descriptio">You have successfully purchased the ${getPrepaidVoucher.priceUSD} Touch recharge card.</div>

              <div className="br"></div>

              <div className="copyTitle">To recharge your prepaid number: </div>
              <div className="copyDesc">Copy the 14-digit secret code below</div>

              <button className="copySerialBtn" onClick={copyToClipboard}>
                <div></div>
                <div className="serial">{getSerialToClipboard}</div>
                <img className="copySerial" src="/build/images/touch/copySerial.png" alt="copySerial" />
              </button>

              <button id="ContinueBtn" className="mt-3" onClick={() => { handleShare() }} >Share Code</button>

              <div className="stepsToRecharge">

                <div className="steps">
                  <div className="dot"></div>
                  <div className="textStep">Go to your phone tab</div>
                </div>
                <div className="steps">
                  <div className="dot"></div>
                  <div className="textStep">Paste the code</div>
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
            <div className="mainTitle">{getPrepaidVoucher.desc3}</div>
            <div className="mainDesc">*All taxes excluded</div>
            <img className="BundleBigImg" src={`/build/images/touch/Bundle${getPrepaidVoucher.vouchertype}h.png`} alt="Bundle" />

            <div className="smlDesc"><img className="question" src={`/build/images/touch/question.png`} alt="question" />Touch only accepts payments in L.L</div>
            <div className="relatedInfo">{getPrepaidVoucher.desc1}</div>
            <div className="MoreInfo">
              <div className="label">Amount in L.L (Including taxes)</div>
              <div className="value">L.L {parseInt(getPrepaidVoucher.priceLBP).toLocaleString()}</div>
            </div>

            <div className="br"></div>
            <div className="MoreInfo">
              <div className="label">Total (Sayrafa rate)</div>
              <div className="value1">L.L {parseInt(getPrepaidVoucher.priceLBP).toLocaleString()}</div>
            </div>
          </div>


          <button id="ContinueBtn" className="btnCont" onClick={handleConfirmPay} disabled={isButtonDisabled} >Pay Now</button>
        </>
      }
    </div>
  );
};

export default MyBundle;