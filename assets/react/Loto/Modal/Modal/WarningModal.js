import React, { useEffect, useState } from "react";
import Modal from "react-bootstrap/Modal";

const WarningModal = (props) => {
  const handleWarning = () => {
    props.setModalShow(false);
    props.setModalName("");
    props.setActiveButton({ name: "Buy" });
  };
  return (
    <Modal
      {...props}
      size="md"
      aria-labelledby="contained-modal-title-vcenter"
      centered
      id="modalRadius"
    >
      <Modal.Body>
        <div id="WarningModal">
          <img src={props.getWarningModal.imgPath} alt="flag" />
          <div className="title">{props.getWarningModal.title}</div>
          <div className="desc">{props.getWarningModal.desc}</div>
          <div className="buttonsDesign">
            <button className="okiBtnModal" onClick={props.onHide}>
              Cancel
            </button>
            <button className="exchangeBtnModal" onClick={handleWarning}>
              {props.getWarningModal.btn}
            </button>
          </div>
        </div>
      </Modal.Body>
    </Modal>
  );
};

export default WarningModal;
