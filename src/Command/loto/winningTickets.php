<?php

namespace App\Command\loto;

use App\Entity\Loto\loto;
use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\order;
use App\Entity\Notification\content;
use App\Entity\Notification\Template;
use App\Service\LotoServices;
use App\Service\NotificationServices;
use App\Service\sendEmail;
use App\Service\SuyoolServices;
use DateInterval;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class winningTickets extends Command
{
    private $mr;
    private $lotoServices;
    private $sendEmail;
    private $notificationServices;
    private $notifyMr;
    private $suyoolServices;
    private $logger;
    public function __construct(ManagerRegistry $mr, LotoServices $lotoServices, sendEmail $sendEmail, NotificationServices $notificationServices, SuyoolServices $suyoolServices, LoggerInterface $logger)
    {
        parent::__construct();

        $this->lotoServices = $lotoServices;
        $this->mr = $mr->getManager('loto');
        $this->sendEmail = $sendEmail;
        $this->notificationServices = $notificationServices;
        $this->notifyMr = $mr->getManager('notification');
        $this->suyoolServices = $suyoolServices;
        $this->logger = $logger;
    }

    protected function configure()
    {
        //php bin/console 
        $this
            ->setName('app:winning');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Winning details send'
        ]);

        $listWinners = [];


        $getLastResults = $this->mr->getRepository(LOTO_results::class)->findOneBy([], ['drawdate' => 'DESC']);

        $drawId = $getLastResults->getdrawid();
        $winningBalls[] = $getLastResults->getnumbers();
        $winningBallsExplode[] = explode(",", $winningBalls[0]);
        $winningBallsZeed['prize1'] = $getLastResults->getzeednumber1();
        $winningBallsZeed['prize2'] = $getLastResults->getzeednumber2();
        $winningBallsZeed['prize3'] = $getLastResults->getzeednumber3();
        $winningBallsZeed['prize4'] = $getLastResults->getzeednumber4();

        $getGridsinThisDraw = $this->mr->getRepository(loto::class)->findgridsInThisDraw($drawId);
        // dd($getGridsinThisDraw);
        $body = [];
        foreach ($getGridsinThisDraw as $gridsTobeUpdated) {
            $loto = 0;
            $zeed = 0;
            $gridsWin = [];
            $gridsWinLoto = [];
            $gridsWinZeed = [];
            $winning = $this->lotoServices->GetWinTicketsPrize($gridsTobeUpdated->getticketId());
            // $winning=[
            //     "d" => [
            //       "grids" => [
            //         [
            //           "drawDate" => "2023-10-22T21:00:00.0000000Z",
            //           "zeedNumber" => "000-1",
            //           "zeedWinnings" => 0,
            //           "lotoWinnings" => 200000,
            //           "gridValidation" => "8000-4403073",
            //           "gridId" => 3945246,
            //           "gridBalls" => "9 16 32 33 35 37",
            //           "drawNumber" => 2155,
            //           "gridPrice" => 50000,
            //           "isFavorite" => null
            //         ],
            //          [
            //           "drawDate" => "2023-10-22T21:00:00.0000000Z",
            //           "zeedNumber" => "12345",
            //           "zeedWinnings" => 20000,
            //           "lotoWinnings" => 0,
            //           "gridValidation" => "8000-4403074",
            //           "gridId" => 3945247,
            //           "gridBalls" => "2 22 30 31 35 37",
            //           "drawNumber" => 2155,
            //           "gridPrice" => 50000,
            //           "isFavorite" => null,
            //          ],
            //         [
            //           "drawDate" => "2023-10-22T21:00:00.0000000Z",
            //           "zeedNumber" => "000-1",
            //           "zeedWinnings" => 0,
            //           "lotoWinnings" => 200000,
            //           "gridValidation" => "8000-4403075",
            //           "gridId" => 3945248,
            //           "gridBalls" => "10 25 26 28 39 41",
            //           "drawNumber" => 2155,
            //           "gridPrice" => 50000,
            //           "isFavorite" => null,
            //         ]
            //     ]
            //       ]];
            if (isset($winning['d']['grids'])) {
                foreach ($winning['d']['grids'] as $winnings) {
                    $loto += $winnings['lotoWinnings'];
                    $zeed += $winnings['zeedWinnings'];
                }
                foreach ($winning['d']['grids'] as $winning) {
                    if ($winning['lotoWinnings'] != 0) {
                        $gridsWinLoto[] = [
                            $winning['gridBalls']
                        ];
                    }
                    if ($winning['zeedWinnings'] != 0) {
                        $gridsWinZeed[] = [
                            $winning['zeedNumber']
                        ];
                    }
                }
            }
            if ($loto != 0 || $zeed != 0) {
                $gridsTobeUpdated->setisWon(true);
            } elseif ($loto == 0 && $zeed == 0) {
                $gridsTobeUpdated->setisWon(false);
            }
            $gridsTobeUpdated->setwinloto($loto);
            $gridsTobeUpdated->setwinzeed($zeed);
            $this->mr->persist($gridsTobeUpdated);
            $this->mr->flush();
            if (!empty($gridsWinLoto) || !empty($gridsWinZeed)) {
                $body[] = [
                    'ticketId' => $gridsTobeUpdated->getticketId(),
                    'Loto' => number_format($loto),
                    'Zeed' => number_format($zeed),
                    'LotoNumbers' => json_encode($gridsWinLoto),
                    'ZeedNumbers' => json_encode($gridsWinZeed)
                ];
            }
        }
        $text = "Draw Number: {$drawId} <br> Loto winning numbers: {$getLastResults->getnumbers()} <br> Zeed winning numbers: {$getLastResults->getzeednumber1()} <br><br>";
        foreach ($body as $body) {
            $text .= "TicketId: {$body['ticketId']} , Loto: {$body['Loto']} , Zeed : {$body['Zeed']} , Loto grids win : {$body['LotoNumbers']} , Zeed numbers : {$body['ZeedNumbers']} <br>";
        }
        if ($_ENV['APP_ENV'] == 'prod') {
            $this->suyoolServices->sendDotNetEmail("{$drawId} Winning Tickets", 'aya.j@skash.com,anthony.saliba@elbarid.com', $text, "", "", "no-reply@suyool.com", "no-reply", 1, 0);
        } else {
            $this->suyoolServices->sendDotNetEmail("{$drawId} Winning Tickets", 'anthony.saliba@elbarid.com', $text, "", "", "suyool@noreply.com", "Suyool", 1, 0);
        }
        $getUsersWhoWon = $this->mr->getRepository(loto::class)->getUsersWhoWon($drawId);
        // dd($getUsersWhoWon);
        $this->logger->debug(json_encode($getUsersWhoWon));
        if (!empty($getUsersWhoWon)) {
            foreach ($getUsersWhoWon as $getUsersWhoWon) {
                $Amount = 0;
                foreach ($getUsersWhoWon['Amount'] as $amount) {
                    $Amount += $amount;
                }
                $orders = implode(",", $getUsersWhoWon['OrderID']);
                $tickets = implode(",", $getUsersWhoWon['TicketID']);
                $listWinners[] = ['UserAccountID' => (int)$getUsersWhoWon['UserAccountID'], 'Amount' => (float)$Amount, 'Currency' => 'LBP', 'OrderID' => $orders, 'TicketID' => $tickets];
            }

            $response = $this->suyoolServices->PushUserPrize($listWinners);
            if ($response[0]) {
                $data = json_decode($response[1], true);
                $this->logger->debug(json_encode($data));

                foreach ($data as $data) {
                    $order = explode(",", $data['OrderID']);
                    if ($data['FlagCode'] == 136) {
                        foreach ($order as $order) {
                            $loto = $this->mr->getRepository(loto::class)->getWinTicketsWinStNull($order, $drawId);
                            foreach ($loto as $loto) {
                                $loto->setwinningStatus('pending');
                                $this->mr->persist($loto);
                                $this->mr->flush();
                            }
                        }
                        $params = json_encode(['currency' => 'L.L', 'amount' => $data['Amount'], 'number' => $drawId]);
                        $content = $this->notificationServices->getContent('L1-ExceedMonthlyLimit');
                        $this->notificationServices->addNotification($data['UserAccountID'], $content, $params, 0);
                    } else if ($data['FlagCode'] == 135) {
                        foreach ($order as $order) {
                            $loto = $this->mr->getRepository(loto::class)->getWinTicketsWinStNull($order, $drawId);
                            foreach ($loto as $loto) {
                                $loto->setwinningStatus('redirected');
                                $this->mr->persist($loto);
                                $this->mr->flush();
                            }
                        }
                        $params = json_encode(['currency' => 'L.L', 'amount' => $data['Amount'], 'number' => $drawId]);
                        $content = $this->notificationServices->getContent('ExceedLimitMoreThanTenThousandsUSD');
                        $this->notificationServices->addNotification($data['UserAccountID'], $content, $params, 0);
                    } else if ($data['FlagCode'] == 1) {
                        foreach ($order as $order) {
                            $loto = $this->mr->getRepository(loto::class)->getWinTicketsWinStNull($order, $drawId);
                            foreach ($loto as $loto) {
                                $loto->setwinningStatus('paid');
                                $this->mr->persist($loto);
                                $this->mr->flush();
                            }
                        }
                        $params = json_encode(['currency' => 'L.L', 'amount' => $data['Amount'], 'number' => $drawId]);
                        $content = $this->notificationServices->getContent('won loto added to suyool wallet');
                        $this->notificationServices->addNotification($data['UserAccountID'], $content, $params, 0, "https://www.suyool.com/loto?goto=Result");
                    }
                }
            }
        }

        return 1;
    }
}
