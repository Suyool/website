<?php

namespace App\Controller;

use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_numbers;
use App\Entity\Loto\LOTO_plays;
use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\LOTO_tickets;
use App\Entity\Loto\Table1;
use App\Utils\Helper;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class LotoController extends AbstractController
{
    private $mr;
    private $session; 

    public function __construct(ManagerRegistry $mr,SessionInterface $session)
    {
        $this->mr = $mr->getManager('loto');
        $this->session=$session;
    }
    /**
     * @Route("/loto", name="app_loto")
     */
    public function index(Request $request, ManagerRegistry $em)
    {
        $printsession=$request->query->get('printsession');
        $loto_draw = $this->mr->getRepository(LOTO_draw::class)->findOneBy([], ['drawdate' => 'DESC']);
        // $loto_tikctes=$this->mr->getRepository(LOTO_tickets::class)->findOneBy([],['create_date'=>'DESC']);
        $loto_numbers = $this->mr->getRepository(LOTO_numbers::class)->findAll();
        $drawId = '2117';
        $loto_prize = $this->mr->getRepository(LOTO_results::class)->findOneBy(['drawId' => $drawId]);
        // echo "<pre>";print_r($_SESSION);die("--");
            
        if(isset($printsession)){
            dd($this->session->get('userId'));
        }
        // $this->session->set('userId',rand());
        // dd($this->session->get('userId'));

        // dd($loto_draw);

        // foreach($loto_draw as $loto_draw){
        if ($loto_draw) {
            $parameters['next_draw_number'] = $loto_draw->getdrawid();
            $parameters['next_loto_prize'] = $loto_draw->getlotoprize();
            $parameters['next_zeed_prize'] = $loto_draw->getzeedprize();
            $parameters['next_date'] = $loto_draw->getdrawdate();
            $parameters['next_date'] = $parameters['next_date']->format('l, M d Y H:i:s');
        }
        // if($loto_tikctes){
        //     $parameters['unit_price']=$loto_tikctes->getloto_ticket();
        //     $parameters['zeed_price']=$loto_tikctes->getzeed_ticket();
        // }
        // $parameters['gridpricematrix']=[];
        // dd($loto_numbers);
        if ($loto_numbers) {
            foreach ($loto_numbers as $loto_numbers) {
                $gridpricematrix[] = [
                    'numbers' => $loto_numbers->getnumbers(),
                    'price' => $loto_numbers->getprice(),
                    'zeed' => $loto_numbers->getzeed(),
                ];
            }
            $parameters['gridpricematrix'] = $gridpricematrix;
        }


        // }
        $parameters['unit_price'] = $gridpricematrix[0]['price'];

        //    dd($parameters);

        $next_date = new DateTime($parameters['next_date']);
        // $interval = new DateInterval('PT3H');
        // $next_date->add($interval);
        $parameters['next_date'] = $next_date->format('l, M d Y H:i:s');

        // $get_price_grid_form_data=["Token"=>"","Grid"=>"B1"];
        // $gridpriceparams['data']=json_encode($get_price_grid_form_data);
        $gridpriceparams['url'] = "/Servicev2.asmx/GetGridandZeedPrice";
        $gridpriceresponse = Helper::send_curl($gridpriceparams, 'loto');

        $gridprice = json_decode($gridpriceresponse, true);
        // dd($gridprice);
        $onegridprice = (int) $gridprice['d']['stringvalue1'];
        $parameters['Zeedgridprice'] = $gridprice['d']['stringvalue2'];
        $parameters['gridprice'] = [
            '1' => $parameters['unit_price'],
            '8' => $parameters['unit_price'] * 8,
            '25' => $parameters['unit_price'] * 25,
            '50' => $parameters['unit_price'] * 50,
            '100' => $parameters['unit_price'] * 100,
            '500' => $parameters['unit_price'] * 500

        ];
        // $parameters['B8gridprice'] = $parameters['B1gridprice'] * 8;

        // dd($gridprice);

        $GetFullGridPriceMatrixparams['url'] = "/Servicev2.asmx/GetFullGridPriceMatrix";
        $ResponseGetFullGridPriceMatrix = Helper::send_curl($GetFullGridPriceMatrixparams, 'loto');


        $GetFullGridPriceMatrix = json_decode($ResponseGetFullGridPriceMatrix, true);
        $numbers = 6;
        $pricematrixarray = [];
        foreach ($GetFullGridPriceMatrix['d']['pricematrix'] as $pricematrix) {
            if ($numbers < 11) {
                $pricematrixarray[] = [
                    'numbers' => $numbers,
                    'price' => $pricematrix['price0J'],
                ];
            }
            $numbers++;
        }


        // $gridselected = ["1,2,3,4,5,6", "11,7,8,9,10,12"];
        // foreach($gridselected as $grid){
        //     dd(explode(',',$grid));
        // }


        // $selected=implode("|",$gridselected);
        //     if(isset($_POST['submit'])){
        //         dd($_POST['getPlayedBalls']);
        //     }

        if (isset($_POST['submit'])) {

            $numDraws = 1;

            $drawnumber = $parameters['next_draw_number'];
            $withZeed = 1;

            $getPlayedBalls = json_decode($_POST['getPlayedBalls'], true);
            $ballsArray = [];

            $ballsArrayNoZeed = [];

            $selected = [];
            // $nozeed=0;

            // dd($getPlayedBalls);

            foreach ($getPlayedBalls as $item) {
                $plays = new LOTO_plays;


                // dd($ballsArray);
                $withZeed = $item['withZeed'];
                if ($withZeed == false) {
                    $balls = implode(" ", $item['balls']);
                    $ballsArrayNoZeed[] = $balls;
                    $withZeed = 0;
                    // $nozeed=1;
                } else {
                    $withZeed = 1;
                    $balls = implode(" ", $item['balls']);
                    $ballsArray = $balls;
                    //     $submit_loto_play = [
                    //         'Token' => '49414be0-d9c2-47e4-82f9-276fdc74157f',
                    //         'drawNumber' => $drawnumber,
                    //         'numDraws' => $numDraws,
                    //         'withZeed' => $withZeed, //1 if with zeed
                    //         'saveToFavorite' => 1, //1 TO ADD TO FAVORITE,
                    //         'GridsSelected' => $ballsArray, // separated with |
                    //     ];
                    //     $submitloto['data'] = json_encode($submit_loto_play);
                    // $submitloto['url'] = "/Servicev2.asmx/SubmitLotoPlayOrder";
                    // $SubmitPlays = Helper::send_curl($submitloto, 'loto');

                    // $submit_loto = json_decode($SubmitPlays, true);

                    // dd($submit_loto);

                    // if ($submit_loto['d']['errorinfo']['errorcode'] > 0) {
                    //     $parameters['submit_loto'] = $submit_loto['d']['errorinfo'];
                    // } else {
                    //     $parameters['submit_loto'] = ['insertId' => $submit_loto['d']['insertId'], 'balance' => $submit_loto['d']['balance'], 'token' => $submit_loto['d']['token']];
                    //     $plays->setgridSelected($ballsArray)
                    //         ->setWithZeed($withZeed)
                    //         ->setdrawnumber($drawnumber)
                    //         ->setnumdraws(1)
                    //         ->setcreatedate(new DateTime());

                    //     $this->mr->persist($plays);
                    //     $this->mr->flush();
                    // }
                    $plays->setgridSelected($ballsArray)
                        ->setWithZeed($withZeed)
                        ->setdrawnumber($drawnumber)
                        ->setnumdraws(1)
                        ->setcreatedate(new DateTime());

                    $this->mr->persist($plays);
                    $this->mr->flush();
                }
                // dd($withZeed);

            }

            // $gridselected = $ballsArray;
            if ($ballsArrayNoZeed != null) {
                $selected = implode('|', $ballsArrayNoZeed);
                $plays->setgridSelected($selected)
                    ->setWithZeed($withZeed)
                    ->setdrawnumber($drawnumber)
                    ->setnumdraws(1)
                    ->setcreatedate(new DateTime());

                $this->mr->persist($plays);
                $this->mr->flush();
            }
            // dd($gridselected);
            // $selected = implode('|', $gridselected);
            // dd($selected);



        }




        // dd("ok");


        // dd($selected);





        $parameters['gridpricematrix'] = $pricematrixarray;
        // $loto_prize_array=[];
        // dd($loto_prize);
        $loto_prize_array = [
            'prize1' => $loto_prize->getwinner1(),
            'prize2' => $loto_prize->getwinner2(),
            'prize3' => $loto_prize->getwinner3(),
            'prize4' => $loto_prize->getwinner4(),
            'prize5' => $loto_prize->getwinner5()
        ];

        $parameters['prize_loto_win'] = $loto_prize_array;




        // dd($parameters);
        return $this->render('loto/index.html.twig', [
            'parameters' => $parameters
        ]);
    }
}
