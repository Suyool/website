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
        // dd($params);
        if($accessToken != null){
            $host = $params['url'];
        }else{

        if($_ENV['APP_ENV']=='prod'){
            if($app=='loto'){
                $host = 'https://backbone.lebaneseloto.com';
            }else{
                $host = 'https://globalapi.suyool.money/api/';
            }
        }else{
     
     if($app=='loto'){
        $host = 'https://backbone.lebaneseloto.com';
     }else{
        $host = 'https://backbone.lebaneseloto.com' ;
        //  $host = 'https://globalapi.suyool.money/api/';
     }
            }
        }
        // dd($host.$params['url']);
        if (isset($params['url']) || isset($params['data'])) {
            $ch = curl_init();
            //Set the options
            curl_setopt($ch, CURLOPT_URL, $host);

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

}