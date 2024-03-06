import React, { useEffect } from "react";
import { useDispatch } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";

const Login = () => {
  const dispatch = useDispatch();
    useEffect(()=>{
        dispatch(settingData({field:"headerData",value:{
            title:"WinDSL Topup",
            backLink:"",
            currentPage:"Login"
        }}))
    },[])
  return (
    <div id="Default">
      <div className="MainTitle">Write your account credentials to top up:</div>
    </div>
  );
};

export default Login;
