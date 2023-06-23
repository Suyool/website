import React from "react";

const BouquetOptions = ({ setShowBouquet, setIsHide }) => {


    return (
        <div className="PickYourBoucket">
            <div className="topSectionPick">
                <div className="brBoucket"></div>
                <div className="titles">
                    <div className="titleGrid">Bouquet Options</div>
                    <button onClick={() => { setShowBouquet(false); setIsHide(false) }}>Cancel</button>
                </div>
            </div>
            <div className="bodySectionPick">

                <div className="bouquetList">

                    <div className="bouquetItem">
                        <div className="checkbox"><input type="radio" name="radio" /></div>
                        <div className="data">
                            <div className="basic">25 basic grids</div>
                            <div className="price">5,000,000 LBP</div>
                        </div>
                    </div>

                    <div className="bouquetItem">
                        <div className="checkbox"><input type="radio" name="radio" /></div>
                        <div className="data">
                            <div className="basic">50 basic grids</div>
                            <div className="price">10,000,000 LBP</div>
                        </div>
                    </div>
                    <div className="bouquetItem">
                        <div className="checkbox"><input type="radio" name="radio" /></div>
                        <div className="data">
                            <div className="basic">100 basics grids</div>
                            <div className="price">20,000,000 LBP</div>
                        </div>
                    </div>
                    <div className="bouquetItem">
                        <div className="checkbox"><input type="radio" name="radio" /></div>
                        <div className="data">
                            <div className="basic">500 basics grids</div>
                            <div className="price">100,000,000 LBP</div>
                        </div>
                    </div>
                    <div className="bouquetItem">
                        <div className="checkbox"><input type="radio" name="radio" /></div>
                        <div className="data">
                            <div className="basic">500 other</div>
                            <div className="price"></div>
                        </div>
                    </div>

                </div>
            </div>
            <div className="footSectionPick">
                <button className="ContinueBtn" onClick={() => { setShowBouquet(false); setIsHide(false) }}>Continue</button>
            </div>
        </div>
    );
};

export default BouquetOptions;

