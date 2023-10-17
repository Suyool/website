<?php

namespace App\Utils;

use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Helper
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client=$client;
    }

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

    public static function send_curl($params, $accessToken = null)
    {
        if ($accessToken != null) {
            $host = $params['url'];
        } else {
            if ($_ENV['APP_ENV'] == 'prod') {
                $host = 'https://externalservices.nicebeach-895ccbf8.francecentral.azurecontainerapps.io/api/GlobalAPIs/';
            } else {
                $host = 'http://10.20.80.62/SuyoolGlobalAPIs/api/';
            }
        }

        if (isset($params['url']) || isset($params['data'])) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $host . $params['url']);
            (isset($params['data'])) ? $data = $params['data'] : $data = "";
            (!isset($params['type']) || $params['type'] != 'get') ? curl_setopt($ch, CURLOPT_POSTFIELDS, $data) : '';
            (isset($params['type']) && $params['type'] == 'post') ? curl_setopt($ch, CURLOPT_POST, true) : '';
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

    public static function getHost($domain)
    {
        $parsedUrl = parse_url($domain);
        $hostname = $parsedUrl['host'];
        $parts = explode('.', $hostname);
        $partsCount = count($parts);
        if ($partsCount >= 3 && $parts[0] === 'www') {
            $hostname = implode('.', array_slice($parts, 1));
        }
        return $hostname;
    }

    public function clientRequest($method, $url, $body)
    {
        try{
            $response = $this->client->request($method, $url, [
                'body' => json_encode($body),
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);
        
            return $response;
        }catch(Exception $e)
        {
            return array(false,$e->getMessage());
        }
        
    }
    
}
