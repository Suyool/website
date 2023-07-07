import React, { useEffect, useState } from "react";

const PayBill = () => {

  return (
    <div id="PayBill">
      <div className="mainTitle">Enter your phone number to recharge</div>

      <div className="MobileNbContainer mt-3">
        <div className="place">
          <img src="/build/images/Alfa/flag.png" alt="flag" />
          <div className="code">+961</div>
        </div>
        <input className="nbInput" placeholder="|" />
      </div>

      <div className="pCurrency">
        <div className="subTitle">My Payment Currency</div>
        <div className="currencies">
          <div className="activeCurrency">USD</div>
          <div className="Currency">LBP</div>
        </div>
      </div>

      <button id="ContinueBtn" className="btnCont">Continue</button>
    </div>
  );
};

export default PayBill;
