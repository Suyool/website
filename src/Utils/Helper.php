<?php

namespace App\Utils;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class Helper
{

    /**
     * @param string $url
     * @param array $params
     * @return mixed
     */
    public static function sendCurl($url, array $params = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }

    public static function send_curl($params)
    {
        if ($_ENV['APP_ENV'] == 'prod') {
            // $host = 'https://globalapi.suyool.money/api/';
            $host = 'https://suyoolglobalapi.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/';
        } else {
            $host = 'https://suyoolglobalapi.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/';
        }
        if (isset($params['url']) || isset($params['data'])) {
            $ch = curl_init();
            //Set the options
            curl_setopt($ch, CURLOPT_URL, $host . $params['url']);
            //Set the data
            (isset($params['data'])) ? $data = $params['data'] : $data = "";
            //If the request type is not get, add the CURL postfield data
            (!isset($params['type']) || $params['type'] != 'get') ? curl_setopt($ch, CURLOPT_POSTFIELDS, $data) : '';
            //If type of the request is post add it
            (isset($params['type']) && $params['type'] == 'post') ? curl_setopt($ch, CURLOPT_POST, true) : '';
            //
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Connection: Keep-Alive',
                ]
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $ret = curl_exec($ch);
            curl_close($ch);
            return $ret;
        }
    }


    public static function getHost($domain){
        $parsedUrl = parse_url($domain);

        // Get the hostname from the parsed URL
        $hostname = $parsedUrl['host'];

        // Remove the "www" and any subdomains
        $parts = explode('.', $hostname);
        $partsCount = count($parts);
        if ($partsCount >= 3 && $parts[0] === 'www') {
            $hostname = implode('.', array_slice($parts, 1));
        }
        return $hostname;
    }

    function spfw_get_browser_type()
    {
        $browser = "";
        if (strrpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("MSIE"))) {
            $browser = "IE";
        } else if (strrpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("Presto"))) {
            $browser = "opera";
        } else if (strrpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("CHROME"))) {
            $browser = "chrome";
        } else if (strrpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("SAFARI"))) {
            $browser = "safari";
        } else if (strrpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("FIREFOX"))) {
            $browser = "firefox";
        } else if (strrpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower("Netscape"))) {
            $browser = "netscape";
        }
        return $browser;
    }

}