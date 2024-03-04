import { useDispatch } from "react-redux";
import useAxiosClient from "../Utils/axios";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const AppAPI = () => {
  const dispatch = useDispatch();
  const axiosClient = useAxiosClient();

  //Get Phone Bill
  const Bill = ({ mobileNumber }) => {
    dispatch(settingData({ field: "isloading", value: true }));

    try {
      return axiosClient
        .post(`/landline`, {
          mobileNumber: mobileNumber,
        })
        .then((response) => {
          dispatch(settingData({ field: "isloading", value: false }));

          if (response?.data?.LandlineReqId != -1) {
            dispatch(
              settingData({
                field: "bottomSlider",
                value: {
                  isShow: true,
                  name: "successLandlineSlider",
                  backPage: "PayBill",
                  data: {
                    landDataSlider: { id: response?.data?.LandlineReqId },
                    landDisplayDataSlider: response?.data?.message, 
                    landlineMobileSlider: response?.data?.mobileNb,
                  },
                  isButtonDisable: false,
                },
              })
            );
            // dispatch(settingData({ field: "headerData", value: { title: "Pay Mobile Bill", backLink: "", currentPage: "MyBill" } }));
            dispatch(settingData({ field: "landlineData", value: { landData: { id: response?.data?.LandlineReqId }, landDisplayData: response?.data?.message, landlineMobile: response?.data?.mobileNb } }));
          } else if (response?.data?.message == 111 || response?.data?.message == 108) {
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
                      There is no pending bill on the landline <br /> {mobileNumber}
                      <br />
                      Kindly try again later
                    </div>
                  ),
                  btn: "OK",
                  flag: "",
                },
              })
            );
          } else if (response?.data?.message == 113) {
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
                      The bill payment associated with {mobileNumber} can only be paid via Ogero
                      <br />
                      Please contact them for more information.
                    </div>
                  ),
                  btn: "OK",
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
                  desc: (
                    <div>
                      The number you entered was not found in the system.
                      <br />
                      Kindly try another number.
                    </div>
                  ),
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

  //Purchase Postpaid Bill
  const BillPay = (getResponseId) => {
    dispatch(settingData({ field: "isloading", value: true }));

    try {
      return axiosClient
        .post(`/landline/pay`, {
          LandlineId: getResponseId,
        })
        .then((response) => {
          const jsonResponse = response?.data?.message;
          dispatch(settingObjectData({ mainField: "landlineData", field: "displayedFees", value: response?.data?.displayedFees }));
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
                  title: "Ogero Landline Bill Paid Successfully",
                  desc: `You have successfully paid your Ogero Landline bill of L.L ${parseInt(TotalAmount).toLocaleString()}.`,
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

  return {
    Bill,
    BillPay,
  };
};

export default AppAPI;
