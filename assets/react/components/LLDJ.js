import React from "react";
import Countdown from "./Countdown";

const LLDJ = ({ parameters }) => {

    console.log(parameters);
    return (
        <div className="LLDJ">

            <div className="estimatedPriceSection mt-3">
                <div className="title">Next Loto Estimated Jackpot</div>
                <div className="priceLoto">LBP {parameters.next_loto_win}</div>
                <img src="/build/images/Loto/LotoLogo.png" alt="SmileLOGO" />
            </div>

            <div className="estimatedPriceSection mt-3">
                <div className="title">Next Zeed Estimated Jackpot</div>
                <div className="priceZeed">LBP {parameters.next_zeed_win}</div>
                <img src="/build/images/Loto/zeedLogo.png" alt="SmileLOGO" />
            </div>

            <div className="nextDraw m-4">
                <div className="title">Next Draw #{parameters.next_draw_number}</div>
                <div className="desc">{new Date(parameters.next_date).toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric', year: 'numeric' })}</div>
                {/* <div className="timeSection">
                    <div className="items">
                        <div className="number">02</div>
                        <div className="date">DAYS</div>
                    </div>
                    <div className="items">
                        <div className="number">06</div>
                        <div className="date">HOURS</div>
                    </div>
                    <div className="items">
                        <div className="number">12</div>
                        <div className="date">MIN</div>
                    </div>
                    <div className="items">
                        <div className="number">03</div>
                        <div className="date">SEC</div>
                    </div>
                </div> */}
                <Countdown nextDrawNumber={parameters.next_date} />
            </div>

            <div className="questionsSection mt-3">
                <div className="title">What are you waiting for?</div>
                <button className="PlayBtn">Play Now</button>
            </div>

            <div className="directlyPlaySection">

                {parameters.gridpricematrix && parameters.gridpricematrix.map((item, index) =>
                    <div className="items" key={index}>
                        <div className="nb">{item.numbers}</div>
                        <div className="title">NUMBERS</div>
                        <div className="price">{item.price}LBP</div>
                        <button className="letsPlayBtn">PLAY</button>
                    </div>
                )}


            </div>
        </div>
    );
};

export default LLDJ;