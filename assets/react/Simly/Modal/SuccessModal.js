import React from "react";
import Modal from "react-bootstrap/Modal";
import QrDownload from "../../Components/QrDownload";

const SuccessModal = (props) => {
    const [qr, setQr] = React.useState(null);

    // const handleDownload = async (e) => {
    //     try {
    //         const result = await fetch(props.getSuccessModal.qr, {
    //             method: "GET",
    //             headers: {},
    //         });
    //         const blob = await result.blob();
    //         const url = URL.createObjectURL(blob);
    //
    //         const link = document.createElement("a");
    //         link.href = url;
    //         link.download = props.getSuccessModal.qr;
    //         link.click();
    //     } catch (error) {
    //         console.error(error);
    //     }
    // };

    const handleDownload = () => {
        // const result = await fetch(props.getSuccessModal.qrImg, {
        //     method: "GET",
        //     headers: {},
        // });

        console.log(props.getSuccessModal.qrImg)
        setQr(props.getSuccessModal?.qrImg)
    }

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
                        <img src={props.getSuccessModal.imgPath} alt="flag"/>
                        <div className="title">{props.getSuccessModal.title}</div>
                        <div className="desc">{props.getSuccessModal.desc}</div>
                        {/* <button className="okiBtnModal" onClick={() => goToPlay()}>
            OK
          </button> */}
                        <button onClick={handleDownload} type="button" className="okiBtnModal">
                            {/* <a href={props.getSuccessModal.qr} download={props.getSuccessModal.qr}> */}
                            Download
                            {/* </a> */}
                        </button>
                        {props.getSuccessModal.btn}
                    </div>
                </Modal.Body>
            </Modal>
            <QrDownload qrCodeData={qr} setQrData={setQr}/>
        </>
    );
};

export default SuccessModal;
