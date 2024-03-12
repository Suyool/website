import React, { useEffect, useState } from "react";
import QrDownload from "../Components/QrDownload";
import { async } from "regenerator-runtime";
import axios from "axios";

const RechargeThePayment = ({parameters, setHeaderTitle, setBackLink, getEsimId }) => {
  const [getselectedBtn, setselectedBtn] = useState("qr");
  const [qr, setQr] = React.useState(null);
  const [getPlanDetail, setPlanDetail] = useState(null);

  useEffect(() => {
    setHeaderTitle("Suyool eSim");
    setBackLink("Account");

    axios
      .post("/simly/GetEsimDetails", {
        esimId: getEsimId,
      })
      .then((response) => {
        setPlanDetail(response.data.message);
        console.log(response.data.message);
      });
  }, []);

  const handleDownload = async (qrString) => {
    // try {
    //   const result = await fetch(getPlanDetail?.qrCodeImage, {
    //     method: "GET",
    //     headers: {},
    //   });
    //   const blob = await result.blob();
    //   const url = URL.createObjectURL(blob);

    //   const link = document.createElement("a");
    //   link.href = url;
    //   link.download = getPlanDetail?.qrCodeImage;
    //   link.click();
    // } catch (error) {
    //   console.error(error);
    // }

    let object = [
      {
        QR: {
          share: "qr",
          text: qrString,
        },
      },
    ];
    console.log(JSON.stringify(object));
    if (parameters?.deviceType === "Android") {
      window.AndroidInterface.callbackHandler(JSON.stringify(object));
    } else if (parameters?.deviceType === "Iphone") {
      window.webkit.messageHandlers.callbackHandler.postMessage(object);
    }
  };
  const copyToClipboard = (data) => {
    // Create a textarea element
    const textarea = document.createElement("textarea");
    // Set the value of the textarea to the data to be copied
    textarea.value = data;
    // Append the textarea to the document body
    document.body.appendChild(textarea);
    // Select the text inside the textarea
    textarea.select();
    // Copy the selected text to the clipboard using the Clipboard API
    document.execCommand("copy");
    // Remove the textarea from the document body
    document.body.removeChild(textarea);
  };
  const handleShare = (shareCode) => {
    let object = [
      {
        Share: {
          share: "share",
          text: shareCode,
        },
      },
    ];
    console.log(JSON.stringify(object));
    if (parameters?.deviceType === "Android") {
      window.AndroidInterface.callbackHandler(JSON.stringify(object));
    } else if (parameters?.deviceType === "Iphone") {
      window.webkit.messageHandlers.callbackHandler.postMessage(object);
    }
  };

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
            <div className="imageBox">
              <img className="image" src={getPlanDetail?.qrCodeImage} alt="qrCode" />
            </div>

            <div className="downloadBtn" onClick={()=>{handleDownload(getPlanDetail?.qrCodeString)}}>
              <img src="/build/images/install.svg" alt="download" />
            </div>
          </div>

          <div className="Caution">
            <div className="warImg">
              <img src="/build/images/attentionSign.svg" alt="warning" />
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
              <div className="textStep">Label the eSIM.</div>
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

          <div className="titleDes">Follow these steps to access your eSIM:</div>

          <div className="stepsToRecharge mt-4">
            <div id="NetworksubTitle">
              <div className="title">Network :</div>
              <div className="bodyy">
                {getPlanDetail?.NetworkAvailable[0]?.supported_networks?.map((network, index) => (
                  <div className="plan" key={index}>
                    <div style={{ color: "black" }}>-{" "}{network.name}</div>
                  </div>
                ))}
              </div>
            </div>
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
          {/* <QrDownload qrCodeData={qr} setQrData={setQr} /> */}
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
                  <div className="desc">{getPlanDetail?.qrCodeString}</div>
                </div>
                <div className="copy">
                  <img src="/build/images/copy.svg" alt="copy" onClick={() => handleShare(getPlanDetail?.qrCodeString)} />
                </div>
              </div>
              <div className="cardNumber">
                <div className="text">
                  <div className="title">ACTIVATION CODE</div>
                  <div className="desc">{getPlanDetail?.qrCodeString}</div>
                </div>
                <div className="copy">
                  <img src="/build/images/copy.svg" alt="copy" onClick={() => handleShare(getPlanDetail?.qrCodeString)} />
                </div>
              </div>
            </div>
          </div>

          <div className="Caution">
            <div className="warImg">
              <img src="/build/images/attentionSign.svg" alt="warning" />
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

          <div className="titleDes">Follow these steps to access your eSIM:</div>

          <div className="stepsToRecharge mt-4">
          <div id="NetworksubTitle">
              <div className="title">Network :</div>
              <div className="bodyy">
                {getPlanDetail?.NetworkAvailable[0]?.supported_networks?.map((network, index) => (
                  <div className="plan" key={index}>
                    <div style={{ color: "black" }}>-{" "}{network.name}</div>
                  </div>
                ))}
              </div>
            </div>
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
