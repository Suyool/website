import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";

const Topup = () => {
  const dispatch = useDispatch();
  const amount = useSelector((state) => state.appData.StoredData.amount);
  const bottomSlider = useSelector((state) => state.appData.bottomSlider);
  const mobileResponse = useSelector((state) => state.appData.mobileResponse);

  const [gethidden,sethidden]= useState(false)
  const { Topup } = AppAPI();
    useEffect(()=>{
        dispatch(settingData({field:"headerData",value:{
            title:"WinDSL Topup",
            backLink:"",
            currentPage:"Topup"
        }}))
    },[])

    const handleInputChange = (event) => {
      const amount = event.target.value
      dispatch(settingObjectData({mainField:'StoredData',field:'amount',value: amount }))
    }

    const onSubmit = (e) => {
      e.preventDefault();
      dispatch(
        settingData({
          field: "bottomSlider",
          value: {
            isShow: true,
            name: "SliderTopup",
            backPage: "",
            data: {
              
            },
            isButtonDisable: false,
          },
        })
      );
        sethidden(true)
      console.log(amount)
    }

    useEffect(() => {
      if (mobileResponse == "success") {
        Topup( { amount , currency: "USD" } )
      } else if (mobileResponse == "failed") {
        dispatch(settingData({ field: "isloading", value: false }));
        dispatch(settingObjectData({ mainField: "bottomSlider", field: "isButtonDisable", value: false }));
        dispatch(settingData({ field: "mobileResponse", value: "" }));
      }
    },[mobileResponse]);
    
    
  return (
    <div id="Default" style={{ opacity: bottomSlider?.isShow ? "0.4" : "" }}>
      <div className="topup">
        <form>
        <div className="MainTitle">How much do you want to top up?</div>
        {/* <input type="number" className="number" name="number" placeholder="$0.00" value={<sup>$</sup>} onChange={handleInputChange}/> */}
        <div className="input-wrapper">
            <sup className="superscript">$</sup>
            <input
              type="number"
              className="number"
              name="number"
              placeholder="0.00"
              value={amount}
              onChange={handleInputChange}
            />
          </div>
        <div className="button">
            <button type="submit" className="btnsubmit" id={amount.length === 0 ? 'hidden' : ''} disabled={bottomSlider?.isShow || amount.length === 0} onClick={onSubmit}>Continue</button>
        </div>
        </form>
      </div>
    </div>
  );
};

export default Topup;
