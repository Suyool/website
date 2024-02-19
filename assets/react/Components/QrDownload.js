import React, { useEffect } from 'react';
import QRCode from 'qrcode';
function QrDownload({qrCodeData, setQrData}) {

    useEffect(() => {
        if (qrCodeData){
            QRCode.toDataURL(qrCodeData)
                .then((url) => {
                    const downloadLink = document.createElement('a');
                    downloadLink.href = url;
                    downloadLink.download = 'qr-code.png';
                    downloadLink.click();
                    setQrData(null);
                })
                .catch((error) => {
                    console.error('Error generating QR code:', error);
                });

        }

    }, [qrCodeData]);

    return (
        <></>
    );
}
export default QrDownload;