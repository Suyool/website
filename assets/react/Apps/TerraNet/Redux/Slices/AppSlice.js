import { createSlice } from "@reduxjs/toolkit";

const initialState = {
  isloading: "idle",
  parameters: null,
  mobileResponse: "",
  headerData: {
    title: "TerraNet",
    backLink: "",
    currentPage: "",
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

  landlineForm: {},

  productInfo: {
    productid: null,
    description: "",
    originalHT: null,
    imagePath: "",
    price: null
  }
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
      state.headerData = { title: "TerraNet", backLink: "", currentPage: "" };
      state.modalData = { isShow: false, name: "", img: "", title: "", desc: "", btn: null, flag: "" };
      state.bottomSlider = { isShow: false, name: "", data: {} };
      state.landlineForm = {};
      state.productInfo = {
        productid: null,
        description: "",
        originalHT: null,
        imagePath: "",
        price: null
      };
    },
  },
});

export const { settingData, settingObjectData, resetData } = AppSlice.actions;

export default AppSlice.reducer;
