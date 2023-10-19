<?php
// src/Service/DeepLinkService.php

namespace App\Service;

class DeepLinkService
{
    public function generateDeepLink($userAgent, $flag, $currentUrl = null, $browser = null, $additionalInfo = null)
    {
        if (stristr($userAgent, 'Android') !== false || stristr($userAgent, 'Linux; Android') !== false) {
            return 'suyoolpay://suyool.com/sms=?{"flag":"' . $flag . '"}';
        } elseif (stristr($userAgent, 'iPhone') !== false || stristr($userAgent, 'iPad') !== false || stristr($userAgent, 'iPod') !== false) {
            // Use the provided parameters for iOS
            return 'suyoolpay://suyool.com/sms=?{"flag":"' . $flag . '","browsertype":"' . $browser . '","AdditionalInfo":"' . $additionalInfo . '","currentUrl":"' . $currentUrl . '"}';
        } else {
            // Default behavior for other devices
            return 'suyoolpay://suyool.com/sms=?{"flag":"' . $flag . '"}';
        }
    }

}
