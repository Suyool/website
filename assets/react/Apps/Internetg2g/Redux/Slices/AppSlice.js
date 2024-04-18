import {createSlice} from "@reduxjs/toolkit";

const initialState = {
    isloading: "idle",
    isloadingData: "idle",
    typeID: "",
    headerTitle: "",
    providerName: "",
    parameters: null,
    mobileResponse: "",
    headerData: {
        title: "Gift2Games",
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

    categories: {},

    childCategories: {},

    products: {},

    productInfo: {},
};

const AppSlice = createSlice({
    name: "appPage",
    initialState,
    reducers: {
        settingData: (state, action) => {
            const {field, value} = action.payload;
            state[field] = value;
        },

        settingObjectData: (state, action) => {
            const {mainField, field, value} = action.payload;
            state[mainField][field] = value;
        },

        resetData: (state) => {
            state.isloading = "idle";
            state.isloadingData = "idle";
            state.mobileResponse = "";
            state.headerData = {title: "Gift2Games", backLink: "", currentPage: ""};
            state.modalData = {isShow: false, name: "", img: "", title: "", desc: "", btn: null, flag: ""};
            state.bottomSlider = {isShow: false, name: "", data: {}};
            state.categories = {};
            state.childCategories = {};
            state.products = {};
            state.productInfo = {};
            state.headerTitle = "";
            state.providerName = "";

        },
    },
});

export const {settingData, settingObjectData, resetData} = AppSlice.actions;

export default AppSlice.reducer;
