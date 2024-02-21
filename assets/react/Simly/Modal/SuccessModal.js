import React from "react";
import Modal from "react-bootstrap/Modal";
import QrDownload from "../../Components/QrDownload";

const SuccessModal = (props) => {
    const { setActiveButton } = props; // Destructure setActiveButton from props
    const [qr, setQr] = React.useState(null);

  const handleDownload = () => {
    // const result = await fetch(props.getSuccessModal.qrImg, {
    //     method: "GET",
    //     headers: {},
    // });

    console.log(props.getSuccessModal.qrImg);
    setQr(props.getSuccessModal?.qrImg);
  };

  const goToPlay = () => {
    if (props.getSuccessModal.deviceType === "Android") {
      window.AndroidInterface.callbackHandler("GoToApp");
    } else if (props.getSuccessModal.deviceType === "Iphone") {
      window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
    }
    props.onHide();
  };

  return (
    <>
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
            <button className="okiBtnModal" onClick={props.getSuccessModal.btn === "OK" ? goToPlay() : props.onsetActiveButton} style={{backgroundColor:"#02ADFF",color:"#fff"}}>
              {props.getSuccessModal.btn}
            </button>
          </div>
        </Modal.Body>
      </Modal>
    </>
  );
};

export default SuccessModal;
