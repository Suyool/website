import React, { useState } from "react";
import ReCAPTCHA from "react-google-recaptcha";

const ApplyForCorporate = () => {
    const [getInfoShowing, setInfoShowing] = useState(false);
    const onChange = (value) => {
        console.log("Captch value : ", value);
    };

    return (
        <div className="ApplyForCorporate">
            <div className="CorporateCont">
                <div className="TopSection">Apply for Corporate Account</div>

                <div className="formCompany">
                    <div className="title">COMPANY INFORMATION</div>

                    <div className="label">Registered Name</div>
                    <input className="input" placeholder="Registered Name" />
                    <div className="label">Type of Business</div>
                    <input className="input" placeholder="Drop down + Others" />
                    <div className="label">Phone Number</div>
                    <input className="input" placeholder="+961" />
                    <div className="label">Email</div>
                    <input className="input" placeholder="name@name.com" />
                    <div className="address">
                        <div className="label">Address</div>
                        <img className="addImg" src="/build/images/pin.png" alt="Logo" />
                        <input className="addressinput" placeholder="Street, building, city, country" />
                    </div>
                    <div className={`row ${getInfoShowing && "mt-2"}`}>
                        <div className="col-4">
                            <div className="label">Person In charge (Authorized Signatory) <img className="addImg" src="/build/images/info.png" alt="Logo" onClick={() => { setInfoShowing(!getInfoShowing) }} /></div>
                            <input className="input" placeholder="Full Name" />
                        </div>
                        {getInfoShowing &&
                            <div className="col-4 infoCont mt-2">
                                <div className="titleInf">Authorized Signatory: </div>
                                <div className="desc">The person who is allowed to act on behalf of the company. Their name should be mentioned in the official records.</div>
                            </div>
                        }

                    </div>
                    <div className="label">Phone Number</div>
                    <input className="input" placeholder="+961" />



                    <div className="title">CONTACT PERSON</div>

                    <div className="label">Full Name</div>
                    <input className="input" placeholder="First & last name" />
                    <div className="label">Phone Number</div>
                    <input className="input" placeholder="+961" />
                    <div className="label">Email</div>
                    <input className="input" placeholder="name@name.com" />



                    <div className="btnSection">
                        <ReCAPTCHA
                            sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"
                            onChange={onChange}
                        />
                        <button className="ApplyBtn">Apply</button>
                    </div>
                </div>

            </div>
        </div>
    );
};

export default ApplyForCorporate;