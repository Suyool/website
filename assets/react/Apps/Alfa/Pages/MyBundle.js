import axios from "axios";
import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { settingData } from "../Redux/Slices/AppSlice";

const MyBundle = () => {
  const dispatch = useDispatch();
  const parameters = useSelector((state) => state.appData.parameters);
  const mobileResponse = useSelector((state) => state.appData.mobileResponse);
  const getPrepaidVoucher = useSelector((state) => state.appData.prepaidData.prepaidVoucher);

  const [isButtonDisabled, setIsButtonDisabled] = useState(false);

  useEffect(() => {
    dispatch(settingData({ field: "headerData", value: { title: "Re-charge Alfa", backLink: "ReCharge", currentPage: "MyBundle" } }));
    setIsButtonDisabled(false);
  }, []);

  const handleConfirmPay = () => {
    dispatch(settingData({ field: "isloading", value: true }));
    setIsButtonDisabled(true);
    if (parameters?.deviceType === "Android") {
      setTimeout(() => {
        window.AndroidInterface.callbackHandler("message");
      }, 2000);
    } else if (parameters?.deviceType === "Iphone") {
      setTimeout(() => {
        window.webkit.messageHandlers.callbackHandler.postMessage("fingerprint");
      }, 2000);
    }
  };

  useEffect(() => {
    if (mobileResponse == "success") {
      axios
        .post("/alfa/BuyPrePaid", {
          Token: "",
          category: "ALFA",
          desc: getPrepaidVoucher.desc,
          type: getPrepaidVoucher.vouchertype,
          amountLBP: getPrepaidVoucher.priceLBP,
          amountUSD: getPrepaidVoucher.priceUSD,
        })
        .then((response) => {
          dispatch(settingData({ field: "isloading", value: false }));
          const jsonResponse = response?.data?.message;
          if (response?.data.IsSuccess) {
            dispatch(
              settingData({
                field: "bottomSlider",
                value: {
                  isShow: true,
                  name: "successPrepaidSlider",
                  backPage: "MyBundle",
                  data: {
                    voucherCode: response?.data?.data?.voucherCode,
                    voucherCodeClipboard: "*14*" + response?.data?.data?.voucherCode + "#",
                    priceUSD: getPrepaidVoucher.priceUSD,
                  },
                  isButtonDisable: false,
                },
              })
            );
          } else {
            console.log(response.data.flagCode);
            if (response.data.IsSuccess == false && response.data.flagCode == 10) {
              dispatch(
                settingData({
                  field: "modalData",
                  value: {
                    isShow: true,
                    name: "ErrorModal",
                    img: "/build/images/alfa/error.png",
                    title: jsonResponse.Title,
                    desc: jsonResponse.SubTitle,
                    btn: jsonResponse.ButtonOne.Text,
                    flag: jsonResponse.ButtonOne.Flag,
                  },
                })
              );
            } else if (!response.data.IsSuccess && response.data.flagCode == 11) {
              dispatch(
                settingData({
                  field: "modalData",
                  value: {
                    isShow: true,
                    name: "ErrorModal",
                    img: "/build/images/alfa/error.png",
                    title: jsonResponse.Title,
                    desc: jsonResponse.SubTitle,
                    btn: jsonResponse.ButtonOne.Text,
                    flag: jsonResponse.ButtonOne.Flag,
                  },
                })
              );
            } else if (jsonResponse == "19") {
              dispatch(
                settingData({
                  field: "modalData",
                  value: {
                    isShow: true,
                    name: "ErrorModal",
                    img: "/build/images/alfa/error.png",
                    title: "Recharge Card Unavailable ",
                    desc: `The ${getPrepaidVoucher.priceUSD}$ Alfa Recharge card is unavailable. 
                    Kindly choose another one.`,
                    btn: "OK",
                    flag: "",
                  },
                })
              );
            } else if (!response.data.IsSuccess && response.data.flagCode == 210) {
              dispatch(
                settingData({
                  field: "modalData",
                  value: {
                    isShow: true,
                    name: "ErrorModal",
                    img: "/build/images/alfa/error.png",
                    title: response.data.Title,
                    desc: response.data.message,
                    btn: "OK",
                    flag: "",
                  },
                })
              );
            } else {
              dispatch(
                settingData({
                  field: "modalData",
                  value: {
                    isShow: true,
                    name: "ErrorModal",
                    img: "/build/images/alfa/error.png",
                    title: "Please Try again",
                    desc: "You cannot purchase now",
                    btn: "OK",
                    flag: "",
                  },
                })
              );
            }
          }
        })
        .catch((error) => {
          dispatch(settingData({ field: "isloading", value: false }));
        });
    } else if (mobileResponse == "failed") {
      dispatch(settingData({ field: "isloading", value: false }));
      setIsButtonDisabled(false);
      dispatch(settingData({ field: "mobileResponse", value: "" }));
    }
  }, [mobileResponse]);

  return (
    <>
      <div id="MyBundle">
        <div className="MyBundleBody">
          <div className="mainTitle">{getPrepaidVoucher.desc1}</div>
          {/* <div className="mainDesc">*All taxes excluded</div> */}
          <img className="BundleBigImg" src={`/build/images/alfa/Bundle${getPrepaidVoucher.vouchertype}h.png`} alt="Bundle" />
          <div className="smlDesc">
            <img className="question" src={`/build/images/alfa/attention.svg`} alt="question" style={{ verticalAlign: "baseline" }} />
            &nbsp; Alfa only accepts payments in L.L
          </div>
          {/* <div className="relatedInfo">{getPrepaidVoucher.desc2}</div> */}
          <div className="MoreInfo">
            <div className="label">Total before taxes</div>
            <div className="value">$ {getPrepaidVoucher.beforeTaxes}</div>
          </div>
          <div className="MoreInfo">
            <div className="label">+V.A.T & Stamp Duty</div>
            <div className="value">$ {getPrepaidVoucher.fees}</div>
          </div>
          <div className="br"></div>
          <div className="MoreInfo">
            <div className="label">Total after taxes</div>
            <div className="value">$ {getPrepaidVoucher.priceUSD}</div>
          </div>
          <div className="MoreInfo">
            <div className="label">Amount in L.L</div>
            <div className="value">L.L {parseInt(getPrepaidVoucher.priceLBP).toLocaleString()}</div>
          </div>
          <div className="br"></div>
          <div className="MoreInfo">
            <div className="label">Total amount to pay</div>
            <div className="value1">L.L {parseInt(getPrepaidVoucher.priceLBP).toLocaleString()}</div>
          </div>
          <div className="smlDescSayrafa">$1 = {parseInt(getPrepaidVoucher.sayrafa).toLocaleString()} L.L (Subject to change).</div>
        </div>

        <button id="ContinueBtn" className="btnCont" onClick={handleConfirmPay} disabled={isButtonDisabled}>
          Pay Now
        </button>
      </div>
    </>
  );
};

export default MyBundle;
