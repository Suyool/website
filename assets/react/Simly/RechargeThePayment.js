import React, { useEffect, useState } from "react";
const RechargeThePayment = ({ setHeaderTitle, setBackLink }) => {
  const [getselectedBtn, setselectedBtn] = useState("qr");
  useEffect(() => {
    setHeaderTitle("Suyool eSim");
    setBackLink("");
  }, []);

  return (
    <div id="RechargeThePayment_simly">
      <div className="MainTitle">Activate your eSIM in two ways - QR code or manually. Choose your preference:</div>
      <div className="Switch">
        <div className={getselectedBtn === "qr" ? "selectBtnSelected" : "selectBtn"} onClick={() => setselectedBtn("qr")}>
          QR CODE
        </div>
        <div className={getselectedBtn === "manual" ? "selectBtnSelected" : "selectBtn"} onClick={() => setselectedBtn("manual")}>
          MANUAL
        </div>
      </div>

      {getselectedBtn === "qr" && (
        <>
          <div className="QrBox">
            <div className="title">This is your purchased eSim!</div>
            <img className="image" src="/build/images/gettheapp.png" alt="qrCode" />
            <div className="downloadBtn"><img src="/build/images/install.svg" alt="download" /></div>
          </div>

          <div className="Caution">
            <div className="warImg">
              <img src="/build/images/Loto/warning.png" alt="warning" />
            </div>
            <div className="title">Usually, eSIMs can only be installed once. Once removed, you can’t reinstall them.</div>
          </div>

          <div className="titleDes">Follow these steps to install your eSIM:</div>

          <div className="stepsToRecharge">
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Save the QR code to your photos or take a screenshot of this screen.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Go to settings - Cellular - Add eSIM.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Tap on ‘Use QR Code’ then ‘Open Photos’ & select the screenshot.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Label the eSIM as ‘Simly - People’s Republic of China’.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Choose your primary line to call or send messages.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Choose your primary line to use iMessage & FaceTime.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Choose the eSIM plan as your default line for Cellular Data & do not turn on ‘Allow Cellular Data Switching’ to prevent charges on your other line.</div>
            </div>
          </div>
          <div className="stepsToRecharge mt-4">
            <div className="subTitle">Network: China Unicom</div>
            <div className="subTitle">APN: The APN is set automatically</div>
            <div className="subTitle">Data Roaming: ON</div>
            <div className="steps mt-2">
              <div className="dot"></div>
              <div className="textStep">Select your Simly eSIM under ‘Cellular Plans’.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Ensure that ‘Turn On This Line’ is toggled ON.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Go to ‘Network Selection’ & select the network.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Turn on the data roaming.</div>
            </div>
          </div>
        </>
      )}

      {getselectedBtn === "manual" && (
        <>
          <div className="QrBox">
            <div className="title">This is your purchased eSim!</div>

            <div className="qrString">
                <div className="cardNumber">
                    <div className="text">
                        <div className="title">SM-DP+ ADDRESS</div>
                        <div className="desc">Consumer.rsp.global</div>
                    </div>
                    <div className="copy"><img src="/build/images/copy.svg" alt="copy" /></div>
                </div>
                <div className="cardNumber">
                    <div className="text">
                        <div className="title">SM-DP+ ADDRESS</div>
                        <div className="desc">Consumer.rsp.global</div>
                    </div>
                    <div className="copy"><img src="/build/images/copy.svg" alt="copy" /></div>
                </div>
            </div>
          </div>

          <div className="Caution">
            <div className="warImg">
              <img src="/build/images/Loto/warning.png" alt="warning" />
            </div>
            <div className="title">Usually, eSIMs can only be installed once. Once removed, you can’t reinstall them.</div>
          </div>

          <div className="titleDes">Follow these steps to install your eSIM:</div>

          <div className="stepsToRecharge">
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Go to Settings - Cellular/Mobile - Add Cellular/Mobile Plan.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Tap on ‘Enter Details Manually’.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Enter your SM-DP+ Address and Activation Code.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Tap on ‘Add Cellular Plan’.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Choose your primary line to call or send messages.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Choose your primary line to use iMessage & FaceTime.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Choose the eSIM plan as your default line for Cellular Data & do not turn on ‘Allow Cellular Data Switching’ to prevent charges on your other line.</div>
            </div>
          </div>
          <div className="stepsToRecharge mt-4">
            <div className="subTitle">Network: China Unicom</div>
            <div className="subTitle">APN: The APN is set automatically</div>
            <div className="subTitle">Data Roaming: ON</div>
            <div className="steps mt-2">
              <div className="dot"></div>
              <div className="textStep">Select your Simly eSIM under ‘Cellular Plans’.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Ensure that ‘Turn On This Line’ is toggled ON.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Go to ‘Network Selection’ & select the network.</div>
            </div>
            <div className="steps">
              <div className="dot"></div>
              <div className="textStep">Turn on the data roaming.</div>
            </div>
          </div>
        </>
      )}
    </div>
  );
};

export default RechargeThePayment;
