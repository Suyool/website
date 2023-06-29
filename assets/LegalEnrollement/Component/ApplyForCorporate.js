import React from "react";

const ApplyForCorporate = () => {
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




                    <div className="title">CONTACT PERSON</div>
                    <div className="label"></div>
                    <input className="input" placeholder="First & last name" />

                    <div className="label">Phone Number</div>
                    <input className="input" placeholder="+961" />

                    <div className="label">Email</div>
                    <input className="input" placeholder="name@name.com" />

                    <div className="btnSection">
                        <button className="ApplyBtn">Apply</button>
                    </div>
                </div>

            </div>
        </div>
    );
};

export default ApplyForCorporate;