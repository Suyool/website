import React from "react";
import ReCAPTCHA from "react-google-recaptcha";

const Footer = ({ handleFormSubmit }) => {
    const onChange = (value) => {
        console.log("Captch value : ", value);
    };

    return (
        <div className="btnSection">
            {/* <ReCAPTCHA
                sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"
                // sitekey="6LcdHeImAAAAAMt93GnsjXSu4aoTX0fAXtRWyTxp"
                onChange={onChange}
            /> */}
            <button className="ApplyBtn" onClick={handleFormSubmit}>Apply</button>
        </div>
    );
};

export default Footer;