import React from 'react';
import {useDispatch} from "react-redux";
import useAxiosClient from "../Utils/axios";
import {settingData, settingObjectData} from "../Redux/Slices/AppSlice";

const AppAPI = () => {
    const dispatch = useDispatch();
    const axiosClient = useAxiosClient();

    const fetchCategories = (TypeID) => {
        dispatch(settingData({field: "isloading", value: true}));

        try {
            return axiosClient
                .get(`/categories/${TypeID}`)
                .then((response) => {
                    dispatch(settingData({field: "isloading", value: false}));

                    if (response?.data?.status) {
                        const parsedData = response?.data?.Payload;
                        dispatch(settingData({field: "categories", value: parsedData}));
                    }
                })
        } catch (e) {
            dispatch(settingData({field: "isloading", value: false}));
            console.log(e);
        }
    };

    const fetchChildCategories = (parentId) => {
        dispatch(settingData({field: "isloading", value: true}));

        try {
            return axiosClient
                .get(`/categories/${parentId}/childs`)
                .then((response) => {
                    dispatch(settingData({field: "isloading", value: false}));

                    if (response?.data?.status) {
                        const childCategories = response?.data?.Payload;
                        dispatch(settingData({field: "childCategories", value: childCategories}));
                    }
                })
        } catch (e) {
            dispatch(settingData({field: "isloading", value: false}));
            console.log(e);
        }
    };

    const fetchProducts = (activeSubCategoryId) => {

        dispatch(settingData({field: "isloadingData", value: true}));
        try {
            return axiosClient
                .get(`/products/${activeSubCategoryId}`)
                .then((response) => {
                    dispatch(settingData({field: "isloadingData", value: false}));

                    if (response?.data?.status) {
                        const productData = response?.data?.Payload;
                        dispatch(settingData({field: "products", value: productData}));
                    }
                })
        } catch (e) {
            dispatch(settingData({field: "isloadingData", value: false}));
            console.log(e);
        }
    };

    const pay = ({productId, title , displayPrice}) => {

        dispatch(settingData({field: "isloading", value: true}));
        try {
            return axiosClient
                .post("/product/pay", {
                    productId: `${productId}`,
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
                                    title: "Payment Successful",
                                    desc: `You have successfully purchased the ${title} for $
                ${displayPrice}.`,
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
    };

    return {
        pay,
        fetchCategories,
        fetchChildCategories,
        fetchProducts,
    };
};

export default AppAPI;
