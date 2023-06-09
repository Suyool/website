import React from "react";

const BouquetOptions = ({ setShowBouquet }) => {


    return (
        <div className="PickYourGrid">
            <div className="topSectionPick">
                <div className="titles">
                    <div className="titleGrid">Bouquet Options</div>
                    <button onClick={() => { setShowBouquet(false) }}>Cancel</button>
                </div>
            </div>
            <div className="bodySectionPick">
                {/* {ballNumbers.map((number) => (
                    <div className="ballCont" key={number}>
                        <button onClick={() => handleBallClick(number)}>
                            <span>{number}</span>
                        </button>
                    </div>
                ))} */}

                <div className="bouquetList">

                    <div className="bouquetItem">
                        <div className="checkbox"><input type="checkbox" /></div>
                        <div className="data">
                            <div className="basic">25 basic grids</div>
                            <div className="price">5,000,000 LBP</div>
                        </div>
                    </div>

                    <div className="bouquetItem">
                        <div className="checkbox"><input type="checkbox" /></div>
                        <div className="data">
                            <div className="basic">50 basic grids</div>
                            <div className="price">10,000,000 LBP</div>
                        </div>
                    </div>
                    <div className="bouquetItem">
                        <div className="checkbox"><input type="checkbox" /></div>
                        <div className="data">
                            <div className="basic">100 basics grids</div>
                            <div className="price">20,000,000 LBP</div>
                        </div>
                    </div>
                    <div className="bouquetItem">
                        <div className="checkbox"><input type="checkbox" /></div>
                        <div className="data">
                            <div className="basic">500 basics grids</div>
                            <div className="price">100,000,000 LBP</div>
                        </div>
                    </div>
                    <div className="bouquetItem">
                        <div className="checkbox"><input type="checkbox" /></div>
                        <div className="data">
                            <div className="basic">500 other</div>
                            <div className="price"></div>
                        </div>
                    </div>

                </div>
            </div>
            <div className="footSectionPick">
                <button className="ContinueBtn" onClick={() => { setShowBouquet(false) }}>Continue</button>
            </div>
        </div>
    );
};

export default BouquetOptions;

