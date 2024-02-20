import React, { useEffect, useState } from "react";
import Modal from "react-bootstrap/Modal";
const ErrorModal = (props) => {
  const [ getAnth, setAnth ] = useState("");
  const handleExchange = () => {
    if (props.getErrorModal.path == "84") {
      let object = [
        {
          exchange: {
            flag: props.getErrorModal.path,
            url: window.location.href,
          },
        },
      ];
      if (props.parameters?.deviceType === "Android") {
        window.AndroidInterface.callbackHandler(JSON.stringify(object));
      } else if (props.parameters?.deviceType === "Iphone") {
        // const message = "data";
        window.webkit.messageHandlers.callbackHandler.postMessage(object);
      }
    }
    if (props.getErrorModal.path == "90") {
      let object = [
        {
          topup: {
            flag: props.getErrorModal.path,
            url: window.location.href,
          },
        },
      ];
      setAnth(JSON.stringify(object));
      if (props.parameters?.deviceType === "Android") {
        setAnth("android");
        window.AndroidInterface.callbackHandler(JSON.stringify(object));
      } else if (props.parameters?.deviceType === "Iphone") {
        // const message = "data";
        window.webkit.messageHandlers.callbackHandler.postMessage(object);
      }
    }
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
        <div id="ErrorModal">
          <img src={props.getErrorModal.img} alt="flag" />
          <div className="title">{props.getErrorModal.title}</div>
          <div className="desc">{props.getErrorModal.desc}</div>
          <div className="buttonsDesign">
            {props.getErrorModal.btn == "OK" && (
              <button className="exchangeBtnModal" onClick={props.onHide}>
                {props.getErrorModal.btn}
              </button>
            )}
            {props.getErrorModal.btn != "OK" && (
              <>
                <button className="okiBtnModal" onClick={props.onHide}>
                  Cancel
                </button>
              </>
            )}
          </div>
        </div>
      </Modal.Body>
    </Modal>
  );
};
export default ErrorModal;
