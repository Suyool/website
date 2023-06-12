<?php

namespace App\Controller;

use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_numbers;
use App\Entity\Loto\LOTO_tickets;
use App\Entity\Loto\Table1;
use App\Utils\Helper;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class LotoController extends AbstractController
{
    private $mr;
    public function __construct(ManagerRegistry $mr)
    {
        $this->mr=$mr->getManager('loto');
    }
    /**
     * @Route("/loto", name="app_loto")
     */
    public function index(Request $request,ManagerRegistry $em)
    {
        $loto_draw=$this->mr->getRepository(LOTO_draw::class)->findOneBy([],['drawdate'=>'DESC']);
        // $loto_tikctes=$this->mr->getRepository(LOTO_tickets::class)->findOneBy([],['create_date'=>'DESC']);
        $loto_numbers=$this->mr->getRepository(LOTO_numbers::class)->findAll();

        // dd($loto_draw);

        // foreach($loto_draw as $loto_draw){
            if($loto_draw){
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
            if($loto_numbers){
                foreach($loto_numbers as $loto_numbers){
                    $gridpricematrix[]=[
                        'numbers'=>$loto_numbers->getnumbers(),
                        'price'=>$loto_numbers->getprice(),
                        'zeed'=>$loto_numbers->getzeed(),
                    ];
                }
                $parameters['gridpricematrix']=$gridpricematrix;
            }
            

        // }
        $parameters['unit_price']=$gridpricematrix[0]['price'];
        
    //    dd($parameters);

        $next_date = new DateTime($parameters['next_date']);
        // $interval = new DateInterval('PT3H');
        // $next_date->add($interval);
        $parameters['next_date'] = $next_date->format('l, M d Y H:i:s');

        // $get_price_grid_form_data=["Token"=>"","Grid"=>"B1"];
        // $gridpriceparams['data']=json_encode($get_price_grid_form_data);
        $gridpriceparams['url'] = "/Servicev2.asmx/GetGridandZeedPrice";
        $gridpriceresponse = Helper::send_curl($gridpriceparams,'loto');

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
        $ResponseGetFullGridPriceMatrix = Helper::send_curl($GetFullGridPriceMatrixparams,'loto');

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

        $drawnumber = $parameters['next_draw_number'];
        $gridselected = "2 3 4 5 6 7 1 | 10 22 39 44 33 11 23";

        $submit_loto_play = [
            'Token' => '30b7ea61-5319-4dc3-81a2-bf339e625a1e',
            'drawNumber' => $drawnumber,
            'numDraws' => $drawnumber,
            'withZeed' => 0, //1 if with zeed
            'saveToFavorite' => 0, //1 TO ADD TO FAVORITE,
            'GridsSelected' => $gridselected, // separated with |
        ];

        $submitloto['data'] = json_encode($submit_loto_play);
        $submitloto['url'] = "/Servicev2.asmx/SubmitLotoPlayOrder";
        $ResponseGetFullGridPriceMatrix = Helper::send_curl($submitloto,'loto');

        $submit_loto = json_decode($ResponseGetFullGridPriceMatrix, true);
        if ($submit_loto['d']['errorinfo']['errorcode'] > 0) {
            $parameters['submit_loto'] = $submit_loto['d']['errorinfo'];
        } else {
            $parameters['submit_loto'] = ['insertId' => $submit_loto['d']['insertId'], 'balance' => $submit_loto['d']['balance'], 'token' => $submit_loto['d']['token']];
        }
        // dd($submit_loto);
        // $parameters['matrix'] = $pricematrixarray;
        $gridnumber = "2 3 4 5 6 7 1";
        $grid_price_by_number = [
            'Token' => '30b7ea61-5319-4dc3-81a2-bf339e625a1e',
            'Grid' => $gridnumber
        ];

        $gridpricebynum['data'] = json_encode($grid_price_by_number);
        $gridpricebynum['url'] = "/Servicev2.asmx/GetGridPrice";
        $gridpricebynumResponse = Helper::send_curl($gridpricebynum,'loto');

        $gridpricebynumData = json_decode($gridpricebynumResponse, true);
        $parameters['gridpricebynum'] = $gridpricebynumData['d'];
        $parameters['gridpricematrix'] = $pricematrixarray;


        dd($parameters);
        return $this->render('loto/index.html.twig', [
            'parameters' => $parameters
        ]);
    }
}