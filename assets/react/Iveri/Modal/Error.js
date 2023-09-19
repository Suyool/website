import React, { useEffect, useState } from "react";
import Modal from "react-bootstrap/Modal";

const Error = (props) => {

  return (
    <Modal
      {...props}
      size="md"
      aria-labelledby="contained-modal-title-vcenter"
      centered
      id="modalRadius"
    >
      <Modal.Body>
        <div id="ErrorModal">
          <img src={props.getErrorModal.img} alt="flag" />
          <div className="title">{props.getErrorModal.title}</div>
          <div className="desc">{props.getErrorModal.desc}</div>
          <div className="buttonsDesign">
            <button
              className="okiBtnModal"
            >
              Try again
            </button>
          </div>
        </div>
      </Modal.Body>
    </Modal>
  );
};

export default Error;
