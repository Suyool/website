import React, { useEffect, useState } from "react";
import Modal from 'react-bootstrap/Modal';

const SuccessModal = (props) => {


    return (
        <Modal
            {...props}
            size="md"
            aria-labelledby="contained-modal-title-vcenter"
            centered
        >
            <Modal.Body>
                <div id="SuccessModal">
                    <img src={props.getSuccessModal.imgPath} alt="flag" />
                    <div className="title">{props.getSuccessModal.title}</div>
                    <div className="desc">{props.getSuccessModal.desc}</div>
                    <button className="okiBtnModal" onClick={(event) =>
                  (window.location.href = `/loto?goto=Play`)
                }>OK</button>
                
                </div>
            </Modal.Body>
        </Modal>
    );
};

export default SuccessModal;
