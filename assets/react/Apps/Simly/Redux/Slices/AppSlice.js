import { createSlice } from "@reduxjs/toolkit";

const initialState = {
  isloading: "idle",
  isLoadingData: "idle",
  parameters: null,
  mobileResponse: "",
  headerData: {
    title: "Simly",
    backLink: "",
    currentPage: "Packages",
  },
  modalData: {
    isShow: false,
    name: "",
    img: "",
    title: "",
    desc: "",
    btn: null,
    flag: "",
  },
  bottomSlider: {
    isShow: false,
    name: "default",
    backPage: "",
    data: {},
    isButtonDisable: false,
  },
  simlyData: {
    isPackageItem: false,
    AvailableCountries: null,
    AvailableCountriesLocal: null,
    SelectedCountry: null,
    SelectedPlan: null,
    SelectedPackage: null,
    esimId: null,
    mapData: null,
    accountInformation : null,
    eSimDetail : null
  },
};

const AppSlice = createSlice({
  name: "appPage",
  initialState,
  reducers: {
    settingData: (state, action) => {
      const { field, value } = action.payload;
      state[field] = value;
    },

    settingObjectData: (state, action) => {
      const { mainField, field, value } = action.payload;
      state[mainField][field] = value;
    },

    resetData: (state) => {
      state.isloading = "idle";
      state.mobileResponse = "";
      state.headerData = { title: "Alfa", backLink: "", currentPage: "" };
      state.modalData = { isShow: false, name: "", img: "", title: "", desc: "", btn: null, flag: "" };
      state.bottomSlider = { isShow: false, name: "", data: {} };
    },
  },
});

export const { settingData, settingObjectData, resetData } = AppSlice.actions;

export default AppSlice.reducer;
