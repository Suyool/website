<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class LotoServices
{
    private $LOTO_API_HOST;

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;

        if ($_ENV['APP_ENV'] == 'prod') {
            $this->LOTO_API_HOST = 'https://backbone.lebaneseloto.com/Service.asmx/';
        } else {
            $this->LOTO_API_HOST = 'https://backbone.lebaneseloto.com/Service.asmx/';
        }
    }
    public function Login()
    {
        $response = $this->client->request('POST', "{$this->LOTO_API_HOST}LoginUser", [
            'body' => json_encode([
                'Username' => 'suyool',
                'Password' => 'ZvNud5qY3qmM3@h',
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $content = $response->toArray();
        $token = $content['d']['token'];
        return $token;
    }


    public function VoucherFilter($vcategory)
    {
        $response = $this->client->request('POST', "{$this->LOTO_API_HOST}GetAllVouchersType", [
            'body' => json_encode([
                "Token" => "",
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->toArray();

        $filteredVouchers = array_filter($content["d"]["ppavouchertypes"], function ($voucher) use ($vcategory) {
            return $voucher["vouchercategory"] === $vcategory;
        });

        return $filteredVouchers;
    }

    public function BuyPrePaid($Token, $category, $type)
    {
        $login = $this->Login();
        // dd($this->Login());
        $retryattempt = 1;
        while ($retryattempt <= 2) {
            $response = $this->client->request('POST', $this->LOTO_API_HOST . '/PurchaseVoucher', [
                'body' => json_encode([
                    "Token" => $login,
                    "category" => $category,
                    "type" => $type,
                ]),
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            $content = $response->getContent();
            $content = $response->toArray();

            // dd($content['d']['errorinfo']['errorcode']);
            if ($content['d']['errorinfo']['errorcode'] == 0) {
                $submit = 0;
            } else if ($retryattempt == 2) {
                $submit = 0;
            } else {
                $submit = $content['d']['errorinfo']['errorcode'];
            }

            if ($submit == 0) {
                return array($content,  json_encode(["Token" => $login, "category" => $category, "type" => $type,]));
            } else {
                // $login = $this->Login();
                // // $login = "a1066b81-af54-4aa3-bd33-0ea4c87777ab";
                sleep(3);
                // echo "attemp " . $retryattempt;
                $retryattempt++;
            }
        }

        // dd($content);
        // return $content;
        return array($content,  json_encode(["Token" => $login, "category" => $category, "type" => $type,]));
    }

    public function BouquetGrids($ticketId)
    {
        $token = $this->Login();
        $response1 = $this->client->request('POST', "{$this->LOTO_API_HOST}GetUserLotoTransactionHistoryDetail", [
            'body' => json_encode([
                'Token' => $token,
                'historyId' => $ticketId,
                'bouquetId' => 0
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $content1 = $response1->toArray();
        $grids = $content1['d']['grids'];
        foreach ($grids as $grids) {
            $bouquetId = $grids['gridId'];
        }

        $response2 = $this->client->request('POST', "{$this->LOTO_API_HOST}GetUserLotoTransactionHistoryDetail", [
            'body' => json_encode([
                'Token' => $token,
                'historyId' => $ticketId,
                'bouquetId' => $bouquetId
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
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
        // date('Y-m-d'),
        //         'toDate' => date('Y-m-d',strtotime("+1 day"))
        while ($retry) {
            $token = $this->Login();
            $response = $this->client->request('POST', "{$this->LOTO_API_HOST}GetUserTransactionHistory", [
                'body' => json_encode([
                    'Token' => $token,
                    'fromDate' =>  date('Y-m-d'),
                    'toDate' => date('Y-m-d',strtotime("+1 day")),
                    'transactionType' => 0
                ]),
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);
            $content = $response->toArray();
            // dd($content);
            $historyEntries = $content['d']['historyEntries'];
            // $content = [];

            if (empty($content) || $historyEntries == null) {
                echo "History Entries Is Null in Loto server will retry in 10sec \n";
                sleep(10);
                $retry = 1;
            } else {
                

                // var_dump($historyEntries);
                foreach ($historyEntries as $historyEntries) {
                    $historyId[] = $historyEntries['historyId'];
                }
                $historyId = $historyId[0];

                // echo $historyId;

                // $historyId = 5227340;

                return $historyId;
            }
        }
    }

    public function playLoto($draw, $withZeed, $gridselected,$numdraws)
    {
        $retryattempt = 1;
        while ($retryattempt <= 2) {
            // date('Y-m-d'),
            //         'toDate' => date('Y-m-d',strtotime("+1 day"))
            $token = $this->Login();
            $response = $this->client->request('POST', "{$this->LOTO_API_HOST}SubmitLotoPlayOrder", [
                'body' => json_encode([
                    'Token' => $token,
                    'drawNumber' => $draw,
                    'numDraws' => $numdraws,
                    'withZeed' => $withZeed,
                    'saveToFavorite' => 1,
                    'GridsSelected' => $gridselected
                ]),
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);

            $content = $response->toArray();


            // if ($retryattempt == 2) {
            // $submit = 0;
            // } else {
            // $submit = $content['d']['errorinfo']['errorcode'];
            // }


            // if(!$withZeed){
            // $submit = 0;
            // }else{
            $submit = $content['d']['errorinfo']['errorcode'];
            // }

            if ($submit == 0) {
                $zeed = $content['d']['insertId'];
                // $zeed = 12345;
                return array(true, $zeed);
            } else if ($submit == 4 || $submit == 6 || $submit == 9) {
                $error = $content['d']['errorinfo']['errormsg'];
                return array(false, $submit, $error);
            } else {
                sleep(10);
                echo "attemp " . $retryattempt;
                $retryattempt++;
            }
        }
        $error = $content['d']['errorinfo']['errormsg'];
        return array(false, $submit, $error);
    }

    public function getDrawsResult()
    {
        $response = $this->client->request('POST', "{$this->LOTO_API_HOST}GetDrawsInformation", [
            'body' => json_encode([
                'Token' => '',
                'from' => 0,
                'to' => 0
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $status = $response->getStatusCode(); // Get the status code

        if ($status == 500) {
            return false;
        }
        $content = $response->toArray(false);

        return $content['d']['draws'];
    }

    public function fetchDrawDetails()
    {
        $response = $this->client->request('POST', "{$this->LOTO_API_HOST}GetInPlayAndNextDrawInformation", [
            'body' => json_encode([
                'Token' => ''
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $status = $response->getStatusCode(); // Get the status code
        if ($status == 500) {
            return false;
        }

        $content = $response->toArray(false);

        $date = $content['d']['draws'][0]['drawdate'];
        $nextdrawdetails = $content['d']['draws'][1];

        return array($date, $nextdrawdetails);
    }
}
