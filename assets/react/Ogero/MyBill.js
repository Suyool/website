import React, { useEffect, useState } from "react";
import axios from "axios";

const MyBill = ({ getLandlineData, setModalShow, setModalName, setSuccessModal, setErrorModal, setActiveButton, setHeaderTitle, setBackLink }) => {

  useEffect(() => {
    setHeaderTitle("Pay Landline Bill")
    setBackLink("PayBill")
  }, [])

  const handleConfirmPay = () => {
    axios
      .post("/ogero/landline/pay",
        {
          LandlineId: getLandlineData.id
        }
      )
      .then((response) => {
        console.log(response.data);
        const jsonResponse = response?.data?.message;
        setSpinnerLoader(false);
        if (response.data?.IsSuccess) {
          setModalName("SuccessModal");
          setSuccessModal({
            imgPath: "/build/images/alfa/SuccessImg.png",
            title: "Alfa Bill Paid Successfully",
            desc: `You have successfully paid your Alfa bill of L.L ${" "} ${parseInt(response.data?.data.amount).toLocaleString()}.`
          })
          setModalShow(true);
        } else {
          console.log(response.data.flagCode)
          if (response.data.IsSuccess == false && response.data.flagCode == 10) {
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
      })
      .catch((error) => {
        console.log(error);
        setSpinnerLoader(false);
      });
  };

  return (
    <div id="MyBill">

      <div id="PaymentConfirmationSection">
        <div className="topSection">
          <div className="brBoucket"></div>
          <div className="titles">
            <div className="titleGrid">Payment Confirmation</div>
            <button onClick={() => { setActiveButton({ name: "PayBill" }); }}>Cancel</button>
          </div>
        </div>

        <div className="bodySection">
          <div className="cardSec">
            <img src="/build/images/Ogero/OgeroLogo.png" alt="flag" />
            <div className="method">Ogero Landline Bill Payment</div>
          </div>

          <div className="MoreInfo">
            <div className="label">Phone Number</div>
            <div className="value">+961 04453277</div>
          </div>

          <div className="br"></div>

          <div className="MoreInfo">
            <div className="label">Amount in LBP (Sayrafa Rate)</div>
            <div className="value1">LBP 90,000</div>
          </div>

          <div className="taxes">*All taxes included</div>

          <div className="br"></div>

          <div className="MoreInfo">
            <div className="label">Total</div>
            <div className="value2">LBP 100,000</div>
          </div>

        </div>

        <div className="footSectionPick">
          <button onClick={handleConfirmPay} >Confirm & Pay</button>
        </div>
      </div>

    </div>
  );
};

export default MyBill;
