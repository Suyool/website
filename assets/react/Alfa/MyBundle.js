import axios from "axios";
import React, { useEffect, useState } from "react";

const MyBundle = ({ getPrepaidVoucher, setModalShow, setModalName, setSuccessModal, setErrorModal, setActiveButton, setHeaderTitle, setBackLink }) => {
  useEffect(() => {
    setHeaderTitle("Pay Mobile Bill")
    setBackLink("ReCharge")
    setIsButtonDisabled(false);
    // console.log(getPrepaidVoucher)
  }, [])
  const [getPaymentConfirmation, setPaymentConfirmation] = useState(false);
  const [getSerialToClipboard, setSerialToClipboard] = useState("");
  const [isButtonDisabled, setIsButtonDisabled] = useState(false);

  const handleConfirmPay = () => {
    // console.log("click")
    setIsButtonDisabled(true);
    axios
      .post("/alfa/BuyPrePaid",
        {
          Token: "",
          category: "ALFA",
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
              path: jsonResponse.ButtonOne.Flag,
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
          }
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
              <img className="SuccessImg" src="/build/images/alfa/SuccessImg.png" alt="Bundle" />
              <div className="bigTitle">Payment Successful</div>
              <div className="descriptio">You have successfully purchased the ${getPrepaidVoucher.priceUSD} Alfa recharge card.</div>

              <div className="br"></div>

              <div className="copyTitle">To recharge your prepaid number: </div>
              <div className="copyDesc">Copy the secret code below</div>

              <button className="copySerialBtn" onClick={copyToClipboard}>
                <div></div>
                <div className="serial">{getSerialToClipboard}</div>
                <img className="copySerial" src="/build/images/alfa/copySerial.png" alt="copySerial" />
              </button>

              <button id="ContinueBtn" className="mt-3" onClick={() => { console.log("share code") }} >Share Code</button>

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
            <div className="mainTitle">{getPrepaidVoucher.desc1}</div>
            <div className="mainDesc">*All taxes excluded</div>
            <img className="BundleBigImg" src={`/build/images/alfa/bundle${getPrepaidVoucher.vouchertype}h.png`} alt="Bundle" />
            {/* <img className="BundleBigImg" src={`/build/images/alfa/bundle${getPrepaidVoucher.vouchertype}x2.png`} alt="Bundle" />
            <img className="BundleBigImg" src={`/build/images/alfa/bundle${getPrepaidVoucher.vouchertype}x3.png`} alt="Bundle" />
            <img className="BundleBigImg" src={`/build/images/alfa/bundle${getPrepaidVoucher.vouchertype}x4.png`} alt="Bundle" /> */}

            <div className="smlDesc">Alfa only accepts payments in LBP.</div>
            <div className="relatedInfo">{getPrepaidVoucher.desc2}</div>
            <div className="MoreInfo">
              <div className="label">Amount in USD</div>
              <div className="value">$ {getPrepaidVoucher.priceUSD}</div>
            </div>

            <div className="br"></div>
            <div className="MoreInfo">
              <div className="label">Total</div>
              <div className="value1">LBP {parseInt(getPrepaidVoucher.priceLBP).toLocaleString()}</div>
            </div>
          </div>


          <button id="ContinueBtn" className="btnCont" onClick={handleConfirmPay} disabled={isButtonDisabled}>Pay Now</button>
        </>
      }
    </div>
  );
};

export default MyBundle;