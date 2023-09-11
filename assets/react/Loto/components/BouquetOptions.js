import React, { useState } from "react";

const BouquetOptions = ({
  setShowBouquet,
  setIsHide,
  getBouquetgridprice,
  setActiveButton,
}) => {
  const [ selectedOption, setSelectedOption ] = useState(""); // Track the selected bouquet option

  // const [bouquet, setBouquetOther] = useState(""); // Track the selected bouquet option

  // Enable/disable the Continue button based on the selected option
  const isContinueDisabled = !selectedOption;

  // Function to handle selecting a bouquet option
  const handleOptionSelect = (option) => {
    if (option != selectedOption) {
      setSelectedOption(option);
    } else {
      setSelectedOption("");
    }
  };

  // Function to handle continuing
  const handleContinue = () => {
    if (
      !selectedOption.gridNb == 0 ||
      !selectedOption.gridNb == null ||
      !selectedOption.gridNb > 500
    ) {
      // Add the selected bouquet option to the local storage
      const bouquetData = {
        bouquet: "B" + selectedOption.gridNb, // Use the gridNb property instead of balls
        price: selectedOption.price,
        currency: "LBP",
        withZeed: false,
        isbouquet: true,
      };

      // Get the existing data from local storage
      const existingData = localStorage.getItem("selectedBalls");

      if (existingData) {
        // Parse the existing data and add the new bouquet data
        const newData = [ ...JSON.parse(existingData), bouquetData ];
        localStorage.setItem("selectedBalls", JSON.stringify(newData));
      } else {
        // Create a new array with the bouquet data and store it in local storage
        localStorage.setItem("selectedBalls", JSON.stringify([ bouquetData ]));
      }

      // Continue with the desired actions
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
                    // value=""
                    onChange={(event) =>
                      handleOptionSelect({
                        gridNb: event.target.value,
                        price: event.target.value * getBouquetgridprice,
                      })
                    }
                  />
                  Other
                </div>
                {/* <div className="price">
                {parseInt(2 * getBouquetgridprice).toLocaleString()} LBP
              </div> */}
              </div>
            </div>

            {/* <div className="bouquetItem">
            <div className="checkbox">
              <input type="radio" name="radio" />
            </div>
            <div className="data">
              <div className="basic">500 other</div>
              <div className="price"></div>
            </div>
          </div> */}
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
