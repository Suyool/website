<?php

namespace App\Utils;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class Helper
{
    /**
     * @param \DateTime|false $date
     * @return string|false
     */
    // function getRssDate($date = false)
    // {
    //     return (!$date) ? gmdate("D, d M Y H:i:s T", time()) : gmdate("D, d M Y H:i:s T", strtotime($date));
    // }

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
    public static function send_curl($params,$app=null) {
        // dd($params);
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
        // dd($host.$params['url']);
        if (isset($params['url']) || isset($params['data'])) {
            $ch = curl_init();
            //Set the options
            curl_setopt($ch, CURLOPT_URL, $host . $params['url']);

            //Set the data
            (isset($params['data'])) ? $data = $params['data'] : $data = "";
            // dd($data);
            //If the request type is not get, add the CURL postfield data
            (!isset($params['type']) || $params['type'] != 'get') ? curl_setopt($ch, CURLOPT_POSTFIELDS, $data) : '';
            //If type of the request is post add it
            (isset($params['type']) && $params['type'] == 'post') ? curl_setopt($ch, CURLOPT_POST, true) : '';
            //
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
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

}