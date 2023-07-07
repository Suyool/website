import React, { useEffect, useState } from "react";
import axios from "axios";

const MyBill = () => {

  const handlePayNow = () => {
    console.log("handlePayNow");

    axios
      .post("/alfa/bill/pay",
        {
          mobileNumber: mobileNumber
        }
      )
      .then((response) => {
        console.log(response);
      })
      .catch((error) => {
        console.log(error);
      });

  };
  return (
    <div id="MyBill">
      <div className="mainTitle">My Bill</div>

      <button id="ContinueBtn" className="btnCont" onClick={handlePayNow}>Pay Now</button>
    </div>
  );
};

export default MyBill;
