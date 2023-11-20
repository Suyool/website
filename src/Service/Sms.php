<?php


namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class SoapSms
{
    public $phoneNumber;
    public $message;
    public $unicodeMessage;
    public $sms_type_id;
    public $notify;
    public $senderId;
    public $priority;
    public $vbApp;
    public $vbIdTime;
}

class SoapAuthHeader
{
    public $username;
    public $password;
}

class Sms
{

    //	------------ Config values and properties ------------
    private $wsdl = 'https://e-barid.com/elbarid.wsdl';//https://elbarid.me/elbarid.wsdl
    //	Credentials
    private $username = 'suyool';
    private $password = 'suy00l321';
    //  SMS properties
    private $senderId = 'Suyool';
    private $notify = '0';
    private $priority = '1';
    private $vbapp = 'EMG';
    // SMS object Properties
    private $sms_type;
    private $soapSmsObject;
    private $client;
    private $AuthHeader;
    private $filesystem;
    protected $kernel;

    //	------------------------------------------------------

    public function __construct(KernelInterface $kernel, Filesystem $filesystem)
    {
        $this->kernel = $kernel;
        $this->filesystem = $filesystem;
        $this->client = new \SoapClient($this->wsdl, array('exceptions' => true, 'trace' => 1, 'encoding' => 'UTF-8'));
        $this->AuthHeader = new SoapAuthHeader();

        $this->AuthHeader->username = $this->username;
        $this->AuthHeader->password = $this->password;

        $Headers[] = new \SoapHeader('namespace', 'AuthHeader', $this->AuthHeader);
        $this->client->__setSoapHeaders($Headers);

        $this->soapSmsObject = new SoapSms();
        $this->soapSmsObject->notify = $this->notify;
        $this->soapSmsObject->senderId = $this->senderId;
        $this->soapSmsObject->priority = $this->priority;
        $this->soapSmsObject->vbApp = $this->vbapp;
    }

    public function runSMSCommand($id, $title)
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'app:send-sms',
            'newsId' => (string)$id,
            'title' => $title,
        ]);
        $output = new BufferedOutput();

        $application->run($input, $output);

        $content = $output->fetch();

        return $content;
    }

    public function send($text, $recipients, $newsId = null)
    {
        $this->soapSmsObject->message = iconv('windows-1256', 'utf-8', $text);
        $text = $this->formatMsg($text);
        $this->soapSmsObject->vbIdTime = is_null($newsId) ? time() : $newsId;
        $this->soapSmsObject->phoneNumber = $recipients;
        $this->soapSmsObject->sms_type_id = $this->getSmsTypeId($this->sms_type);
        if (in_array($this->soapSmsObject->sms_type_id, array(3, 4))) {
            $this->soapSmsObject->unicodeMessage = $text;
        }
        if (strpos($recipients, ',') === false) {
            if ($this->sendUnicast($text, $recipients)) return '0';
        } else {
            if ($this->sendBroadcast($text, $recipients)) return '0';
        }
    }

    private function sendUnicast($text, $recipient)
    {
        try {
            $this->soapSmsObject->vbIdTime = time();
            $result = $this->client->unicast($this->soapSmsObject);
        } catch (SoapFault $fault) {
            $this->logIt($fault->__toString());
            return false;
        }
        if ($this->validateResponse($result)) {
            return true;
        }
        $this->logIt($result);
        return false;
    }

    private function sendBroadcast($text, $recipients)
    {
        try {
            $result = $this->client->broadcast($this->soapSmsObject);
        } catch (SoapFault $fault) {
            $this->logIt($fault->__toString());
            return false;
        }
        if ($this->validateResponse($result)) {
            return true;
        }
        $this->logIt($result);
        return false;
    }

    private function validateResponse($response)
    {
        $return = false;
        if (strlen($response) > 0) {
            $response = substr($response, 0, 1);
            if ($response == 0) {
                $return = true;
            }
        }
        return $return;
    }

    private function getSmsTypeId($type)
    {
        switch (strtolower($type)) {
            case 'longarabic'    :
                return 4;
            case 'arabic'        :
                return 3;
            case 'long'            :
                return 2;
            default:
            case 'normal'        :
                return 1;
        }
    }

    private function formatMsg($title)
    {
        $title = $this->encodeSMS($title);
        $title = $this->sequences($title);
        return $title;
    }

    private function getUnicodeOfSpecialCharacters($text)
    {
        $replace = array(
            '\u' => '',
            '\n' => '000A',
            '\r\n' => '000D',
            ' ' => '0020',
            '!' => '0021',
            '"' => '0022',
            '#' => '0023',
            '$' => '0024',
            '%' => '0025',
            '&' => '0026',
            "'" => '0027',
            '(' => '0028',
            ')' => '0029',
            '*' => '002A',
            '+' => '002B',
            ',' => '002C',
            '-' => '002D',
            '.' => '002E',
            '/' => '002F',
            ':' => '003A',
            '@' => '0040',
            '_' => '00F2',
            '=' => '003D',
            '?' => '003F',
            ';' => '003B',
            '<' => '003C',
            '>' => '003E',
            '|' => '007C',
            '~' => '007E',
            '{' => '007B',
            '}' => '007D'
        );
        $message = strtr($text, $replace);
        return $message;
    }

    private function encodeSMS($title)
    {
        $title = str_replace("\"", "", $title);
        $title = str_replace("&quot;", "", $title);
        $title = str_replace("&#39;", "", $title);
        $title = str_replace(array(), array(), $title);
        $title = str_replace(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9), array('#0,', '#1,', '#2,', '#3,', '#4,', '#5,', '#6,', '#7,', '#8,', '#9,'), $title);
        $title = str_replace(array('#0,', '#1,', '#2,', '#3,', '#4,', '#5,', '#6,', '#7,', '#8,', '#9,'), array('0030', '0031', '0032', '0033', '0034', '0035', '0036', '0037', '0038', '0039'), $title);
        foreach (range('a', 'z') as $i) {
            $char = ord($i);
            $char = dechex($char);
            $char = "00" . $char;
            $title = str_replace($i, $char, $title);
        }
        $title = str_replace(array("�", "�"), "e", $title);
        foreach (range('A', 'Z') as $i) {
            $char = ord($i);
            $char = dechex($char);
            $char = "00" . $char;
            $title = str_replace($i, $char, $title);
        }
        $title = iconv("windows-1256", "utf-8", $title);
        $title = json_encode($title);
        $title = str_replace(array('"', '\u', ' ', ':', '.', ',', '-'),
            array('', '', '0020', '003A', '002E', '002C', '002D',), $title);
        $title = $this->getUnicodeOfSpecialCharacters($title);
        return $title;
    }

    private function sequences($title)
    {
        if (strlen($title) > 280) {
            $header = "050003c8";
            if (strlen($title) < 536) {
                $part1 = substr($title, 0, 268);
                $part1 = $header . "0201" . $part1;
                $part2 = substr($title, 268, 268);
                $part2 = $header . "0202" . $part2;
                $title = $part1 . $part2;
            } elseif (strlen($title) > 536 && strlen($title) < 805) {
                $part1 = substr($title, 0, 268);
                $part1 = $header . "0301" . $part1;
                $part2 = substr($title, 268, 268);
                $part2 = $header . "0302" . $part2;
                $part3 = substr($title, 536, 268);
                $part3 = $header . "0303" . $part3;
                $title = $part1 . $part2 . $part3;
            } else {
                $this->logIt("Wrong title length : " . strlen($title) . " article (" . $this->soapSmsObject->vbIdTime . ") not sent.");
                die();
            }
            $this->sms_type = "LongArabic";
        } else {
            $this->sms_type = "Arabic";
        }
        return $title;
    }

    private function logIt($text)
    {
        $this->filesystem->appendToFile("var/logs/smsSOAPLog.txt", "\n\r\n\r$text - " . date("Y-m-d H:i:s"));
    }

}
