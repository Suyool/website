import {useDispatch} from "react-redux";
import useAxiosClient from "../Utils/axios";
import {settingData, settingObjectData} from "../Redux/Slices/AppSlice";
import React from "react";

const AppAPI = () => {
    const dispatch = useDispatch();
    const axiosClient = useAxiosClient();

    //Get Accounts
    const getAccounts = (formattedNumber) => {
        dispatch(settingData({field: "isloading", value: true}));
        try {
            return axiosClient
                .post("/get_accounts", {
                    username: formattedNumber
                })
                .then((response) => {
                    dispatch(settingData({field: "isloading", value: false}));
                    if (response.data.flag === 2) {

                        dispatch(
                            settingData({
                                field: "modalData",
                                value: {
                                    isShow: true,
                                    name: "ErrorModal",
                                    img: "/build/images/alfa/error.png",
                                    title: 'Error',
                                    desc: 'The number you entered was not found in the system.<br>Kindly try another number.',
                                    btn: 'OK',
                                    flag: response.data.flag,
                                },
                            })
                        );

                    } else {
                        const data = response.data.return
                        // Proceed with the normal flow

                        dispatch(settingData({
                            field: "landlineForm", value: data
                        }));
                        dispatch(settingData({
                            field: "headerData",
                            value: {title: "Landline", backLink: "LandlineForm", currentPage: "ReCharge"}
                        }));

                    }
                })

        } catch (e) {
            dispatch(settingData({field: "isloading", value: false}));
            console.log(e);
        }
    };

    const payBills = (productId, type, price) => {
        dispatch(settingData({field: "isloading", value: true}));
        try {
            return axiosClient
                .post("/refill_customer_terranet", {
                    productId: productId,
                    accountType: type,
                })
                .then((response) => {
                    const jsonResponse = response?.data?.message;
                    if (response.data?.IsSuccess) {
                        dispatch(
                            settingData({
                                field: "modalData",
                                value: {
                                    isShow: true,
                                    name: "SuccessModal",
                                    img: "/build/images/alfa/SuccessImg.png",
                                    title: "TerraNet Bill Paid Successfully",
                                    desc: `You have successfully paid your TerraNet bill of L.L ${" "} ${parseInt(price).toLocaleString()}.`,
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
            dispatch(settingData({field: "isloading", value: false}));
            console.log(e);
        }
    }

    return {
        getAccounts,
        payBills,
    };
};

export default AppAPI;
