import React from "react";
import { useDispatch } from "react-redux";
import useAxiosClient from "../Utils/axios";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const AppAPI = () => {
  const dispatch = useDispatch();
  const axiosClient = useAxiosClient();

  const GetAllAvailableCountries = () => {
    dispatch(settingData({ field: "isLoadingData", value: "default" }));

    try {
      return axiosClient.post(`/getAllAvailableCountries`).then((response) => {
        if (response.data.status == true) {
          dispatch(settingObjectData({ mainField: "simlyData", field: "AvailableCountries", value: response.data.message }));
        }
        dispatch(settingData({ field: "isLoadingData", value: false }));
      });
    } catch (e) {
      dispatch(settingData({ field: "isLoadingData", value: false }));
    }
  };

  const GetLocalAvailableCountries = () => {
    dispatch(settingData({ field: "isLoadingData", value: "default" }));

    try {
      return axiosClient.post(`/getLocalAvailableCountries`).then((response) => {
        if (response.data.status == true) {
          dispatch(settingObjectData({ mainField: "simlyData", field: "AvailableCountriesLocal", value: response.data.message }));
        }
        dispatch(settingData({ field: "isLoadingData", value: false }));
      });
    } catch (e) {
      dispatch(settingData({ field: "isLoadingData", value: false }));
    }
  };

  const GetPlansUsingISOCode = (isoCode) => {
    dispatch(settingData({ field: "isLoadingData", value: "default" }));

    try {
      return axiosClient.get(`/getPlansUsingISOCode?code=${isoCode}`).then((response) => {
        if (response.data.status == true) {
          dispatch(settingObjectData({ mainField: "headerData", field: "backLink", value: "Packages" }));
          dispatch(settingObjectData({ mainField: "simlyData", field: "SelectedCountry", value: response.data.message }));
          dispatch(settingObjectData({ mainField: "simlyData", field: "isPackageItem", value: true }));
        }
        dispatch(settingData({ field: "isLoadingData", value: false }));
      });
    } catch (e) {
      dispatch(settingData({ field: "isLoadingData", value: false }));
    }
  };

  const PurchaseTopup = (selectedPackage, selectedPlan) => {
    dispatch(settingData({ field: "isloading", value: true }));

    try {
      return axiosClient
        .post(`/purchaseTopup`, {
          planId: selectedPackage.planId,
          country: selectedPlan.name,
          countryImage: selectedPlan.countryImageURL,
          parentPlanType: localStorage.getItem("parentPlanType"),
          isoCode: selectedPlan.isoCode,
        })
        .then((response) => {
          dispatch(settingData({ field: "isloading", value: false }));
          const jsonResponse = response.data.message;
          if (response.data.status) {
            dispatch(settingObjectData({ mainField: "simlyData", field: "esimId", value: response.data.data.id }));
            dispatch(
              settingData({
                field: "modalData",
                value: {
                  isShow: true,
                  name: "SuccessModal",
                  img: "/build/images/alfa/SuccessImg.png",
                  title: "eSIM Payment Successful",
                  desc: (
                    <div>
                      You have successfully topped up the {selectedPackage?.offre ? `${selectedPackage.initial_price_free}` : `$ ${selectedPackage.initial_price}`} {selectedPlan.name} eSIM.
                    </div>
                  ),
                  btn: "Install eSIM",
                  flag: "",
                },
              })
            );
          } else if (!response.data.status && response.data.flagCode == 10) {
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
          } else if (!response.data.status && response.data.flagCode == 11) {
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
        });
    } catch (e) {
      dispatch(settingData({ field: "isloading", value: false }));
    }
  };

  const GetNetworksById = (plan) => {
    try {
      return axiosClient.get(`/getNetworksById?planId=${plan}`).then((response) => {
        if (response.data.status == true) {
          dispatch(
            settingData({
              field: "bottomSlider",
              value: {
                isShow: true,
                name: "availableNetworks",
                backPage: "",
                data: {
                  networks: response.data?.message,
                },
                isButtonDisable: false,
              },
            })
          );
        }
      });
    } catch (e) {}
  };

  const GetCountriesById = (country) => {
    try {
      return axiosClient.get(`/getContientAvailableByCountry?country=${country}`).then((response) => {
        if (response.data.status == true) {
          dispatch(
            settingData({
              field: "bottomSlider",
              value: {
                isShow: true,
                name: "availableCountries",
                backPage: "",
                data: {
                  countryInfo: response.data?.message,
                },
                isButtonDisable: false,
              },
            })
          );
        }
      });
    } catch (e) {}
  };

  const GetUsageOfEsim = () => {
    dispatch(settingData({ field: "isLoadingData", value: true }));
    try {
      return axiosClient.post("/getUsageOfEsim").then((response) => {
        dispatch(settingData({ field: "isLoadingData", value: false }));
        dispatch(settingObjectData({ mainField: "simlyData", field: "mapData", value: true }));
        dispatch(settingObjectData({ mainField: "simlyData", field: "accountInformation", value: response.data.message }));
      });
    } catch (e) {
      dispatch(settingData({ field: "isLoadingData", value: false }));
    }
  };

  const PurchaseTopupEsim = (reqObj) => {
    dispatch(settingData({ field: "isloading", value: true }));

    try {
      return axiosClient.post(`/purchaseTopup`, reqObj).then((response) => {
        const jsonResponse = response.data.message;
        dispatch(settingData({ field: "isloading", value: false }));
        if (response.data.status) {
          dispatch(
            settingData({
              field: "modalData",
              value: {
                isShow: true,
                name: "SuccessModal",
                img: "/build/images/alfa/SuccessImg.png",
                title: "eSIM Payment Successful",
                desc: <div>You have successfully Topup the eSIM Account.</div>,
                btn: "OK",
                flag: "",
              },
            })
          );
        } else if (!response.data.status && response.data.flagCode == 10) {
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
        } else if (!response.data.status && response.data.flagCode == 11) {
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
      });
    } catch (e) {
      dispatch(settingData({ field: "isloading", value: false }));
    }
  };

  const GetEsimDetails = (esimId) => {
    try{
      return axiosClient.post(`/GetEsimDetails`,{
        esimId: esimId,
      }).then((response) => {
        dispatch(settingData({ field: "planDetail", value: response.data.message }));
      })
    }catch(e){}
  }

  const GetOffres = () => {
    try{
      return axiosClient.get(`/getOffres`).then((response) => {
        dispatch(settingData({ field: "offre", value: response.data.message }));
      })
    }catch(e){}
  }

  return {
    GetAllAvailableCountries,
    GetLocalAvailableCountries,
    GetPlansUsingISOCode,
    PurchaseTopup,
    GetNetworksById,
    GetCountriesById,
    GetUsageOfEsim,
    PurchaseTopupEsim,
    GetEsimDetails,
    GetOffres
  };
};

export default AppAPI;
