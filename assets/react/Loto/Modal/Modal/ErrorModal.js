import React, { useEffect, useState } from "react";
import Modal from "react-bootstrap/Modal";

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
          <img src={props.getErrorModal.img} alt="flag" />
          <div className="title">{props.getErrorModal.title}</div>
          <div className="desc">{props.getErrorModal.desc}</div>
          <div className="buttonsDesign">
            <button className="okiBtnModal" onClick={props.onHide}>
              Cancel
            </button>
            {props.getErrorModal.btn && (
              <button
                className="exchangeBtnModal"
                onClick={(event) =>
                  (window.location.href = `/app?f=${props.getErrorModal.path}`)
                }
              >
                {props.getErrorModal.btn}
              </button>
            )}
          </div>
        </div>
      </Modal.Body>
    </Modal>
  );
};

export default ErrorModal;
