import React from 'react';
import {useDispatch} from "react-redux";
import useAxiosClient from "../Utils/axios";
import {settingData, settingObjectData} from "../Redux/Slices/AppSlice";

const AppAPI = () => {
    const dispatch = useDispatch();
    const axiosClient = useAxiosClient();

    const fetchProducts = (TypeID,PlanType) => {
        dispatch(settingData({field: "isloading", value: true}));

        try {
            return axiosClient
                .get(`/productsInternet/${TypeID}/${PlanType}`)
                .then((response) => {
                    dispatch(settingData({field: "isloading", value: false}));

                    if (response?.data?.status) {
                        const parsedData = response?.data?.Payload;
                        dispatch(settingData({field: "products", value: parsedData}));
                    }
                })
        } catch (e) {
            dispatch(settingData({field: "isloading", value: false}));
            console.log(e);
        }
    };


    const pay = ({productId}) => {

        dispatch(settingData({field: "isloading", value: true}));
        try {
            return axiosClient
                .post("/product/pay", {
                    productId: `${productId}`,
                    type: "internet"
                })
                .then((response) => {
                    const jsonResponse = response?.data?.message;
                    dispatch(settingData({field: "SerialToClipboard", value: response?.data?.data?.data?.serialCode}));

                    dispatch(settingData({field: "isloading", value: false}));
                    if (response.data?.IsSuccess) {
                        var TotalAmount = parseInt(response.data?.data.amount) + parseInt(response.data?.data.fees);
                        dispatch(settingData({
                            field: "bottomSlider", value: {
                                isShow: true,
                                name: "PaymentDoneG2G",
                                backPage: "",
                                data: response?.data?.data,
                                isButtonDisable: false,
                            }
                        }));

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
    };

    return {
        pay,
        fetchProducts,
    };
};

export default AppAPI;
