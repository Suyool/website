<?php

namespace App\Controller;

use App\Utils\Helper;
use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class LotoController extends AbstractController
{
    /**
     * @Route("/loto", name="app_loto")
     */
    public function index(Request $request)
    {
        $next_draw_form_data = ["Token" => ""];
        $NextDrawparams['data'] = json_encode($next_draw_form_data);
        // $params['type']='post';
        // $params['url'] = 'https://backbone.lebaneseloto.com/Servicev2.asmx/GetAllDraws';
        /*** Call the api ***/
        // $response = Helper::sendCurl($params['url'],$form_data);
        $NextDrawparams['url'] = "/Servicev2.asmx/GetInPlayAndNextDrawInformation";

        $NextDrawresponse = Helper::send_curl($NextDrawparams);
        // dd($response);

        $NextDraw = json_decode($NextDrawresponse, true);

        // dd($NextDraw);
        if ($NextDraw) {
            $parameters['next_draw_number'] = $NextDraw['d']['draws'][0]['drawnumber'];
            $parameters['next_loto_win'] = $NextDraw['d']['draws'][1]['lotojackpotLBP'];
            $parameters['next_zeed_win'] = $NextDraw['d']['draws'][1]['zeedjackpotLBP'];
            $parameters['next_date'] = $NextDraw['d']['draws'][0]['drawdate'];
        }


        $next_date = new DateTime($parameters['next_date']);
        $interval = new DateInterval('PT3H');
        $next_date->add($interval);
        $parameters['next_date'] = $next_date->format('l, M d Y H:i:s');

        // $get_price_grid_form_data=["Token"=>"","Grid"=>"B1"];
        // $gridpriceparams['data']=json_encode($get_price_grid_form_data);
        $gridpriceparams['url'] = "/Servicev2.asmx/GetGridandZeedPrice";
        $gridpriceresponse = Helper::send_curl($gridpriceparams);

        $gridprice = json_decode($gridpriceresponse, true);
        // dd($gridprice);
        $onegridprice = (int) $gridprice['d']['stringvalue1'];
        $parameters['Zeedgridprice'] = $gridprice['d']['stringvalue2'];
        $parameters['gridprice'] = [
            'grid1' => $onegridprice,
            'grid8' => $onegridprice * 8,
            'grid25' => $onegridprice * 25,
            'grid50' => $onegridprice * 50,
            'grid100' => $onegridprice * 100,
            'grid500' => $onegridprice * 500
        ];
        // $parameters['B8gridprice'] = $parameters['B1gridprice'] * 8;

        // dd($gridprice);

        $GetFullGridPriceMatrixparams['url'] = "/Servicev2.asmx/GetFullGridPriceMatrix";
        $ResponseGetFullGridPriceMatrix = Helper::send_curl($GetFullGridPriceMatrixparams);

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
        $ResponseGetFullGridPriceMatrix = Helper::send_curl($submitloto);

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
        $gridpricebynumResponse = Helper::send_curl($gridpricebynum);

        $gridpricebynumData = json_decode($gridpricebynumResponse, true);
        $parameters['gridpricebynum'] = $gridpricebynumData['d'];
        $parameters['gridpricematrix'] = $pricematrixarray;


        // dd($parameters);
        return $this->render('loto/index.html.twig', [
            'parameters' => $parameters
        ]);
    }
}