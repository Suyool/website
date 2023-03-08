<?php

namespace App\Utils;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class Helper
{
    /**
     * Slugify a string
     * @param string string <p>
     * The string that is to be slugified.
     * </p>
     * @param int  type <p>
     * When set to 1, shorten the string in case it is greater than 50 characters. Defaults to 1.
     * </p>
     * @return string Returns the slugified string.
     */
    public function slugifyText($string, $type = 1)
    {
        $string = mb_ereg_replace("#[^a-zA-Z0-9]#", " ", $string);
        $string = str_replace("%", "", $string);
        $string = str_replace("&quot;", "", $string);
        $string = str_replace("quot", "", $string);
        $string = str_replace("/", "", $string);
        $string = str_replace("#-", "-", $string);
        $string = str_replace("#", "", $string);
        $string = str_replace("'", "", $string);
        $string = str_replace('"', "", $string);
        $string = str_replace(".", "", $string);
        $string = str_replace(":", "", $string);
        $string = explode(" ", $string);

        foreach ($string as $value) {
            if (mb_strlen($value) > 2)
                $array [] = $value;
        }

        if (!empty($array)) {
            $string = implode("-", $array);
            if ($type == 1)
                if (mb_strlen($string) > 50) $string = $this->shortString($string, 50);
            return $string;
        }

        return false;
    }

    public function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
    {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }
        return $str;
    }

    /**
     * @param string $string
     * @param int $length
     * @param int $start
     * @return mixed|string
     */
    public static function shortString($string, $length, $start = 0)
    {
        $string = str_replace("&nbsp;", "", $string);
        $string = html_entity_decode($string);
        $string = mb_substr($string, $start, $length);
        return $string;
    }

    /**
     * @param \DateTime|int $date
     * @param bool $isDatetime
     * @return string|false
     */
    public function getISO8601Date($date, $isDatetime = false)
    {
        $timestamp = $isDatetime ? strtotime($date->format('Y-m-d H:i:s')) : $date;
        return date('c', $timestamp);
    }

    /**
     * @param \DateTime|int $date
     * @param bool $isDatetime
     * @return string|false
     */
    public function getISO8601DateFromDate($date, $isDatetime = false)
    {
        $timestamp = strtotime($date);
        return date('c', $timestamp);
    }

    /**
     * @author George Kmeid
     *
     * @param string $image
     * @param string $packageName
     * @return string
     */
    public function files($image, $packageName = 'original', $baseUrl = false)
    {
        if(!$baseUrl)
            $baseUrl = $_ENV['REMOTE_FILES_URL'];

        $versionStrategy = new EmptyVersionStrategy;

        $defaultPackage = new Package($versionStrategy);

        if ($packageName === 'original')
            $namedPackages = [
                'original' => new UrlPackage($baseUrl . "pictures", $versionStrategy),
            ];
        else
            $namedPackages = [
                $packageName => new UrlPackage($baseUrl . "imagine/pictures_" . $packageName, $versionStrategy),
            ];

        $packages = new Packages($defaultPackage, $namedPackages);

        return $packages->getUrl('/' . $image, $packageName);
    }

    /**
     * @param int $number
     * @return mixed
     */
    public function getArabicOrdinalNumber($number)
    {
        $arabicOrdinalNumbers = ['الأول', 'الثاني', 'الثالث', 'الرابع', 'الخامس',];
        return $arabicOrdinalNumbers[$number];
    }

    /**
     * @param string $url
     * @return mixed
     */
    public function fetchDataFromUrl($url)
    {
        $jsonData = @file_get_contents($url);
        return json_decode($jsonData);
    }

    /**
     * @param \DateTime|false $date
     * @return string|false
     */
    function getRssDate($date = false)
    {
        return (!$date) ? gmdate("D, d M Y H:i:s T", time()) : gmdate("D, d M Y H:i:s T", strtotime($date));
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

    public static function sendPostData($url, $post, $json = true)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($json)
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Connection: Keep-Alive',
                ]
            );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    function generateUploadedFileName($file)
    {
        return rand(1000000, 9999999) . '_' . time() . '.' . $file->guessExtension();
    }

    /**
     * Returns an array of tags IDs
     * @param string $tags
     * @return array
     */
    public function getTagsIds($tags)
    {
        $tagsArray = explode(",", $tags);
        $tagsArray = array_unique($tagsArray);

        $tagIds = [];
        foreach ($tagsArray as $tag) {
            if ($tag) {
                $tag = str_replace("#", "", $tag);
                $tagIds[] = $tag;
            }
        }
        return array_filter($tagIds);
    }

    function convertArabicNumbers($arabic) {
        $trans = array(
            "٠" => "0",
            "١" => "1",
            "٢" => "2",
            "٣" => "3",
            "٤" => "4",
            "٥" => "5",
            "٦" => "6",
            "٧" => "7",
            "٨" => "8",
            "٩" => "9",
        );
        return strtr($arabic, $trans);
    }

    public static function getImages($html){
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);

        $imgs = $doc->getElementsByTagName('img');
        $images = array();

        foreach($imgs as $img){
            $image = (string)$img->getAttribute('src');
            $images[] = basename($image);
        }

        $images = implode(",",$images);
        return $images;
    }

    public static function getVideo($html){
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);

        $iframes = $doc->getElementsByTagName('iframe');

        $videos = array();

        foreach($iframes as $iframe){
            $src = (string)$iframe->getAttribute('src');
            $videos[] = $src;
        }

        $videos = implode(",",$videos);
        return $videos;
    }

    public static function getInstagrams($html){
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);

        $blockquotes = $doc->getElementsByTagName('blockquote');

        $instagrams = array();
        foreach ($blockquotes as $blockquote) {
            if ($blockquote->hasAttribute('class') && strstr($blockquote->getAttribute('class'), 'instagram-media')) {
                $hrefs = $blockquote->getElementsByTagName('a');
                foreach ($hrefs as $href) {
                    $instagrams[] = explode("?",$href->getAttribute('href'))[0];
                }
            }
        }

        $instagrams = array_unique($instagrams);

        return $instagrams;
    }

    public static function getBody($body, $external = false, $html = false) {
        $empty = array('&rlm;', '&lrm;', '&zwnj;', '&#8203;');
        $spaces = array('&nbsp;');
        $quotes = array('&laquo;', '&raquo;', '&ldquo;', '&rdquo;');
        $dashes = array('&ndash;', '&mdash;');
        $body = str_replace($empty, '', $body);
        $body = str_replace($spaces, ' ', $body);
        $body = str_replace($quotes, '"', $body);
        $body = str_replace($dashes, '-', $body);
        //$body = strip_tags($body,'<a>');
        if(!$html)
            $body = strip_tags($body);
        else
            $body = strip_tags($body, '<p><img><a>');

        $body = trim($body);
        $body = html_entity_decode($body);
        $body = htmlspecialchars_decode($body);
        $body = str_ireplace(array("<br />", "<br>", "<br/>"), "\r\n", $body);
        $body = str_replace('&ndash;', ' ', $body);
        $body = str_replace('&mdash;', ' ', $body);
        $body = str_replace('&zwnj;', '', $body);
        $body = str_replace('&rlm;', '', $body);
        $body = str_replace('&lrm;', '', $body);
        $body = str_replace('&ldquo;', '"', $body);
        $body = str_replace('&rdquo;', '"', $body);
        $internalErrors = libxml_use_internal_errors(true);
        if ($html) {
            $body = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $body);

            $dom = new \DOMDocument();
            $dom->loadHTML('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $body);
            $new_p = $dom->createElement('p');
            $images = $dom->getElementsByTagName('img');

            // Place image tags inside paragraph tags
            foreach ($images as $image) {
                $src = $image->getAttribute('src');
                $image->setAttribute('src', $src);
                $new_p_clone = $new_p->cloneNode();
                $image->parentNode->replaceChild($new_p_clone, $image);
                $new_p_clone->appendChild($image);
            }

            $body = $dom->saveHTML($dom->documentElement);
            // Get body from html
            preg_match("/<body[^>]*>(.*?)<\/body>/is", $body, $matches);

            if(isset($matches[1])){
                $body = $matches[1];
                $body = trim($body);
                $body = str_replace('<p><p>', '<p>', $body);
                $body = preg_replace("/(<img[^>]*><\\/p[^>]*>)/", "$1<p>", $body);
            }
        }

        return $body;
    }

    public static function getJsonTitle($title) {
        $title = (new Helper)->capture_html_entities($title);
        $title = html_entity_decode($title);
        $title = htmlspecialchars_decode($title);
        $title = (new Helper)->capture_html_entities($title, true);
        return $title;
    }

    /**
     * @param bool $entire_url
     * @return string
     */
    public function currentURL($entire_url = true)
    {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $pageURL .= "s";

        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= ($entire_url) ? $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] :
                $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"];
        }
        return $pageURL;
    }

    /**
     * @param string $pageTitle
     * @return string
     */
    public function formatMetaTitle($pageTitle)
    {
        $pageTitle = str_replace('&quot;', '', $pageTitle);
        return str_replace('"', '', $pageTitle);
    }

    /**
     * @param string $pageDesc
     * @return string
     */
    public function formatMetaDescription($pageDesc)
    {
        $pageDesc = str_replace('&quot;', '', $pageDesc);
        $pageDesc = str_replace('"', '', $pageDesc);
        return (mb_strlen($pageDesc) > 160) ? mb_substr(strip_tags($pageDesc), 0, 160) : $pageDesc;
    }

    /**
     * Convert HTML Entities to UTF-8 characters found in ar_utf8.php
     * @param string $string
     * @param bool $decode
     * @return string
     */
    public function capture_html_entities($string, $decode = false) {
        $pattern = $decode ? "/UNICODE_(\d+)/" : "/&#(\d+);/";

        return preg_replace_callback($pattern, function ($matches) use ($decode) {
            if (defined("UNICODE_" . $matches[1]))
                return $decode ? constant($matches[0]) : "UNICODE_" . $matches[1];
            else
                return $matches[0];
        }, $string);
    }

    public function createFile($path,$mode,$str){
        $fp=fopen($path,$mode);
        fputs($fp,$str);
        fclose($fp);
    }

    public function create_zip($file, $destination, $localname = "newsfeed.json"){
        $zip = new \ZipArchive();

        if ($zip->open($destination, \ZipArchive::CREATE)!==TRUE){
            exit("cannot open <$destination>\n");
        }

        $zip->addFile($file, $localname);
        $zip->close();
    }

    public function replaceDate($date){
        $date = str_replace("Mon","الإثنين", $date);
        $date = str_replace("Tue","الثلاثاء", $date);
        $date = str_replace("Wed","الأربعاء", $date);
        $date = str_replace("Thu","الخميس", $date);
        $date = str_replace("Fri","الجمعة", $date);
        $date = str_replace("Sat","السبت", $date);
        $date = str_replace("Sun","الأحد", $date);

        $date = str_replace("Jan","كانون الثاني", $date);
        $date = str_replace("Feb","شباط", $date);
        $date = str_replace("Mar","آذار", $date);
        $date = str_replace("Apr","نيسان", $date);
        $date = str_replace("May","أيار", $date);
        $date = str_replace("Jun","حزيران", $date);
        $date = str_replace("Jul","تموز", $date);
        $date = str_replace("Aug","آب", $date);
        $date = str_replace("Sep","أيلول", $date);
        $date = str_replace("Oct", "تشرين الأول", $date);
        $date = str_replace("Nov", "تشرين الثاني", $date);
        $date = str_replace("Dec","كانون الأول", $date);

        return $date;
    }

    public function getWordsCount($string) {
        $punctuation_marks = array('"', ',', ';', '،', '؛', '?', '؟', '.', ')', '(', '!', ':');
        $words = preg_split('/\s+/', str_replace($punctuation_marks, ' ', $string));

        foreach($words as $k=>$word){
            if(empty($word)){
                unset($words[$k]);
            }
        }

        return count($words);
    }

    public function shortenURL($url){
        $login = "elnashranews";
        $appkey = "R_13572f954bd7fc8ea051a1c8b78de9ab";
        $format = "txt";

        $connectURL = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;
        return $this->sendCurl($connectURL);
    }

    public function setNewsDateForJson($create_date){
        $date = date("D d M Y", strtotime($create_date));
        $time = date("H:i", strtotime($create_date));
        $date = $this->replaceDate($date);

        $date = $date . "،" . " $time";
        return $date;
    }

    public function setNewsDateTimeForJson($create_date,$date = true){
        if($date)
            $date = date("D d M Y", strtotime($create_date));
        else
            $time = date("H:i", strtotime($create_date));

        $date = $this->replaceDate($date);

        return $date;
    }

    /**
     * Convert a string from Windows-1256 to UTF-8.
     * @param string string <p>
     * The string that is to be converted.
     * </p>
     * @return string Returns the converted string.
     */
    public static function convertToUtf8($string){
        $string = iconv("windows-1256", "utf-8", $string);

        return $string;
    }

    public function convertNumbersToArabic($number){
        $western_arabic = array('0','1','2','3','4','5','6','7','8','9');
        $eastern_arabic = array('٠','١','٢','٣','٤','٥','٦','٧','٨','٩');

        return str_replace($western_arabic, $eastern_arabic, $number);
    }
    public static function send_curl($params) {
        if($_ENV['APP_ENV']=='prod'){
            $host = 'https://hey-pay.mobi/';
        }else{
            $host = 'https://stage.elbarid.com/' ;
        }
        if (isset($params['url']) || isset($params['data'])) {
            $ch = curl_init();
            //Set the options
            curl_setopt($ch, CURLOPT_URL, $host. $params['url']);

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
    public function clean($string)
    {
        $string = preg_replace('/[^.A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
        $string = preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
        return trim($string, '-'); // Removes leading/trailing hyphens.
    }
}