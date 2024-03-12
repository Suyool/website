import React, { useEffect, useState } from "react";
import { useDispatch } from "react-redux";
import { settingData, settingObjectData } from "../Redux/Slices/AppSlice";
import AppAPI from "../Api/AppAPI";

const Login = () => {
  const dispatch = useDispatch();
  const { Login } = AppAPI();
  useEffect(() => {
    dispatch(
      settingData({
        field: "headerData",
        value: {
          title: "WinDSL Topup",
          backLink: "",
          currentPage: "Login",
        },
      })
    );
  }, []);

  function onSubmit(e) {
    e.preventDefault();
    const username = document.getElementsByName("username")[0].value;
    const password = document.getElementsByName("password")[0].value;
    Login({username,password})
  }
  return (
    <div id="Default">
      <div className="header row align-items-center">
        <div className="col-3">
          <img className="logoImg" src="/build/images/windsl/windsl.png" alt="WinDSL Logo" />
        </div>
        <div className="col">
          <div className="headerTitle">
            <h5 className="titleMain">Top Up WinDSL Account</h5>
            <p className="headerDesc">Insert account credentials</p>
          </div>
        </div>
      </div>
      <div className="form">
        <form onSubmit={onSubmit}>
          <div className="MainTitle">Username or Phone Number</div>
          <input type="text" className="username" name="username" required />
          <div className="MainTitle">Password</div>
          <input
            type="password"
            className="password"
            name="password"
            required
          />
          <div className="button">
            <button type="submit" className="btnsubmit">
              Continue
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default Login;
