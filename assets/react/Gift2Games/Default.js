import React, {useEffect, useState} from "react";
import axios from "axios";

const Default = ({
  SetVoucherData,
  setActiveButton,
  setHeaderTitle,
  setBackLink,
}) => {

  const [categories, setCategories] = useState([]);

  useEffect(() => {
    setHeaderTitle("Gift2Games");
    setBackLink("default");
    fetchCategories();
  }, []);

  const fetchCategories = () => {
    axios
      .get("/gift2games/categories")
      .then((response) => {
        console.log("response", response)
        if (response?.data?.status)
        setCategories(JSON.parse(response?.data?.Payload)?.data);
      })
      .catch((error) => {
        console.log(error);
      });
  }

    console.log("categories",categories)

  const handleButtonClick = (name, category) => {
    setActiveButton({ name: name, category: category });
  };

  return (
    <div id="Default_g2g">
      <div className="MainTitle">What do you want to do?</div>

      <div className="categories">
        {
          categories.map(({id, title}) => (
              <div className="Cards" onClick={()=>{
                handleButtonClick("Products", id);
              }}>
                <img
                    className="logoImg"
                    src="/build/images/g2g/g2g-logo.png"
                    alt="gift2gamesLogo"
                />
                <div className="Text">
                  <div className="SubTitle">{title}</div>
                </div>
              </div>
          ))
        }
      </div>

      {/*<div*/}
      {/*  className="Cards"*/}
      {/*  onClick={() => {*/}
      {/*    handleButtonClick("PayBill");*/}
      {/*  }}*/}
      {/*>*/}
      {/*  <img*/}
      {/*    className="logoImg"*/}
      {/*    src="/build/images/g2g/g2g-logo.png"*/}
      {/*    alt="gift2gamesLogo"*/}
      {/*  />*/}
      {/*  <div className="Text">*/}
      {/*    <div className="SubTitle">Pay Mobile Bills</div>*/}
      {/*    <div className="description">*/}
      {/*      Settle your Alfa bill quickly and securely*/}
      {/*    </div>*/}
      {/*  </div>*/}
      {/*</div>*/}

      {/*<div*/}
      {/*  className="Cards"*/}
      {/*  onClick={() => {*/}
      {/*    handleButtonClick("ReCharge");*/}

      {/*    axios*/}
      {/*      .post("/alfa/ReCharge")*/}
      {/*      .then((response) => {*/}
      {/*        SetVoucherData(response?.data?.message);*/}
      {/*      })*/}
      {/*      .catch((error) => {*/}
      {/*        console.log(error);*/}
      {/*      });*/}
      {/*  }}*/}
      {/*>*/}
      {/*  <img*/}
      {/*    className="logoImg"*/}
      {/*    src="/build/images/g2g/g2g-logo.png"*/}
      {/*    alt="gift2gamesLogo"*/}
      {/*  />*/}
      {/*  <div className="Text">*/}
      {/*    <div className="SubTitle">Re-charge Alfa</div>*/}
      {/*    <div className="description">Recharge your Alfa prepaid number</div>*/}
      {/*  </div>*/}
      {/*</div>*/}


    </div>
  );
};

export default Default;
