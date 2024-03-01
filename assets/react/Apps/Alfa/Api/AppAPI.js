import { useDispatch } from "react-redux";
import useAxiosClient from "../Utils/axios";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const AppAPI = () => {
  const dispatch = useDispatch();
  const axiosClient = useAxiosClient();

  //Get Phone Bill
  const Bill = ({ mobileNumber, currency }) => {
    dispatch(settingData({ field: "isloading", value: true }));

    try {
      return axiosClient
        .post(`/bill`, {
          mobileNumber: mobileNumber,
          currency: currency,
        })
        .then((response) => {
          dispatch(settingData({ field: "isloading", value: false }));

          if (response?.data?.message == "connected") {
            dispatch(settingData({ field: "headerData", value: { title: "Pay Mobile Bill", backLink: "", currentPage: "MyBill" } }));
            dispatch(settingObjectData({ mainField: "postpaidData", field: "id", value: response?.data?.invoicesId }));
          } else if (response?.data?.message == "Maximum allowed number of PIN requests is reached") {
            dispatch(
              settingData({
                field: "modalData",
                value: {
                  isShow: true,
                  name: "ErrorModal",
                  img: "/build/images/alfa/error.png",
                  title: "PIN Tries Exceeded",
                  desc: (
                    <div>
                      You have exceeded the allowed PIN requests.
                      <br /> Kindly try again later
                    </div>
                  ),
                  btn: "OK",
                  flag: "",
                },
              })
            );
          } else if (response?.data?.message == "Not Enough Balance Amount to be paid") {
            dispatch(
              settingData({
                field: "modalData",
                value: {
                  isShow: true,
                  name: "ErrorModal",
                  img: "/build/images/alfa/error.png",
                  title: "No Pending Bill",
                  desc: (
                    <div>
                      There is no pending bill on the mobile number {localStorage.getItem("billMobileNumber")}
                      <br />
                      Kindly try again later
                    </div>
                  ),
                  btn: "OK",
                  flag: "",
                },
              })
            );
          } else if (response?.data?.message == "Internal Error") {
            dispatch(
              settingData({
                field: "modalData",
                value: {
                  isShow: true,
                  name: "ErrorModal",
                  img: "/build/images/alfa/error.png",
                  title: "Unable To Pay Your Bill",
                  desc: (
                    <div>
                      We were unable to process your transaction for the bill payment associated with {localStorage.getItem("billMobileNumber")} .
                      <br />
                      Kindly check your internet connection & try again
                    </div>
                  ),
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
                  title: "Number Not Found",
                  desc: `<div>
                          The number you entered was not found in the system.
                          <br />
                          Kindly try another number.
                        </div>`,
                  btn: "OK",
                },
              })
            );
          }
        });
    } catch (e) {
      dispatch(settingData({ field: "isloading", value: false }));
      console.log(e);
    }
  };

  //Get Phone Result Bill
  const BillRetrieveResult = ({ mobileNumber, currency, Pin, invoicesId }) => {
    dispatch(settingData({ field: "isloading", value: true }));
    try {
      return axiosClient
        .post(`/bill/RetrieveResults`, {
          mobileNumber: mobileNumber,
          currency: currency,
          Pin: Pin,
          invoicesId: invoicesId,
        })
        .then((response) => {
          dispatch(settingData({ field: "isloading", value: false }));
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
            dispatch(settingObjectData({ mainField: "postpaidData", field: "ResponseId", value: response?.data?.postpayed }));
          } else if (response.data.message == "213") {
            dispatch(settingObjectData({ mainField: "postpaidData", field: "pinCode", value: [] }));
            dispatch(settingObjectData({ mainField: "postpaidData", field: "isPinWrong", value: true }));
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
            dispatch(settingObjectData({ mainField: "postpaidData", field: pinCode, value: [] }));
          }
        });
    } catch (e) {
      dispatch(settingData({ field: "isloading", value: false }));
      console.log(e);
    }
  };

  //Purchase Postpaid Bill
  const BillPay = (getResponseId) => {
    dispatch(settingData({ field: "isloading", value: true }));

    try {
      return axiosClient
        .post(`/bill/pay`, {
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
        });
    } catch (e) {
      dispatch(settingData({ field: "isloading", value: false }));
      console.log(e);
    }
  };

  //Get Prepaid Vouchers
  const Recharge = () => {
    try {
      return axiosClient.post(`/ReCharge`).then((response) => {
        dispatch(settingObjectData({ mainField: "prepaidData", field: "vouchers", value: response?.data?.message }));
      });
    } catch (e) {
      console.log(e);
    }
  };

  //Purchase Prepaid Voucher
  const BuyPrePaid = (getPrepaidVoucher) => {
    dispatch(settingData({ field: "isloading", value: true }));

    try {
      return axiosClient
        .post(`/BuyPrePaid`, {
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
        });
    } catch (e) {
      dispatch(settingData({ field: "isloading", value: false }));
      console.log(e);
    }
  };

  return {
    Bill,
    BillRetrieveResult,
    BillPay,
    Recharge,
    BuyPrePaid,
  };
};

export default AppAPI;
