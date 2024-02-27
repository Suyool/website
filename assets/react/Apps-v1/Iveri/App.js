import axios from "axios";
import React, { useEffect, useState } from "react";
import Success from "./Modal/Success";
import Error from "./Modal/Error";

const App = ({ parameters }) => {
  const [getModalName, setModalName] = useState("");
  const [modalShow, setModalShow] = useState(false);
  const [getSuccessModal, setSuccessModal] = useState({
    imgPath: "",
    title: "",
    desc: "",
  });
  const [getErrorModal, setErrorModal] = useState({
    img: "",
    title: "",
    desc: "",
    path: "",
  });
  useEffect(() => {
    if (parameters.status) {
      setModalName("Success");
      setModalShow(true);
      setSuccessModal({
        imgPath: "/build/images/Loto/success.png",
        title: "Top Up Successful",
        desc: (
          <div>
            Your wallet has been topped up with {parameters.currency} {parameters.amount}. Check your new
            balance
          </div>
        ),
      });
    } else {
      setModalName("Error");
      setModalShow(true);
      setSuccessModal({
        imgPath: "/build/images/Loto/error.png",
        title: "Top Up Failed",
        desc: (
          <div>
            An error has occurred with your top up. Please try again later or
            use another top up method.
          </div>
        ),
      });
    }
  }, []);

  return (
    <>
      {getModalName === "Success" && (
        <Success
          getSuccessModal={getSuccessModal}
          show={modalShow}
          onHide={() => {
            setModalShow(false);
            setModalName("");
          }}
        />
      )}
      {getModalName === "Error" && (
        <Error
          getSuccessModal={getSuccessModal}
          show={modalShow}
          onHide={() => {
            setModalShow(false);
            setModalName("");
          }}
        />
      )}
    </>
  );
};

export default App;
