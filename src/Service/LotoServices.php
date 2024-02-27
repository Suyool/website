<?php

namespace App\Service;

use App\Entity\Loto\loto;
use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_results;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Utils\Helper;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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

                $content = $response->getContent();
                $content = $response->toArray();

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
                    sleep(3);
                    $retryattempt++;
                }
            }

            return array($content,  json_encode(["Token" => $login, "category" => $category, "type" => $type,]));
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
                return array(true, $content['d']['insertId']);
            } else if ($submit == 4 || $submit == 6 || $submit == 9) {
                $error = $content['d']['errorinfo']['errormsg'];
                return array(false, $submit, $error);
            } else {
                sleep(10);
                $this->loggerInterface->info("attemp " . $retryattempt);
                $this->loggerInterface->error($content['d']['errorinfo']['errormsg']);
                $retryattempt++;
            }
        }
        $error = $content['d']['errorinfo']['errormsg'];
        return array(false, $submit, $error);
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

    public function _getResultsFromData($userId,$mr,$request,$lotoService,$data){
        if (isset($data)) {
            $suyoolUserId = $userId;
            $loto_draw = $mr->getRepository(LOTO_draw::class)->findOneBy([], ['drawdate' => 'DESC']);


            $loto_prize_result = $mr->getRepository(LOTO_results::class)->findBy([], ['drawdate' => 'desc']);

            $data = json_decode($request->getContent(), true);

            $drawId = $data['drawNumber'];
            $loto_prize = $mr->getRepository(LOTO_results::class)->findOneBy(['drawId' => $drawId]);
            if ($loto_prize != null) {
                $loto_prize_per_days = $mr->getRepository(loto::class)->getResultsPerUser($suyoolUserId, $loto_prize->getDrawId(), $lotoService,$loto_draw->getdrawid());
                // dd($loto_prize_per_days);
            } else {
                $loto_prize_per_days = $mr->getRepository(loto::class)->getfetchhistory($suyoolUserId, $drawId,$loto_draw->getdrawid());
                // dd($loto_prize_per_days);
            }

            if ($loto_prize != null) {
                $loto_prize_array = [
                    'numbers' => $loto_prize->getnumbers(),
                    'prize1' => $loto_prize->getwinner1(),
                    'prize2' => $loto_prize->getwinner2(),
                    'prize3' => $loto_prize->getwinner3(),
                    'prize4' => $loto_prize->getwinner4(),
                    'prize5' => $loto_prize->getwinner5(),
                    'zeednumbers' => $loto_prize->getzeednumber1(),
                    'zeednumbers2' => $loto_prize->getzeednumber2(),
                    'zeednumbers3' => $loto_prize->getzeednumber3(),
                    'zeednumbers4' => $loto_prize->getzeednumber4(),
                    'prize1zeed' => $loto_prize->getwinner1zeed(),
                    'prize2zeed' => $loto_prize->getwinner2zeed(),
                    'prize3zeed' => $loto_prize->getwinner3zeed(),
                    'prize4zeed' => $loto_prize->getwinner4zeed(),
                    'date' => $loto_prize->getdrawdate()
                ];
            } else {
                $loto_prize_array = [
                    'numbers' => '',
                    'prize1' => '',
                    'prize2' => '',
                    'prize3' => '',
                    'prize4' => '',
                    'prize5' => '',
                    'zeednumbers' => '',
                    'zeednumbers2' => '',
                    'zeednumbers3' => '',
                    'zeednumbers4' => '',
                    'prize1zeed' => '',
                    'prize2zeed' => '',
                    'prize3zeed' => '',
                    'prize4zeed' => '',
                    'date' => ''
                ];
            }

            $parameters['prize_loto_win'] = $loto_prize_array;
            $prize_loto_perdays = [];
            foreach ($loto_prize_per_days as $days) {
                foreach ($days['gridSelected'] as $gridselected) {
                    $grids[] = $gridselected;
                }
                $date = new DateTime($days['date']);

                $prize_loto_perdays[] = [
                    'month' => $date->format('M'),
                    'day' => $date->format('d'),
                    'date' => $date->format('l'),
                    'year' => $date->format('Y'),
                    'drawNumber' => $days['drawId'],
                    'gridSelected' => $grids,
                ];
                $prize_loto_result[] = [
                    'month' => $date->format('M'),
                    'day' => $date->format('d'),
                    'date' => $date->format('l'),
                    'year' => $date->format('Y'),
                    'drawNumber' => $days['drawId']
                ];
            }

            foreach ($loto_prize_result as $result) {
                $prize_loto_result[] = [
                    'month' => $result->getdrawdate()->format('M'),
                    'day' => $result->getdrawdate()->format('d'),
                    'date' => $result->getdrawdate()->format('l'),
                    'year' => $result->getdrawdate()->format('Y'),
                    'drawNumber' => $result->getdrawid()
                ];
            }

            $parameters['prize_loto_perdays'] = $prize_loto_perdays;
            $parameters['prize_loto_result'] = $prize_loto_result;

            // dd($parameters);
            return $parameters;
        }
    }
}
