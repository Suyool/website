import React, { useEffect, useState } from "react";
import Modal from 'react-bootstrap/Modal';

const ErrorModal = (props) => {

    return (
        <Modal
            {...props}
            size="md"
            aria-labelledby="contained-modal-title-vcenter"
            centered
        >
            <Modal.Body>
                <div id="ErrorModal">
                    <img src={props.getSuccessModal.imgPath} alt="flag" />
                    <div className="title">{props.getSuccessModal.title}</div>
                    <div className="desc">{props.getSuccessModal.desc}</div>
                </div>
            </Modal.Body>
        </Modal>
    );
};

export default ErrorModal;
