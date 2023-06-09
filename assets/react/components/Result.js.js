import React from "react";

const Result = () => {

    return (
        <div id="Result">
            <div className="resultTopSection mt-4">
                <div className="title">Draw Numbers</div>
                <div className="ballSection mt-2">
                    <span>11</span>
                    <span>16</span>
                    <span>18</span>
                    <span>27</span>
                    <span>29</span>
                    <span>42</span>
                </div>
            </div>

            <div className="nextDrawSection mt-4">
                <div className="selectTime">May 2023</div>

                <div className="dayDrow">
                    <div className="goNext"><img src="/build/images/Loto/goNext.png" alt="goNext" /></div>
                    <div className="items">
                        <div className="item">
                            <div className="time">14</div>
                            <div className="day">Mon</div>
                        </div>
                        <div className="item">
                            <div className="time">14</div>
                            <div className="day">Mon</div>
                        </div>
                        <div className="item">
                            <div className="time">14</div>
                            <div className="day">Mon</div>
                        </div>
                        <div className="item">
                            <div className="time">14</div>
                            <div className="day">Mon</div>
                        </div>
                        <div className="item">
                            <div className="time">14</div>
                            <div className="day">Mon</div>
                        </div>
                    </div>
                    <div className="goNext"><img src="/build/images/Loto/goNext.png" alt="goNext" /></div>
                </div>

                <div className="winnweSection">
                    <div className="winnweHeader">
                        <div>
                            <img src="/build/images/Loto/LotoLogo.png" alt="SmileLOGO" />
                            <span>BASIC</span>
                        </div>
                    </div>
                    <div className="winnweBody">
                        <div className="ballSection mt-2">
                            <span>11</span>
                            <span>16</span>
                            <span>18</span>
                            <span>27</span>
                            <span>29</span>
                            <span>42</span>
                        </div>
                    </div>

                    <div className="winnweFooter">
                        <div className="price">L.L 2,000,000 won</div>
                        <div className="img">
                            <img src="/build/images/Loto/trofie.png" alt="SmileLOGO" />
                        </div>
                    </div>
                </div>

                <div className="winnweSection">
                    <div className="winnweHeader">
                        <div>
                            <img src="/build/images/Loto/LotoLogo.png" alt="SmileLOGO" />
                            <span>BASIC</span>
                        </div>
                    </div>
                    <div className="winnweBody">
                        <div className="ballSection mt-2">
                            <span>11</span>
                            <span>16</span>
                            <span>18</span>
                            <span>27</span>
                            <span>29</span>
                            <span>42</span>
                        </div>
                    </div>

                    <div className="NoWinnweFooter">
                        <div className="price">L.L 2,000,000 won</div>
                    </div>
                </div>

            </div>
        </div>
    );
};

export default Result;