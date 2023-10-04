import React, { useEffect, useState } from "react";
import Modal from "react-bootstrap/Modal";

const SuccessModal = (props) => {

  const goToPlay = () => {
    if(props.getSuccessModal.deviceType === "Android"){
      window.AndroidInterface.callbackHandler("GoToApp");
    }else if(props.getSuccessModal.deviceType === "Iphone"){
      window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
    }
    props.onHide();
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
        <div id="SuccessModal">
          <img src={props.getSuccessModal.imgPath} alt="flag" />
          <div className="title">{props.getSuccessModal.title}</div>
          <div className="desc">{props.getSuccessModal.desc}</div>
          <button className="okiBtnModal" onClick={() => goToPlay()}>
            OK
          </button>
        </div>
      </Modal.Body>
    </Modal>
  );
};

export default SuccessModal;
