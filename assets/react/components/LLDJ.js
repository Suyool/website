import React, { useState } from "react";
import Countdown from "./Countdown";
import BouquetOptions from "./BouquetOptions";

const LLDJ = ({ parameters, setPickYourGrid, setTotalAmount, setBallNumbers }) => {

    // console.log(parameters);
    const [getShowBouquet, setShowBouquet] = useState(false);
    return (
        <>
            <div id="LLDJ">

                <div className="estimatedPriceSection mt-3">
                    <div className="title">Next Loto Estimated Jackpot</div>
                    <div className="priceLoto">LBP {parameters.next_loto_win}</div>
                    <img src="/build/images/Loto/LotoLogo.png" alt="SmileLOGO" />
                </div>

                <div className="estimatedPriceSection mt-5">
                    <div className="title">Next Zeed Estimated Jackpot</div>
                    <div className="priceZeed">LBP {parameters.next_zeed_win}</div>
                    <img src="/build/images/Loto/zeedLogo.png" alt="SmileLOGO" />
                </div>

                <div className="nextDraw m-4">
                    <div className="title">Next Draw #{parameters.next_draw_number}</div>
                    <div className="desc">{new Date(parameters.next_date).toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric', year: 'numeric' })}</div>
                    <Countdown nextDrawNumber={parameters.next_date} />
                </div>

                <div className="questionsSection mt-5">
                    <div className="title">What are you waiting for?</div>
                    <button className="PlayBtn">Play Now</button>
                </div>

                <div className="gridsSwction">
                    <div className="itemsSection">
                        <div className="items">
                            <div className="title">1 GRID</div>
                            <div className="price">{parameters.gridprice.grid1}LBP</div>
                            <button className="letsPlayBtn">PLAY NOW</button>
                        </div>

                        <div className="items redone">
                            <div className="image"><img src="/build/images/Loto/popular.png" alt="popular" /></div>
                            <div className="title">8 GRIDS</div>
                            <div className="price">{parameters.gridprice.grid8}LBP</div>
                            <button className="letsPlayBtn">PLAY NOW</button>
                        </div>

                        <div className="items">
                            <div className="title">BOUQUET</div>
                            <div className="price"></div>
                            <button className="letsPlayBtn" onClick={() => { setShowBouquet(true) }}>PLAY NOW</button>
                        </div>
                    </div>
                </div>

                <div className="directlyPlaySection mt-4">
                    <div className="bigTitle">Play directly by ball numbers</div>
                    <div className="itemsSection">
                        {parameters.gridpricematrix && parameters.gridpricematrix.map((item, index) =>
                            <div className="items" key={index}>
                                <div className="nb">{item.numbers}</div>
                                <div className="title">NUMBERS</div>
                                <div className="price">{item.price}LBP</div>
                                <button className="letsPlayBtn" onClick={() => {
                                    setBallNumbers(item.numbers);
                                    setTotalAmount(item.price);
                                    setPickYourGrid(true);
                                }}>PLAY</button>
                            </div>
                        )}
                    </div>
                </div>

            </div>
            {getShowBouquet && <BouquetOptions setShowBouquet={setShowBouquet} />}
        </>

    );
};

export default LLDJ;