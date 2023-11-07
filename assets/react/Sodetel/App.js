import React, {useState} from 'react';
import Default from "./components/Default";
import PayBill from "../Alfa/PayBill";

function App({parameters}) {
    const [activeButton, setActiveButton] = useState({name: ""});
    const [getBackLink, setBackLink] = useState({name: ""});
    const [getHeaderTitle, setHeaderTitle] = useState("Sodetel");

    const [getPostpaidData, setPostpaidData] = useState({ id: "" });

    //Modal Variable
    const [modalShow, setModalShow] = useState(false);
    const [modalDesc, setModalDesc] = useState({
        name: "",
        imgPath: "/build/images/alfa/SuccessImg.png",
        title: "Success Modal",
        desc: "Success Modal",
    });

    console.log(parameters)

    return (
        <div>
            {activeButton.name === "" &&
                <Default setActiveButton={setActiveButton} setBackLink={setBackLink} setHeaderTitle={setHeaderTitle}/>
            }

            {activeButton.name === "PayBill" && (
                <PayBill
                    setPostpaidData={setPostpaidData}
                    setModalShow={setModalShow}
                    setModalDesc={setModalDesc}
                    activeButton={activeButton}
                    setActiveButton={setActiveButton}
                    setHeaderTitle={setHeaderTitle}
                    setBackLink={setBackLink}
                />
            )}


        </div>
    );
}

export default App;