<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class LotoServices
{

    private $client;
    private $LOTO_API_HOST;

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
        // date('Y-m-d'),
        //         'toDate' => date('Y-m-d',strtotime("+1 day"))
        $token = $this->Login();
        $response = $this->client->request('POST', "{$this->LOTO_API_HOST}GetUserTransactionHistory", [
            'body' => json_encode([
                'Token' => $token,
                'fromDate' =>  date('Y-m-d'),
                'toDate' => date('Y-m-d', strtotime("+1 day")),
                'transactionType' => 0
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $content = $response->toArray();

        $historyEntries = $content['d']['historyEntries'];
        foreach ($historyEntries as $historyEntries) {
            $historyId[] = $historyEntries['historyId'];
        }
        $historyId = $historyId[0];

        return $historyId;
    }

    public function playLoto($draw, $withZeed, $gridselected)
    {
        // date('Y-m-d'),
        //         'toDate' => date('Y-m-d',strtotime("+1 day"))
        $token = $this->Login();
        $response = $this->client->request('POST', "{$this->LOTO_API_HOST}SubmitLotoPlayOrder", [
            'body' => json_encode([
                'Token' => $token,
                'drawNumber' => $draw,
                'numDraws' => 1,
                'withZeed' => $withZeed,
                'saveToFavorite' => 1,
                'GridsSelected' => $gridselected
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $content = $response->toArray();

        $submit = $content['d']['errorinfo']['errorcode'];
        if ($submit == 0) {
            $zeed = $content['d']['insertId'];
            return array(true, $zeed);
        } else {
            return array(false);
        }
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

        if($status == 500){
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
        if($status == 500){
            return false;
        }

        $content = $response->toArray(false);

        $date=$content['d']['draws'][0]['drawdate'];
        $nextdrawdetails=$content['d']['draws'][1];

        return array($date,$nextdrawdetails);
    }
}
