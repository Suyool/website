<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Utils\Helper;
use Exception;
use Psr\Log\LoggerInterface;

class LotoServices
{
    private $LOTO_API_HOST;
    private $client;
    private $METHOD_POST;
    private $helper;
    private $loggerInterface;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, Helper $helper, LoggerInterface $loggerInterface)
    {
        $this->client = $client;
        $this->LOTO_API_HOST = 'https://backbone.lebaneseloto.com/Service.asmx/';
        $this->METHOD_POST = $params->get('METHOD_POST');
        $this->helper = $helper;
        $this->loggerInterface = $loggerInterface;
    }

    public function Login()
    {
        $body = [
            'Username' => 'suyool',
            'Password' => 'ZvNud5qY3qmM3@h',
        ];
        $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->LOTO_API_HOST}LoginUser",  $body);

        $content = $response->toArray();
        $token = $content['d']['token'];
        return $token;
    }

    public function VoucherFilter($vcategory)
    {
        $body = ["Token" => "",];
        $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->LOTO_API_HOST}GetAllVouchersType",  $body);

        $content = $response->toArray();
        // dd($content);
        $filteredVouchers = array_filter($content["d"]["ppavouchertypes"], function ($voucher) use ($vcategory) {
            return $voucher["vouchercategory"] === $vcategory;
        });

        return $filteredVouchers;
    }

    public function BuyPrePaid($Token, $category, $type)
    {
        try {
            $login = $this->Login();
            $retryattempt = 1;
            while ($retryattempt <= 2) {
                $body = [
                    "Token" => $login,
                    "category" => $category,
                    "type" => $type,
                ];
                // dd($body);
                $response = $this->helper->clientRequest($this->METHOD_POST,  $this->LOTO_API_HOST . '/PurchaseVoucher',  $body);
                $status = $response->getStatusCode(); // Get the status code
                // $status=null;
                $content = $response->getContent();
                $content = $response->toArray();
                // $content=json_decode('{"d":{"voucherSerial":"8230148466815","voucherCode":"211070872867078","voucherExpiry":"2024-05-29","desc":"7.58$ 35 Days","displayMessage":"Testing You have successfully purchased a 7.58$ 35 Days Voucher code. Please recharge it using the code 211070872867078 before 2024-05-29","token":"fd56a656-42e5-442f-b429-9c8b92c95e46","balance":1373278637.8,"errorinfo":{"errorcode":0,"errormsg":"SUCCESS"},"insertId":null}}',true);

                if ($content['d']['errorinfo']['errorcode'] == 0) {
                    $submit = 0;
                } else if ($retryattempt == 2) {
                    $submit = 0;
                } else {
                    $submit = $content['d']['errorinfo']['errorcode'];
                }

                if ($submit == 0) {
                    return array($content,  json_encode(["Token" => $login, "category" => $category, "type" => $type]),$this->LOTO_API_HOST . '/PurchaseVoucher',@$status);
                } else {
                    sleep(3);
                    $retryattempt++;
                }
            }

            return array($content,  json_encode(["Token" => $login, "category" => $category, "type" => $type]),$this->LOTO_API_HOST . '/PurchaseVoucher',@$status);
        } catch (Exception $e) {
            return array(false, $e->getMessage());
        }
    }

    public function BouquetGrids($ticketId)
    {
        $token = $this->Login();
        $body = [
            'Token' => $token,
            'historyId' => $ticketId,
            'bouquetId' => 0
        ];
        $response1 = $this->helper->clientRequest($this->METHOD_POST, "{$this->LOTO_API_HOST}GetUserLotoTransactionHistoryDetail",  $body);

        $content1 = $response1->toArray();
        $grids = $content1['d']['grids'];
        foreach ($grids as $grids) {
            $bouquetId = $grids['gridId'];
        }
        $body = [
            'Token' => $token,
            'historyId' => $ticketId,
            'bouquetId' => $bouquetId
        ];
        $response2 = $this->helper->clientRequest($this->METHOD_POST, "{$this->LOTO_API_HOST}GetUserLotoTransactionHistoryDetail",  $body);

        $bouquetResponse = $response2->toArray();
        $bouquetId = $bouquetResponse['d']['grids'];
        foreach ($bouquetId as $bouquetId) {
            $gridBalls[] = $bouquetId['gridBalls'];
        }
        $selectedBallsBouquet = implode('|', $gridBalls);
        return $selectedBallsBouquet;
    }

    public function GetTicketId()
    {
        $retry = 1;
        while ($retry) {
            $token = $this->Login();
            $body = [
                'Token' => $token,
                'fromDate' =>  date('Y-m-d'),
                'toDate' => date('Y-m-d', strtotime("+2 days")),
                'transactionType' => 0
            ];
            $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->LOTO_API_HOST}GetUserTransactionHistory",  $body);

            $content = $response->toArray();
            $historyEntries = $content['d']['historyEntries'];

            if (empty($content) || $historyEntries == null) {
                echo "History Entries Is Null in Loto server will retry in 10sec \n";
                sleep(10);
                $retry = 1;
            } else {
                foreach ($historyEntries as $historyEntries) {
                    $historyId[] = $historyEntries['historyId'];
                }
                $historyId = $historyId[0];

                return $historyId;
            }
        }
    }

    public function playLoto($draw, $withZeed, $gridselected, $numdraws, $mobileNo)
    {
        $retryattempt = 1;
        while ($retryattempt <= 2) {
            $token = $this->Login();
            $body = [
                'Token' => $token,
                'drawNumber' => $draw,
                'numDraws' => $numdraws,
                'withZeed' => $withZeed,
                'saveToFavorite' => 1,
                'GridsSelected' => $gridselected,
                'PhoneNumber' => $mobileNo
            ];
            $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->LOTO_API_HOST}SubmitLotoPlayOrderWithPhoneNumber",  $body);

            $content = $response->toArray();
            $submit = $content['d']['errorinfo']['errorcode'];

            if ($submit == 0) {
                return array(true, $content['d']['insertId'],"",$content,$body);
            } else if ($submit == 4 || $submit == 6 || $submit == 9) {
                $error = $content['d']['errorinfo']['errormsg'];
                return array(false, $submit, $error,$content,$body);
            } else {
                sleep(10);
                $this->loggerInterface->info("attemp " . $retryattempt);
                $this->loggerInterface->error($content['d']['errorinfo']['errormsg']);
                $retryattempt++;
            }
        }
        $error = $content['d']['errorinfo']['errormsg'];
        return array(false, $submit, $error,$content,$body);
    }

    public function getDrawsResult()
    {
        $body = [
            'Token' => '',
            'from' => 0,
            'to' => 0
        ];
        $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->LOTO_API_HOST}GetDrawsInformation",  $body);

        $status = $response->getStatusCode(); // Get the status code
        if ($status == 500) {
            return false;
        }
        $content = $response->toArray(false);
        return $content['d']['draws'];
    }

    public function fetchDrawDetails()
    {
        $body = ['Token' => ''];
        $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->LOTO_API_HOST}GetInPlayAndNextDrawInformation",  $body);

        $status = $response->getStatusCode(); // Get the status code
        if ($status == 500) {
            return false;
        }
        $content = $response->toArray(false);
        $date = $content['d']['draws'][0]['drawdate'];
        $nextdrawdetails = $content['d']['draws'][1];

        return array($date, $nextdrawdetails);
    }

    public function GetFullGridPriceMatrix()
    {
        $body = ['Token' => ''];
        $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->LOTO_API_HOST}GetFullGridPriceMatrix",  $body);

        $status = $response->getStatusCode(); // Get the status code
        if ($status == 500) {
            return false;
        }
        $content = $response->toArray(false);


        return $content;
    }

    public function GetWinTicketsPrize($ticketId)
    {
        $token = $this->Login();
        $body = ['Token' => $token, 'historyId' => $ticketId, 'bouquetId' => -1];
        $response = $this->helper->clientRequest($this->METHOD_POST, "{$this->LOTO_API_HOST}GetUserTransactionHistoryDetail",  $body);

        $status = $response->getStatusCode(); // Get the status code
        if ($status == 500) {
            return false;
        }
        $content = $response->toArray(false);


        return $content;
    }
}
