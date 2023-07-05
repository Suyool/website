import React, { useState, useEffect } from "react";

const SuccessfullySent = ({ }) => {
    return (
        <>
            <div className="SuccessfullySent">
                <div className="SuccessfullySentCont">
                    <img className="addImgs" src="/build/images/approved.png" alt="approved" />
                    <div className="approvedTitle">Application successfully sent</div>
                    <div className="approvedDesc">Once the information is validated, you will receive an email to complete your registration.</div>
                </div>
            </div>
        </>
    );
};

export default SuccessfullySent;