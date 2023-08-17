import React from "react";
import Modal from 'react-bootstrap/Modal';

function MyVerticallyCenteredModal(props) {
    return (
        <Modal
            {...props}
            size="md"
            aria-labelledby="contained-modal-title-vcenter"
            centered
        >
            <Modal.Body>
                <div id="legalModle">
                    <img src="/build/images/warning.png" alt="warrning" />
                    <div className="title">{props.title}</div>
                    <div className="description">{props.description}</div>
                    <button className="okiBtnModal" onClick={props.onHide}>ok</button>
                </div>
            </Modal.Body>
        </Modal>
    );
}
export default MyVerticallyCenteredModal;