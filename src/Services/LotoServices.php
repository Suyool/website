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
        $token=$content['d']['token'];

        return $token;
    }

    public function BouquetGrids()
    {
        $token=$this->Login();
        $response1 = $this->client->request('POST', "{$this->LOTO_API_HOST}GetUserLotoTransactionHistoryDetail", [
            'body' => json_encode([
                'Token' => $token,
                'historyId' => 5191419,
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
                'historyId' => 5191419,
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
        $token=$this->Login();
        $response = $this->client->request('POST', "{$this->LOTO_API_HOST}GetUserTransactionHistory", [
            'body' => json_encode([
                'Token' => $token,
                'fromDate' => '2023-06-20',
                'toDate' => '2023-06-21',
                'transactionType'=>0
            ]),
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
        $content = $response->toArray();

        $historyEntries=$content['d']['historyEntries'];
        foreach($historyEntries as $historyEntries)
        {
            $historyId[]=$historyEntries['historyId'];
        }
        dd($historyId[0]);
        
        return $content;

    }
}
