import { createSlice } from "@reduxjs/toolkit";

const initialState = {
  isloading: "idle",
  parameters: null,
  mobileResponse: "",
  headerData: {
    title: "Alfa",
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

  postpaidData: {
    id: "",
  },

  prepaidData: {
    vouchers: null,
    prepaidVoucher: {},
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
      state.postpaidData = { id: "" };
      state.prepaidData = { vouchers: null, prepaidVoucher: {} };
    },
  },
});

export const { settingData, settingObjectData, resetData } = AppSlice.actions;

export default AppSlice.reducer;
