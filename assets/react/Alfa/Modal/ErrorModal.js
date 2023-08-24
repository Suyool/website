import React, { useEffect, useState } from "react";
import Modal from "react-bootstrap/Modal";
const ErrorModal = (props) => {
  
  const handleExchange = () => {
    // window.location.href = "/app?f="+props.getErrorModal.path;
    let object = [
      {
        flag: props.getErrorModal.path,
        url: window.location.href,
      },
    ];
    if (props.parameters?.deviceType === "Android") {
      window.AndroidInterface.callbackHandler(object);
    } else if (props.parameters?.deviceType === "Iphone") {
      // const message = "data";
      window.webkit.messageHandlers.callbackHandler.postMessage(object);
    }
  };
  console.log(object);

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
            <button className="okiBtnModal" onClick={props.onHide}>
              Cancel
            </button>
            {props.getErrorModal.btn && (
              <button
                className="exchangeBtnModal"
                onClick={handleExchange}
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