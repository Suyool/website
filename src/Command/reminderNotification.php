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

class reminderNotification extends Command
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
            ->setName('app:reminder');
        //reminder users if played loto once every monday and thursday and don't play loto for 6 months at 10am
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Notification reminder send'
        ]);
        dd($this->mr->getRepository(loto::class)->findPlayedUserAndDontPlayThisWeek());
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

        foreach($notifyUser as $notifyUser)
        {
            $orderid=$this->mr->getRepository(order::class)->findOneBy(['id'=>$notifyUser['id']]);
            $notification = new notification;
                    $notification->setIdentifier('Loto result');
                    $notification->setTitle("Draw ". $notifyUser['drawNumber'] ." results");
                    $notification->setNotify("Balls: ". $notifyUser['numbers'] . "<br>Next Estimate Jackbot LBP " . $lastresultprice->getlotoprize());
                    $notification->setSubject("Draw ". $notifyUser['drawNumber'] ." results");
                    $notification->setOrderId($orderid);
                    $notification->settransId($orderid->gettransId());
                    $notification->setText("Balls: ". $notifyUser['numbers'] . "<br>Next Estimate Jackbot LBP " . $lastresultprice->getlotoprize());
                    // $notification->setGrids($i);
                    $notification->setamount($orderid->getamount());
                    $notification->setcurrency($orderid->getcurrency());
                    // $notification->setDrawId($drawId);
                    // $notification->setResultDate($drawId->getdrawdate()->format('Y-m-d H:i:s'));
                    // $notification->setzeed($lotoidcompletedtonot->getwithZeed());
                    // $notification->setbouquet($lotoidcompletedtonot->getbouquet());


                    $this->mr->persist($notification);
                    $this->mr->flush();
        }


        return 1;
    }
}
