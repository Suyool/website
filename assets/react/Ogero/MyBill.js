import React, { useEffect, useState } from "react";
import axios from "axios";
import { Spinner } from "react-bootstrap";

const MyBill = ({ getLandlineMobile, getLandlineDisplayedData, getLandlineData, setModalShow, setModalName, setSuccessModal, setErrorModal, setActiveButton, setHeaderTitle, setBackLink }) => {

  const [isButtonDisabled, setIsButtonDisabled] = useState(false);
  const [getSpinnerLoader, setSpinnerLoader] = useState(false);
  const [getdisplayedFees, setdisplayedFees] = useState("");

  useEffect(() => {
    setHeaderTitle("Pay Landline Bill")
    setBackLink("PayBill")
    setIsButtonDisabled(false);
  }, [])

  const handleConfirmPay = () => {
    setIsButtonDisabled(true);
    setSpinnerLoader(true);
    axios
      .post("/ogero/landline/pay",
        {
          LandlineId: getLandlineData.id
        }
      )
      .then((response) => {
        console.log(response);
        const jsonResponse = response?.data?.message;
        setdisplayedFees(response?.data?.displayedFees);
        console.log(getdisplayedFees);
        setSpinnerLoader(false);
        if (response.data?.IsSuccess) {
          setModalName("SuccessModal");
          setSuccessModal({
            imgPath: "/build/images/Ogero/SuccessImg.png",
            title: "Ogero Landline Bill Paid Successfully",
            desc: `You have successfully paid your Ogero Landline bill of L.L ${parseInt(response.data?.data.amount).toLocaleString()}.`
          })
          setModalShow(true);
        } else {
          // console.log(response.data.flagCode)
          if (response.data.IsSuccess == false && response.data.flagCode == 10) {
            setModalName("ErrorModal");
            setErrorModal({
              imgPath: "/build/images/Ogero/ErrorImg.png",
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
              imgPath: "/build/images/Ogero/ErrorImg.png",
              title: jsonResponse.Title,
              desc: jsonResponse.SubTitle,
              path: jsonResponse.ButtonOne.Flag,
              btn: jsonResponse.ButtonOne.Text,
            });
            setModalShow(true);
          }else{
            setModalName("ErrorModal");
            setErrorModal({
              imgPath: "/build/images/alfa/error.png",
              title: "Please Try again",
              desc: `You can not purchase now`,
              // path: response.data.path,
              // btn:'Top up'
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
    <div id="MyBill" className="hideBack">
      {getSpinnerLoader && <div id="spinnerLoader"><Spinner className="spinner" animation="border" variant="secondary" /></div>}

      {getSpinnerLoader ? <></> :
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
              <div className="label">Landline Number</div>
              <div className="value">+961 {getLandlineMobile}</div>
            </div>

            <div className="br"></div>

            <div className="MoreInfo">
              <div className="label">Amount in LBP</div>
              <div className="value1">LBP {parseInt(getLandlineDisplayedData.OgeroTotalAmount).toLocaleString()}</div>
            </div>

            <div className="MoreInfo">
              <div className="label">Fees in LBP</div>
              <div className="value1">LBP {parseInt(getLandlineDisplayedData.Fees).toLocaleString()}</div>
            </div>


            {/* <div className="taxes">*All taxes included</div> */}

            <div className="br"></div>

            <div className="MoreInfo">
              <div className="label">Total</div>
              <div className="value2">LBP {parseInt(getLandlineDisplayedData.TotalAmount).toLocaleString()}</div>
            </div>

          </div>

          <div className="footSectionPick">
            <button onClick={handleConfirmPay} disabled={isButtonDisabled}>Confirm & Pay</button>
          </div>
        </div>}


    </div>
  );
};

export default MyBill;
