<?php

namespace App\Command;

use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_numbers;
use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\LOTO_tickets;
use App\Utils\Helper;
use DateInterval;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LotoDrawData extends Command
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
            ->setName('app:loto');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Next Draw Data Information Added'
        ]);

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
            $LOTO_draw->setcreatedate($date);

            $this->mr->persist($LOTO_draw);
            $this->mr->flush();
        }
        $GetFullGridPriceMatrixparams['url'] = "/Servicev2.asmx/GetFullGridPriceMatrix";
        $ResponseGetFullGridPriceMatrix = Helper::send_curl($GetFullGridPriceMatrixparams, 'loto');

        $GetFullGridPriceMatrix = json_decode($ResponseGetFullGridPriceMatrix, true);
        // $loto_ticket=$this->mr->getRepository(LOTO_tickets::class)->findOneBy(['loto_ticket'=>$GetFullGridPriceMatrix['d']['unitprice'],'zeed_ticket'=>$GetFullGridPriceMatrix['d']['zeedprice']]);

        // // dd($GetFullGridPriceMatrix);
        // if($GetFullGridPriceMatrix && !$loto_ticket){
        //     $LOTO_tickets->setloto_ticket($GetFullGridPriceMatrix['d']['unitprice']);
        //     $LOTO_tickets->setzeed_ticket($GetFullGridPriceMatrix['d']['zeedprice']);
        //     $LOTO_tickets->setcreatedate($date);
        //     $this->mr->persist($LOTO_tickets);
        //     $this->mr->flush();
        // }

        $loto_numbers = $GetFullGridPriceMatrix['d']['pricematrix'];
        $numbers = 6;
        foreach ($loto_numbers as $number_price) {
            // $num[]= $number_price['price0J'];
            // dd();
            if (!$this->mr->getRepository(LOTO_numbers::class)->findOneBy(['price' => $number_price['price0J']])) {
                $LOTO_numbers = new LOTO_numbers;

                $LOTO_numbers->setnumbers($numbers);
                $LOTO_numbers->setprice($number_price['price0J']);
                $LOTO_numbers->setzeed($GetFullGridPriceMatrix['d']['zeedprice']);
                $LOTO_numbers->setcreatedate($date);
                $this->mr->persist($LOTO_numbers);
                $this->mr->flush();
                $numbers++;
            }
        }

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
                $drawdate=strtotime($prize_loto['drawdate']);
                if(!$this->mr->getRepository(LOTO_results::class)->findBy(['drawId'=>$prize_loto['drawnumber']])){
                $numbers = [$prize_loto['B1'], $prize_loto['B2'], $prize_loto['B3'], $prize_loto['B4'], $prize_loto['B5'], $prize_loto['B6'], $prize_loto['B7']];
                $numbers = implode(",", $numbers);
                // dd($prize_loto['drawdate']->forma);
               $drawdate=strtotime($prize_loto['drawdate'].'+1 hour');
            //    dd($drawdate);
            //    dd(date('Y-m-d H:i:s',$drawdate));
                $results->setdrawid($prize_loto['drawnumber']);
                $results->setnumbers($numbers);
                $results->setdrawdate(date('Y-m-d H:i:s',$drawdate));
                $results->setwinner1($prize_loto['prize1']);
                $results->setwinner2($prize_loto['prize2']);
                $results->setwinner3($prize_loto['prize3']);
                $results->setwinner4($prize_loto['prize4']);
                $results->setwinner5($prize_loto['prize5']);
                $results->setcreatedate($date);
                $this->mr->persist($results);
                $this->mr->flush();
    
            }
        }
   
        // dd(implode(",",$numbers));
        // var_dump($numbers);
        // dd($prize_loto);

        // dd($NextDraw);


        return 1;
    }
}
