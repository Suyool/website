import {useDispatch, useSelector} from "react-redux";
import useAxiosClient from "../Utils/axios";
import {settingData, settingObjectData} from "../Redux/Slices/AppSlice";

const AppAPI = () => {
    const dispatch = useDispatch();
    const axiosClient = useAxiosClient();

    const {planData, bundle, identifier, requestId} = useSelector((state) => state.appData);

    const GetBundles = (service, identifier, credential) => {
        dispatch(settingData({field: "isloading", value: true}));
        try {
            return axiosClient
                .post(`/bundles`, {
                    service: service,
                    identifier: identifier,
                })
                .then((response) => {
                    dispatch(settingData({field: "isloading", value: false}));
                    if (response?.data[0]) {
                        dispatch(settingData({field: "isloading", value: false}));

                        dispatch(
                            settingData({
                                field: "sodetelData",
                                value: response?.data[1]
                            })
                        );
                        dispatch(settingData({field: "requestId", value: response?.data[2]}));
                        dispatch(settingObjectData({mainField: "headerData", field: "currentPage", value: "ReCharge"}));
                    } else if (response?.data?.message === "Maximum allowed number of PIN requests is reached") {
                        dispatch(settingData({field: "isloading", value: false}));
                        dispatch(settingData({
                            field: "modalData",
                            value: {
                                isShow: true,
                                name: "ErrorModal",
                                img: "/build/images/alfa/error.png",
                                title: " PIN Tries Exceeded",
                                desc: (
                                    <div>You have exceeded the allowed PIN requests.<br/> Kindly try again later</div>),
                                btn: "OK",
                                flag: "",
                            },
                        }));
                    } else if (
                        response?.data?.message === "Not Enough Balance Amount to be paid"
                    ) {
                        dispatch(settingData({field: "isloading", value: false}));
                        dispatch(settingData({
                            field: "modalData",
                            value: {
                                isShow: true,
                                name: "ErrorModal",
                                img: "/build/images/alfa/error.png",
                                title: "Not Enough Balance Amount to be paid",
                                desc: (<div>No enough balance to pay the bill</div>),
                                btn: "OK",
                                flag: "",
                            },
                        }));
                    } else {
                        dispatch(settingData({field: "isloading", value: false}));
                        dispatch(settingData({
                            field: "modalData",
                            value: {
                                isShow: true,
                                name: "ErrorModal",
                                img: "/build/images/alfa/error.png",
                                title: credential.label + " Not Found ",
                                desc: (
                                    <div>The {credential.label} you entered was not found in the system.<br/> Kindly try
                                        another number.</div>),
                                btn: "OK",
                                flag: "",
                            },
                        }));
                    }
                });
        } catch (e) {
            dispatch(settingData({field: "isloading", value: false}));
            console.log(e);
        }
    };

    const Recharge = () => {
        dispatch(settingData({field: "isloading", value: true}));
        try {
            return axiosClient
                .post("/refill", {
                    refillData: planData,
                    bundle: bundle,
                    identifier: identifier,
                    requestId: requestId,
                })
                .then((response) => {
                    dispatch(settingData({field: "isloading", value: false}));
                    if (response?.data.IsSuccess) {
                        dispatch(settingData({field: "isloading", value: false}));
                        dispatch(
                            settingData({
                                field: "bottomSlider",
                                value: {
                                    isShow: true,
                                    name: "successSlider",
                                    backPage: "ReCharge",
                                    data: response?.data[1],
                                    isButtonDisable: false,
                                },
                            })
                        );
                    } else {
                        if (
                            response.data?.IsSuccess === false &&
                            (response.data?.flagCode === 10 || response.data.flagCode === 11)
                        ) {
                            const message = JSON.parse(response.data?.message);
                            dispatch(settingData({
                                field: "modalData",
                                value: {
                                    name: "ErrorModal",
                                    img: "/build/images/alfa/error.png",
                                    title: message.Title,
                                    desc: message.SubTitle,
                                    show: true,
                                    btn: message?.ButtonOne?.Text,
                                    flag: message?.ButtonOne.Flag,
                                },
                            }));
                        } else if (
                            !response.data.IsSuccess &&
                            response.data.flagCode === 11
                        ) {
                            dispatch(settingData({
                                field: "modalData",
                                value: {
                                    name: "ErrorModal",
                                    img: "/build/images/alfa/error.png",
                                    title: response.Title,
                                    desc: response.SubTitle,
                                    show: true,
                                    btn: response.ButtonOne.Text,
                                    flag: response.ButtonOne.Flag,
                                },
                            }));
                        } else if (!response.data.IsSuccess &&
                            response.data.data === -1) {
                            dispatch(settingData({
                                field: "modalData",
                                value: {
                                    name: "ErrorModal",
                                    img: "/build/images/alfa/error.png",
                                    title: "Recharge Card Unavailable ",
                                    desc: `The ${planData?.plandescription} Sodetel Recharge Service is unavailable.`,
                                    show: true,
                                    btn: "OK",
                                },
                            }));
                        } else {
                            console.log(response.data);
                            dispatch(settingData({
                                field: "modalData",
                                value: {
                                    name: "ErrorModal",
                                    img: "/build/images/alfa/error.png",
                                    title: "Please Try again",
                                    desc: "You cannot purchase this product now",
                                    show: true,
                                    btn: "OK",
                                },
                            }));
                        }
                    }
                })
                .finally((error) => {
                    dispatch(settingData({field: "isloading", value: false}));
                    console.log(error);
                });
        } catch (e) {
            dispatch(settingData({field: "isloading", value: false}));
            console.log(e);
        }
    }

    return {GetBundles, Recharge};
};

export default AppAPI;
