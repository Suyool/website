import React, { useEffect, useState, useRef } from "react";
import axios from "axios";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const MyBill = () => {
  const dispatch = useDispatch();
  const parameters = useSelector((state) => state.appData.parameters);
  const getPostpaidData = useSelector((state) => state.appData.postpaidData);
  const mobileResponse = useSelector((state) => state.appData.mobileResponse);

  const PinLength = 4;

  const [pinCode, setPinCode] = useState([]);
  const [getResponseId, setResponseId] = useState(null);
  const [getPinWrong, setPinWrong] = useState(false);
  const [getBtnDesign, setBtnDesign] = useState(false);

  const inputRef = useRef(null);

  useEffect(() => {
    dispatch(
      settingData({
        field: "headerData",
        value: {
          title: "Pay Mobile Bill",
          backLink: "PayBill",
          currentPage: "MyBill",
        },
      })
    );
    dispatch(settingObjectData({ mainField: "bottomSlider", field: "isButtonDisable", value: false }));
  }, []);

  const handlePincodeClick = () => {
    inputRef.current.focus();
  };

  const handleInputChange = (event) => {
    const inputValue = event.target.value;
    const newPinCode = inputValue.slice(0, PinLength).split("");
    setPinCode(newPinCode);
  };

  const handlePayNow = () => {
    if (pinCode.length === PinLength) {
      dispatch(settingData({ field: "isloading", value: true }));
      axios
        .post("/alfa/bill/RetrieveResults", {
          mobileNumber: localStorage.getItem("billMobileNumber").replace(/\s/g, ""),
          currency: "LBP",
          Pin: pinCode,
          invoicesId: getPostpaidData.id,
        })
        .then((response) => {
          console.log(response);
          if (response.data.message == "connected") {
            dispatch(settingData({ field: "isloading", value: false }));

            dispatch(
              settingData({
                field: "bottomSlider",
                value: {
                  isShow: true,
                  name: "successPostpaidSlider",
                  backPage: "MyBill",
                  data: {
                    displayData: response?.data?.displayData,
                    displayedFees: response?.data?.displayedFees,
                  },
                  isButtonDisable: false,
                },
              })
            );
            setResponseId(response?.data?.postpayed);
          } else if (response.data.message == "213") {
            setPinWrong(true);
            setPinCode("");
            dispatch(settingData({ field: "isloading", value: false }));
          } else {
            dispatch(
              settingData({
                field: "modalData",
                value: {
                  isShow: true,
                  name: "ErrorModal",
                  img: "/build/images/alfa/error.png",
                  title: "No Available Bill",
                  desc: `There is no available bill for ${localStorage.getItem("billMobileNumber")} at the moment.
                Kindly try again later. `,
                  btn: "OK",
                  flag: "",
                },
              })
            );
            setPinCode("");
          }
        })
        .catch((error) => {
          console.log(error);
        });
    }
    setBtnDesign(false);
  };

  const handleConfirmPay = () => {
    dispatch(settingObjectData({ mainField: "bottomSlider", field: "isButtonDisable", value: true }));
    dispatch(settingData({ field: "isloading", value: true }));

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
        .post("/alfa/bill/pay", {
          ResponseId: getResponseId,
        })
        .then((response) => {
          const jsonResponse = response?.data?.message;
          dispatch(settingData({ field: "isloading", value: false }));
          if (response.data?.IsSuccess) {
            var TotalAmount = parseInt(response.data?.data.amount) + parseInt(response.data?.data.fees);
            dispatch(
              settingData({
                field: "modalData",
                value: {
                  isShow: true,
                  name: "SuccessModal",
                  img: "/build/images/alfa/SuccessImg.png",
                  title: "Alfa Bill Paid Successfully",
                  desc: `You have successfully paid your Alfa bill of L.L ${" "} ${parseInt(TotalAmount).toLocaleString()}.`,
                  btn: null,
                  flag: "",
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
            } else {
              dispatch(
                settingData({
                  field: "modalData",
                  value: {
                    isShow: true,
                    name: "ErrorModal",
                    img: "/build/images/alfa/error.png",
                    title: "Please Try again",
                    desc: `You cannot purchase now`,
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

      dispatch(settingObjectData({ mainField: "bottomSlider", field: "isButtonDisable", value: false }));
      dispatch(settingData({ field: "mobileResponse", value: "" }));
    }
  });

  const handleInputFocus = () => {
    setBtnDesign(true);
  };

  return (
    <>
      <div id="MyBill">
        <div className="mainTitle">Insert the PIN you have received by SMS</div>

        <div className="PinSection" onClick={handlePincodeClick}>
          <div className="Pintitle">PIN</div>
          <div className="Pincode">
            {Array.from({ length: PinLength }, (_, index) => (
              <div className="code" key={index}>
                {pinCode[index] !== undefined ? pinCode[index] : ""}
              </div>
            ))}
            <input
              ref={inputRef}
              type="text"
              value={pinCode ? pinCode.join("") : ""}
              onChange={handleInputChange}
              onFocus={handleInputFocus}
              // onBlur={handleInputBlur}
              style={{ opacity: 0, position: "absolute", left: "-10000px" }}
            />
          </div>
        </div>

        <div className={`${!getBtnDesign ? "continueSection" : "continueSectionFocused"}`}>
          <button id="ContinueBtn" className="btnCont" onClick={handlePayNow} disabled={pinCode.length !== PinLength}>
            Continue
          </button>
          {getPinWrong && <p style={{ color: "red" }}>Unable to proceed, kindly try again.</p>}
        </div>
      </div>
    </>
  );
};

export default MyBill;
