import React from 'react';
import { useDispatch } from "react-redux";
import useAxiosClient from "../Utils/axios";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const AppAPI = () => {
  const dispatch = useDispatch();
  const axiosClient = useAxiosClient();

  //Get Phone Bill
  const Login = ({ username, password }) => {
    dispatch(settingData({ field: "isloading", value: true }));

    try {
      return axiosClient
        .post(`/login`, {
          username: username,
          password: password,
        })
        .then((response) => {
          dispatch(settingData({ field: "isloading", value: false }));

          if (response?.data?.isSuccess) {
            dispatch(settingData({ field: "headerData", value: { title: "WinDSL Topup", backLink: "", currentPage: "Topup" } }));
            dispatch(settingObjectData({mainField:"StoredData",field:"username",value:username}))
            dispatch(settingObjectData({mainField:"StoredData",field:"oldBalance",value:response?.data?.balance}))
          } else {
            dispatch(
              settingData({
                field: "modalData",
                value: {
                  isShow: true,
                  name: "ErrorModal",
                  img: "/build/images/alfa/error.png",
                  title: "User Not Found",
                  desc: `<div>
                          User not found in the system
                          <br />
                          Kindly try another username.
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

  const Topup = ({ amount, currency }) => {
    dispatch(settingData({ field: "isloading", value: true }));

    try {
      return axiosClient
        .post(`/topup`, {
          amount :amount,
          currency : currency
        })
        .then((response) => {
          const jsonResponse = response?.data?.message;

          dispatch(settingData({ field: "isloading", value: false }));
          if (response.data?.isSuccess) {
            dispatch(
              settingData({
                field: "modalData",
                value: {
                  isShow: true,
                  name: "SuccessModal",
                  img: "/build/images/alfa/SuccessImg.png",
                  title: "Win DSL Top Up Successful",
                  desc: `You have successfully topped up your Win DSL account with ${currency === "USD" ? "$" : "LL"} ${amount}`,
                  btn: null,
                  flag: "",
                },
              })
            );
          } else {
            if (response.data.isSuccess == false && response.data.flagCode == 10) {
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
            } else if (!response.data.isSuccess && response.data.flagCode == 11) {
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
    Login,
    Topup
  };
};

export default AppAPI;
