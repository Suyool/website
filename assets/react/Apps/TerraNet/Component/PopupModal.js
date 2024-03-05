import React from "react";
import Modal from "react-bootstrap/Modal";
import { useDispatch, useSelector } from "react-redux";
import { settingData } from "../Redux/Slices/AppSlice";

const PopupModal = () => {
  const dispatch = useDispatch();
  const modalData = useSelector((state) => state.appData.modalData);
  const parameters = useSelector((state) => state.appData.parameters);
  const headerData = useSelector((state) => state.appData.headerData);

  const onHide = () => {
    dispatch(
      settingData({
        field: "modalData",
        value: {
          isShow: false,
          name: "",
          img: "",
          title: "",
          desc: "",
          btn: null,
          flag: "",
        },
      })
    );
    dispatch(
      settingData({
        field: "headerData",
        value: {
          title: "Alfa",
          backLink: "",
          currentPage: headerData.currentPage,
        },
      })
    );
  };

  const handleExchange = () => {
    if (modalData.flag == "84") {
      let object = [
        {
          exchange: {
            flag: modalData.flag,
            url: window.location.href,
          },
        },
      ];
      if (parameters?.deviceType === "Android") {
        window.AndroidInterface.callbackHandler(JSON.stringify(object));
      } else if (parameters?.deviceType === "Iphone") {
        window.webkit.messageHandlers.callbackHandler.postMessage(object);
      }
    }
    if (modalData.flag == "90") {
      let object = [
        {
          topup: {
            flag: modalData.flag,
            url: window.location.href,
          },
        },
      ];
      if (parameters?.deviceType === "Android") {
        window.AndroidInterface.callbackHandler(JSON.stringify(object));
      } else if (parameters?.deviceType === "Iphone") {
        window.webkit.messageHandlers.callbackHandler.postMessage(object);
      }
    }
  };

  const goToPlay = () => {
    console.log(parameters);
    if (parameters?.deviceType === "Android") {
      window.AndroidInterface.callbackHandler("GoToApp");
    } else if (parameters?.deviceType === "Iphone") {
      window.webkit.messageHandlers.callbackHandler.postMessage("GoToApp");
    }
    // onHide();
  };

  return (
    <Modal show={modalData.isShow} size="md" aria-labelledby="contained-modal-title-vcenter" centered id="modalRadius">
      <Modal.Body>
        {modalData.name == "SuccessModal" && (
          <div id="SuccessModal">
            <img src={modalData.img} alt="flag" />
            <div className="title">{modalData.title}</div>
            <div className="desc">{modalData.desc}</div>
            <button className="okiBtnModal" onClick={() => goToPlay()}>
              OK
            </button>
          </div>
        )}
        {modalData.name == "ErrorModal" && (
          <div id="ErrorModal">
            <img src={modalData.img} alt="flag" />
            <div className="title">{modalData.title}</div>
            <div className="desc">{typeof modalData.desc === "string" ? <div dangerouslySetInnerHTML={{ __html: modalData.desc }} /> : modalData.desc}</div>
            <div className="buttonsDesign">
              {modalData.btn == "OK" && (
                <button
                  className="exchangeBtnModal"
                  onClick={() => {
                    onHide();
                  }}
                >
                  {modalData.btn}
                </button>
              )}
              {modalData.btn != "OK" && (
                <>
                  <button
                    className="okiBtnModal"
                    onClick={() => {
                      onHide();
                    }}
                  >
                    Cancel
                  </button>
                  <button className="exchangeBtnModal" onClick={handleExchange}>
                    {modalData.btn}
                  </button>
                </>
              )}
            </div>
          </div>
        )}
      </Modal.Body>
    </Modal>
  );
};

export default PopupModal;
