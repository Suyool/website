import React from "react";

const LLDJ = () => {

    return (
        <div className="LLDJ">

            <div className="estimatedPriceSection mt-3">
                <div className="title">Next Loto Estimated Jackpot</div>
                <div className="priceLoto">LBP 12,000,000,000</div>
                <img src="/build/images/Loto/LotoLogo.png" alt="SmileLOGO" />
            </div>

            <div className="estimatedPriceSection mt-3">
                <div className="title">Next Zeed Estimated Jackpot</div>
                <div className="priceZeed">LBP 400,000,000</div>
                <img src="/build/images/Loto/zeedLogo.png" alt="SmileLOGO" />
            </div>

            <div className="nextDraw m-4">
                <div className="title">Next Draw #2114</div>
                <div className="desc">Thursday Jun, 01, 2023</div>
                <div className="timeSection">
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
                </div>
            </div>

            <div className="questionsSection mt-3">
                <div className="title">What are you waiting for?</div>
                <button className="PlayBtn">Play Now</button>
            </div>

            <div className="directlyPlaySection">
                <div className="items">
                    <div className="nb">6</div>
                    <div className="title">NUMBERS</div>
                    <div className="price">20,000LBP</div>
                    <button className="letsPlayBtn">PLAY</button>
                </div>

                <div className="items">
                    <div className="nb">7</div>
                    <div className="title">NUMBERS</div>
                    <div className="price">140,000LBP</div>
                    <button className="letsPlayBtn">PLAY</button>
                </div>

                <div className="items">
                    <div className="nb">8</div>
                    <div className="title">NUMBERS</div>
                    <div className="price">560,000LBP</div>
                    <button className="letsPlayBtn">PLAY</button>
                </div>

                <div className="items">
                    <div className="nb">9</div>
                    <div className="title">NUMBERS</div>
                    <div className="price">1,680,000LBP</div>
                    <button className="letsPlayBtn">PLAY</button>
                </div>

                <div className="items">
                    <div className="nb">6</div>
                    <div className="title">NUMBERS</div>
                    <div className="price">4,200,000LBP</div>
                    <button className="letsPlayBtn">PLAY</button>
                </div>
            </div>
        </div>
    );
};

export default LLDJ;