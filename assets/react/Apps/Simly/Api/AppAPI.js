import { useDispatch } from "react-redux";
import useAxiosClient from "../Utils/axios";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";

const AppAPI = () => {
  const dispatch = useDispatch();
  const axiosClient = useAxiosClient();

  const GetAllAvailableCountries = () => {
    dispatch(settingData({ field: "isLoadingData", value: "default" }));

    try {
      return axiosClient
        .post(`/getAllAvailableCountries`)
        .then((response) => {
          if(response.data.status == true){
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
      return axiosClient
        .post(`/getLocalAvailableCountries`)
        .then((response) => {
          if(response.data.status == true){
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
      return axiosClient
      .get(`/simly/getPlansUsingISOCode?code=${isoCode}`)
        .then((response) => {
          if(response.data.status == true){
            dispatch(settingObjectData({ mainField: "simlyData", field: "SelectedCountry", value: response.data.message }));
            dispatch(settingObjectData({ mainField: "simlyData", field: "isPackageItem", value: true }));
          }
          dispatch(settingData({ field: "isLoadingData", value: false }));

        });
    } catch (e) {
      dispatch(settingData({ field: "isLoadingData", value: false }));
    }
  };

  return {
    GetAllAvailableCountries,
    GetLocalAvailableCountries,
    GetPlansUsingISOCode,
  };
};

export default AppAPI;
