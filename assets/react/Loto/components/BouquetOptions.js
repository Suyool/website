import React, { useState } from "react";

const BouquetOptions = ({
  setShowBouquet,
  setIsHide,
  getBouquetgridprice,
  setActiveButton,
}) => {
  const [selectedOption, setSelectedOption] = useState("");
  const isContinueDisabled = !selectedOption;
  const handleOptionSelect = (option) => {
    if (option != selectedOption) {
      setSelectedOption(option);
    } else {
      setSelectedOption("");
    }
  };
  const handleContinue = () => {
    if (
      !selectedOption.gridNb == 0 ||
      !selectedOption.gridNb == null ||
      !selectedOption.gridNb > 500
    ) {
      const bouquetData = {
        bouquet: "B" + selectedOption.gridNb,
        price: selectedOption.price,
        currency: "LBP",
        withZeed: false,
        isbouquet: true,
      };

      const existingData = localStorage.getItem("selectedBalls");

      if (existingData) {
        const newData = [...JSON.parse(existingData), bouquetData];
        localStorage.setItem("selectedBalls", JSON.stringify(newData));
      } else {
        localStorage.setItem("selectedBalls", JSON.stringify([bouquetData]));
      }

      setShowBouquet(false);
      setIsHide(false);
      setActiveButton({ name: "Play" });
    }
  };
  return (
    <div className="PickYourBoucket">
      <div className="thePickGridCont">
        <div className="topSectionPick">
          <div className="brBoucket"></div>
          <div className="titles">
            <div className="titleGrid">Bouquet Options</div>
            <button
              onClick={() => {
                setShowBouquet(false);
                setIsHide(false);
              }}
            >
              Cancel
            </button>
          </div>
        </div>
        <div className="bodySectionPick">
          <div className="bouquetList">
            <div className="bouquetItem">
              <div className="checkbox">
                <input
                  type="radio"
                  name="radio"
                  onChange={() =>
                    handleOptionSelect({
                      gridNb: 25,
                      price: 25 * getBouquetgridprice,
                    })
                  }
                />
              </div>
              <div className="data">
                <div className="basic">25 basic grids</div>
                <div className="price">
                  {parseInt(25 * getBouquetgridprice).toLocaleString()} LBP
                </div>
              </div>
            </div>

            <div className="bouquetItem">
              <div className="checkbox">
                <input
                  type="radio"
                  name="radio"
                  onChange={() =>
                    handleOptionSelect({
                      gridNb: 50,
                      price: 50 * getBouquetgridprice,
                    })
                  }
                />
              </div>
              <div className="data">
                <div className="basic">50 basic grids</div>
                <div className="price">
                  {parseInt(50 * getBouquetgridprice).toLocaleString()} LBP
                </div>
              </div>
            </div>
            <div className="bouquetItem">
              <div className="checkbox">
                <input
                  type="radio"
                  name="radio"
                  onChange={() =>
                    handleOptionSelect({
                      gridNb: 100,
                      price: 100 * getBouquetgridprice,
                    })
                  }
                />
              </div>
              <div className="data">
                <div className="basic">100 basics grids</div>
                <div className="price">
                  {parseInt(100 * getBouquetgridprice).toLocaleString()} LBP
                </div>
              </div>
            </div>
            <div className="bouquetItem">
              <div className="checkbox">
                <input
                  type="radio"
                  name="radio"
                  onChange={() =>
                    handleOptionSelect({
                      gridNb: 500,
                      price: 500 * getBouquetgridprice,
                    })
                  }
                />
              </div>
              <div className="data">
                <div className="basic">500 basics grids</div>
                <div className="price">
                  {parseInt(500 * getBouquetgridprice).toLocaleString()} LBP
                </div>
              </div>
            </div>

            <div className="bouquetItem">
              <div className="checkbox">
                <input
                  type="radio"
                  name="radio"
                  onChange={() =>
                    handleOptionSelect({
                      gridNb: 0,
                      price: 0 * getBouquetgridprice,
                    })
                  }
                />
              </div>
              <div className="data">
                <div className="basic">
                  <input
                    type="number"
                    name="selectedOption"
                    onChange={(event) =>
                      handleOptionSelect({
                        gridNb: event.target.value,
                        price: event.target.value * getBouquetgridprice,
                      })
                    }
                  />
                  Other
                </div>
              </div>
            </div>
          </div>
        </div>
        <div className="footSectionPick">
          <button
            className="ContinueBtn"
            disabled={isContinueDisabled}
            onClick={handleContinue}
          >
            Continue
          </button>
        </div>
      </div>
    </div>
  );
};

export default BouquetOptions;
