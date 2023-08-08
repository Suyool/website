<?php

namespace App\Utils;


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
       public static function send_curl($params,$app=null,$accessToken=null) {
           if($accessToken != null){
               $host = $params['url'];
           }else{
            if($_ENV['APP_ENV']=='prod'){
                if($app=='loto'){
                    $host = 'https://backbone.lebaneseloto.com';
                }else{
                    $host = 'https://externalservices.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/api/GlobalAPIs/';
                }
            }else{
         
         if($app=='loto'){
            $host = 'https://backbone.lebaneseloto.com';
         }else{
            $host = 'http://10.20.80.62/SuyoolGlobalAPIs/api/' ;
            //  $host = 'https://globalapi.suyool.money/api/';
         }
                }
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
                'X-Shopify-Access-Token: ' . $accessToken,
            ]);

            

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $ret = curl_exec($ch);
            curl_close($ch);

            return $ret;
        }
    }

    public static function getBrowserType()
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
}