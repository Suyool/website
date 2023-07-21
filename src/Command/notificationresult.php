<?php

namespace App\Command;

use App\Entity\Loto\loto;
use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_numbers;
use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\LOTO_tickets;
use App\Entity\Loto\notification;
use App\Entity\Loto\order;
use App\Utils\Helper;
use DateInterval;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class notificationresult extends Command
{
    private $mr;
    public function __construct(ManagerRegistry $mr)
    {
        parent::__construct();

        $this->mr = $mr->getManager('loto');
    }

    protected function configure()
    {
        //php bin/console 
        $this
            ->setName('app:result');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Notification result send'
        ]);

        $token_prize = [
            'Token' => '',
            'from' => 0,
            'to' => 0
        ];
        $prize['data'] = json_encode($token_prize);
        $prize['url'] = '/Service.asmx/GetDrawsInformation';

        $reponseprize = Helper::send_curl($prize, 'loto');
        $prize_loto = json_decode($reponseprize, true);
        // dd($prize_loto);
        // $drawdate=strtotime($prize_loto['d']['draws'][0]['drawdate']);
        // dd($drawdate);
        // dd($prize_loto);
        foreach ($prize_loto['d']['draws'] as $prize_loto) {
            $results = new LOTO_results;
            $drawdate = strtotime($prize_loto['drawdate']);
            if (!$this->mr->getRepository(LOTO_results::class)->findBy(['drawId' => $prize_loto['drawnumber']])) {
                $numbers = [$prize_loto['B1'], $prize_loto['B2'], $prize_loto['B3'], $prize_loto['B4'], $prize_loto['B5'], $prize_loto['B6'], $prize_loto['B7']];
                $numbers = implode(",", $numbers);
                // dd($prize_loto['drawdate']->forma);
                $drawdate = strtotime($prize_loto['drawdate'] . '+1 hour');
                $time = new DateTime();
                $time->setTimestamp($drawdate);
                //    dd(date('Y-m-d H:i:s',$drawdate));
                $results->setdrawid($prize_loto['drawnumber']);
                $results->setnumbers($numbers);
                $results->setdrawdate($time);
                $results->setwinner1($prize_loto['prize1']);
                $results->setwinner2($prize_loto['prize2']);
                $results->setwinner3($prize_loto['prize3']);
                $results->setwinner4($prize_loto['prize4']);
                $results->setwinner5($prize_loto['prize5']);
                $results->setzeednumber1($prize_loto['zeednumber']);
                $results->setzeednumber2($prize_loto['zeednumber2']);
                $results->setzeednumber3($prize_loto['zeednumber3']);
                $results->setzeednumber4($prize_loto['zeednumber4']);
                $results->setwinner1zeed($prize_loto['zeedprize1']);
                $results->setwinner2zeed($prize_loto['zeedprize2']);
                $results->setwinner3zeed($prize_loto['zeedprize3']);
                $results->setwinner4zeed($prize_loto['zeedprize4']);
                $this->mr->persist($results);
                $this->mr->flush();
            }
        }

        $lastdraw=$this->mr->getRepository(LOTO_draw::class)->findOneBy([],['drawdate'=>'desc']);
        // dd(($lastdraw->getdrawid()));
        $drawid=$lastdraw->getdrawid();
        $notifyUser=$this->mr->getRepository(loto::class)->findPlayedUser($drawid);

        $next_draw_form_data = ["Token" => ""];
        $NextDrawparams['data'] = json_encode($next_draw_form_data);
        // $params['type']='post';
        // $params['url'] = 'https://backbone.lebaneseloto.com/Servicev2.asmx/GetAllDraws';
        /*** Call the api ***/
        // $response = Helper::sendCurl($params['url'],$form_data);
        $NextDrawparams['url'] = "/Servicev2.asmx/GetInPlayAndNextDrawInformation";

        $NextDrawresponse = Helper::send_curl($NextDrawparams, 'loto');
        $NextDraw = json_decode($NextDrawresponse, true);

        // dd($response);
        $LOTO_draw = new LOTO_draw;
        // $LOTO_tickets=new LOTO_tickets;
        $next_date = new DateTime($NextDraw['d']['draws'][0]['drawdate']);
        $interval = new DateInterval('PT3H');
        $next_date->add($interval);

        $loto_draw = $this->mr->getRepository(LOTO_draw::class)->findOneBy(['drawId' => $NextDraw['d']['draws'][1]['drawnumber']]);


        $date = new DateTime();
        if ($NextDraw && !$loto_draw) {
            $LOTO_draw->setdrawid($NextDraw['d']['draws'][1]['drawnumber']);
            $LOTO_draw->setdrawdate($next_date);
            $LOTO_draw->setlotoprize($NextDraw['d']['draws'][1]['lotojackpotLBP']);
            $LOTO_draw->setzeedprize($NextDraw['d']['draws'][1]['zeedjackpotLBP']);

            $this->mr->persist($LOTO_draw);
            $this->mr->flush();
        }

        $lastresultprice = $this->mr->getRepository(LOTO_draw::class)->findOneBy([],['drawdate'=>'desc']);

        // foreach($notifyUser as $notifyUser)
        // {
        //     $orderid=$this->mr->getRepository(order::class)->findOneBy(['id'=>$notifyUser['id']]);
        //     $notification = new notification;
        //             $notification->setIdentifier('Loto result');
        //             $notification->setTitle("Draw ". $notifyUser['drawNumber'] ." results");
        //             $notification->setNotify("Balls: ". $notifyUser['numbers'] . "<br>Next Estimate Jackbot LBP " . $lastresultprice->getlotoprize());
        //             $notification->setSubject("Draw ". $notifyUser['drawNumber'] ." results");
        //             $notification->setOrderId($orderid);
        //             $notification->settransId($orderid->gettransId());
        //             $notification->setText("Balls: ". $notifyUser['numbers'] . "<br>Next Estimate Jackbot LBP " . $lastresultprice->getlotoprize());
        //             // $notification->setGrids($i);
        //             $notification->setamount($orderid->getamount());
        //             $notification->setcurrency($orderid->getcurrency());
        //             // $notification->setDrawId($drawId);
        //             // $notification->setResultDate($drawId->getdrawdate()->format('Y-m-d H:i:s'));
        //             // $notification->setzeed($lotoidcompletedtonot->getwithZeed());
        //             // $notification->setbouquet($lotoidcompletedtonot->getbouquet());


        //             $this->mr->persist($notification);
        //             $this->mr->flush();
        // }


        return 1;
    }
}
