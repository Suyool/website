<?php

namespace App\Command\loto;

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

        // $next_draw_form_data = ["Token" => ""];
        // $NextDrawparams['data'] = json_encode($next_draw_form_data);
        // // $params['type']='post';
        // // $params['url'] = 'https://backbone.lebaneseloto.com/Servicev2.asmx/GetAllDraws';
        // /*** Call the api ***/
        // // $response = Helper::sendCurl($params['url'],$form_data);
        // $NextDrawparams['url'] = "/Servicev2.asmx/GetInPlayAndNextDrawInformation";

        // $NextDrawresponse = Helper::send_curl($NextDrawparams, 'loto');
        // $NextDraw = json_decode($NextDrawresponse, true);

        // // dd($response);
        // $LOTO_draw = new LOTO_draw;
        // // $LOTO_tickets=new LOTO_tickets;
        // $next_date = new DateTime($NextDraw['d']['draws'][0]['drawdate']);
        // $interval = new DateInterval('PT3H');
        // $next_date->add($interval);

        // $loto_draw = $this->mr->getRepository(LOTO_draw::class)->findOneBy(['drawId' => $NextDraw['d']['draws'][1]['drawnumber']]);


        // $date = new DateTime();
        // if ($NextDraw && !$loto_draw) {
        //     $LOTO_draw->setdrawid($NextDraw['d']['draws'][1]['drawnumber']);
        //     $LOTO_draw->setdrawdate($next_date);
        //     $LOTO_draw->setlotoprize($NextDraw['d']['draws'][1]['lotojackpotLBP']);
        //     $LOTO_draw->setzeedprize($NextDraw['d']['draws'][1]['zeedjackpotLBP']);

        //     $this->mr->persist($LOTO_draw);
        //     $this->mr->flush();
        // }
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
        // Check if any rows need to be deleted
        $deleteRows = false;

        foreach ($loto_numbers as $number_price) {
            if (!$this->mr->getRepository(LOTO_numbers::class)->findOneBy(['price' => $number_price['price0J']])) {
                $deleteRows = true;
                break;
            }
        }

        // Delete existing rows if required
        if ($deleteRows) {
            $repository = $this->mr->getRepository(LOTO_numbers::class);
            $existingNumbers = $repository->findAll();

            foreach ($existingNumbers as $existingNumber) {
                $this->mr->remove($existingNumber);
            }

            $this->mr->flush();
        }

        // Insert new rows
        $numbers = 6;

        foreach ($loto_numbers as $number_price) {
            if (!$this->mr->getRepository(LOTO_numbers::class)->findOneBy(['price' => $number_price['price0J']])) {
                $LOTO_numbers = new LOTO_numbers;
                $LOTO_numbers->setnumbers($numbers);
                $LOTO_numbers->setprice($number_price['price0J']);
                $LOTO_numbers->setzeed($GetFullGridPriceMatrix['d']['zeedprice']);
                $this->mr->persist($LOTO_numbers);
                $this->mr->flush();

                $numbers++;
            }
        }

        

        // dd(implode(",",$numbers));
        // var_dump($numbers);
        // dd($prize_loto);

        // dd($NextDraw);


        return 1;
    }
}
