<?php

namespace App\Controller;

use App\Entity\Loto\loto;
use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_numbers;
use App\Entity\Loto\LOTO_plays;
use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\LOTO_tickets;
use App\Entity\Loto\notification;
use App\Entity\Loto\order;
use App\Entity\Loto\Table1;
use App\Utils\Helper;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class LotoController extends AbstractController
{
    private $mr;
    private $session;
    private $certificate;
    private $hash_algo;

    public function __construct(ManagerRegistry $mr, SessionInterface $session, $certificate, $hash_algo)
    {
        $this->mr = $mr->getManager('loto');
        $this->session = $session;
        $this->certificate = $certificate;
        $this->hash_algo = $hash_algo;
    }

    /**
     * @Route("/loto", name="app_loto")
     */
    public function index(Request $request, ManagerRegistry $em)
    {
        $printsession = $request->query->get('printsession');
        $loto_draw = $this->mr->getRepository(LOTO_draw::class)->findOneBy([], ['drawdate' => 'DESC']);
        $loto_numbers = $this->mr->getRepository(LOTO_numbers::class)->findPriceByNumbers(11);

        $loto_prize_per_days = $this->mr->getRepository(LOTO_results::class)->findBy([], ['drawdate' => 'desc']);

        $data = json_decode($request->getContent(), true);
        if (isset($data)) {
            $drawId = $data['drawNumber'];
            $loto_prize = $this->mr->getRepository(LOTO_results::class)->findOneBy(['drawId' => $drawId]);
        } else {
            $loto_prize = $this->mr->getRepository(LOTO_results::class)->findOneBy([], ['drawdate' => 'desc']);
        }

        // $this->session->set('userId', rand());
        $session=$this->session->get('userId');

        if ($loto_draw) {
            $parameters['next_draw_number'] = $loto_draw->getdrawid();
            $parameters['next_loto_prize'] = $loto_draw->getlotoprize();
            $parameters['next_zeed_prize'] = $loto_draw->getzeedprize();
            $parameters['next_date'] = $loto_draw->getdrawdate();
            $parameters['next_date'] = $parameters['next_date']->format('l, M d Y H:i:s');
        }

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


        $parameters['unit_price'] = $gridpricematrix[0]['price'];


        $next_date = new DateTime($parameters['next_date']);

        $parameters['next_date'] = $next_date->format('l, M d Y H:i:s');

        $gridpriceparams['url'] = "/Servicev2.asmx/GetGridandZeedPrice";
        $gridpriceresponse = Helper::send_curl($gridpriceparams, 'loto');

        $gridprice = json_decode($gridpriceresponse, true);
        $onegridprice = (int) $gridprice['d']['stringvalue1'];
        $parameters['Zeedgridprice'] = $gridprice['d']['stringvalue2'];
        $parameters['gridprice'] = [
            $parameters['unit_price']


        ];
        $loto_prize_array = [
            'numbers' => $loto_prize->getnumbers(),
            'prize1' => $loto_prize->getwinner1(),
            'prize2' => $loto_prize->getwinner2(),
            'prize3' => $loto_prize->getwinner3(),
            'prize4' => $loto_prize->getwinner4(),
            'prize5' => $loto_prize->getwinner5(),
            'date' => $loto_prize->getdrawdate()
        ];

        $parameters['prize_loto_win'] = $loto_prize_array;
        $prize_loto_perdays = [];
        foreach ($loto_prize_per_days as $days) {
            $prize_loto_perdays[] = [
                'month' => $days->getdrawdate()->format('M'),
                'day' => $days->getdrawdate()->format('d'),
                'date' => $days->getdrawdate()->format('l'),
                'year' => $days->getdrawdate()->format('Y'),
                'drawNumber' => $days->getdrawid()
            ];
        }

        $parameters['prize_loto_perdays'] = $prize_loto_perdays;

        if (isset($data)) {
            return new JsonResponse([
                'parameters' => $parameters
            ]);
        } else {
            if(isset($session)){
                return $this->render('loto/index.html.twig', [
                    'parameters' => $parameters
                ]);
            }else{
                return new JsonResponse([
                    'message'=>'Not found'
                ],404);
            }
            
        }
    }

    /**
     * @Route("/loto/play", name="app_loto_play",methods="POST")
     */
    public function play(Request $request)
    {
        $session = $this->session->get('userId');
        // $session=20;
        $loto_draw = $this->mr->getRepository(LOTO_draw::class)->findOneBy([], ['drawdate' => 'DESC']);
        if (isset($session)) {
            $data = json_decode($request->getContent(), true);

            $getPlayedBalls = $data['selectedBalls'];
            if ($getPlayedBalls != null) {
                $getPlayedBalls = json_decode($getPlayedBalls, true);
                $numDraws = 1;

                $drawnumber = $loto_draw->getdrawId();

                $ballsArray = [];

                $ballsArrayNoZeed = [];
                $amounttotal = 0;
                $selected = [];

                $order = new order;
                $order->setsuyoolUserId($session)
                    ->setstatus('pending');

                $this->mr->persist($order);
                $this->mr->flush();
                foreach ($getPlayedBalls as $item) {
                    $currency = $item['currency'];
                    $withZeed = $item['withZeed'];
                    if ($withZeed == false) {
                        $withZeed = 0;
                    } else {
                        $withZeed = 1;
                    }
                    if ($withZeed == false) {
                        $balls = implode(" ", $item['balls']);
                        $ballsArrayNoZeed[] = $balls;
                        $withZeed = 0;

                        $amounttotal += $item['price'];
                    } else {
                        $balls = implode(" ", $item['balls']);
                        $ballsArray = $balls;
                        $orderid = $this->mr->getRepository(order::class)->findBy(['suyoolUserId' => $session, 'status' => 'pending']);

                        foreach ($orderid as $orderid) {
                            $loto = new loto;
                            $loto->setOrderId($orderid)
                                ->setdrawnumber($drawnumber)
                                ->setnumdraws($numDraws)
                                ->setWithZeed(true)
                                ->setgridSelected($ballsArray)
                                ->setprice($item['price'])
                                ->setcurrency($currency)
                                ->setbouquet(false);

                            $this->mr->persist($loto);
                            $this->mr->flush();
                        }
                    }
                }
                if ($ballsArrayNoZeed != null) {
                    $selected = implode('|', $ballsArrayNoZeed);

                    $nozeed = 1;
                    $orderid = $this->mr->getRepository(order::class)->findOneBy(['suyoolUserId' => $session, 'status' => 'pending']);
                    $loto = new loto;
                    $loto->setOrderId($orderid)
                        ->setdrawnumber($drawnumber)
                        ->setnumdraws($numDraws)
                        ->setWithZeed(false)
                        ->setgridSelected($selected)
                        ->setprice($amounttotal)
                        ->setcurrency($currency)
                        ->setbouquet(false);

                    $this->mr->persist($loto);
                    $this->mr->flush();
                }
                $lotoid = $this->mr->getRepository(loto::class)->findBy(['order' => $orderid]);

                $sum = 0;
                foreach ($lotoid as $lotoid) {
                    // $sum += $lotoid->getprice();
                }
                $id = $orderid->getId();

                $Hash = base64_encode(hash($this->hash_algo, $session . 1 . $id . $sum . $lotoid->getcurrency() . $this->certificate, true));

                $form_data = [
                    'userAccountID' => $session,
                    "merchantAccountID" => 1,
                    'orderID' => $id,
                    'amount' => $sum,
                    'currency' => $lotoid->getcurrency(),
                    'secureHash' =>  $Hash,
                ];

                $params['data'] = json_encode($form_data);
                $params['url'] = 'SuyoolGlobalAPIs/api/Utilities/PushUtilityPayment';
                /*** Call the api ***/
                $response = Helper::send_curl($params);
                $parameters['push_utility_response'] = json_decode($response, true);


                if ($parameters['push_utility_response']['globalCode'] == 1) {
                    $orderid->setamount($sum)
                        ->setcurrency($lotoid->getcurrency())
                        ->setstatus("completed")
                        ->settransId($parameters['push_utility_response']['data']);

                    $this->mr->persist($orderid);
                    $this->mr->flush();


                    $Hash = base64_encode(hash($this->hash_algo, $parameters['push_utility_response']['data'] . "testing" . $this->certificate, true));

                    $form_data = [
                        'transactionID' => $parameters['push_utility_response']['data'],
                        "additionalData" => "testing",
                        'secureHash' =>  $Hash,
                    ];

                    $params['data'] = json_encode($form_data);
                    $params['url'] = 'SuyoolGlobalAPIs/api/Utilities/UpdateUtilityPayment';
                    /*** Call the api ***/
                    $response = Helper::send_curl($params);
                    $parameters['update_utility_response'] = json_decode($response, true);
                    print_r($parameters['update_utility_response']);
                    $message = "You have played your grid , Best of luck :)";
                } else {
                    $lotoid = $this->mr->getRepository(loto::class)->findBy(['order' => $orderid]);
                    // dd($lotoid);               
                    foreach ($lotoid as $index => $lotoid) {
                        if ($index === 0 || $index === 1) {
                            $arr = 1;
                        } else {
                            $arr = 0;
                        }

                        if ($arr == 1) {
                            $lotoid->setcompleted(true);
                        } else {
                            $lotoid->setcompleted(false);
                        }

                        $this->mr->persist($lotoid);
                        $this->mr->flush();
                    }
                    $lotoidcompleted = $this->mr->getRepository(loto::class)->findBy(['order' => $orderid, 'iscompleted' => true]);
                    foreach ($lotoidcompleted as $lotoidcompletedsum) {
                        $sum += $lotoidcompletedsum->getprice();
                    }
                    $transId = rand();
                    $orderid->setamount($sum)
                        ->setcurrency($lotoidcompletedsum->getcurrency())
                        ->settransId($transId)
                        ->setstatus("completed");

                    $this->mr->persist($orderid);
                    $this->mr->flush();


                    // $orderCompleted = $this->mr->getRepository(loto::class)->getlotonotify($transId,$orderid);

                    $orderCompleted = $this->mr->getRepository(order::class)->findOneBy(['suyoolUserId' => $session, 'status' => 'completed', 'transId' => $transId]);
                    // dd($orderCompleted);
                    foreach ($lotoidcompleted as $lotoidcompletedtonot) {
                        // dd($lotoidcompletedtonot);
                        $notification = new notification;
                        $notification->setOrderId($orderCompleted);
                        $notification->settransId($orderCompleted->gettransId());
                        $notification->setText("Hello {fname}, thanks for playing loto using suyool");
                        $notification->setGrids($lotoidcompletedtonot->getgridSelected());
                        $notification->setamount($lotoidcompletedtonot->getprice());
                        $notification->setcurrency($lotoidcompletedtonot->getcurrency());
                        $notification->setdraw($lotoidcompletedtonot->getdrawnumber());
                        $notification->setzeed($lotoidcompletedtonot->getwithZeed());
                        $notification->setbouquet($lotoidcompletedtonot->getbouquet());

                        $this->mr->persist($notification);
                        $this->mr->flush();
                    }
                    // ->settransId($parameters['push_utility_response']['data']);

                    // $message = $parameters['push_utility_response']['message'];
                    // $orderid
                    //     ->setstatus("canceled");
                    //     $this->mr->persist($orderid);
                    //     $this->mr->flush();
                    $message = "You have played your grid , Best of luck :)";
                }
            } else {
                $message = "You dont have grid available";
            }
        } else {
            $message = "Don't have userId in session please contact the administrator or login";
        }
        return new JsonResponse([
            'status' => true,
            'message' => $message
        ], 200);
    }

    /**
     * @Route("/loto/getData", name="app_getData",methods="GET")
     * 
     */
    public function getData(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if ($data != null) {
            $transId = $data['transId'];
            $order = $data['orderId'];
            $lotodata = $this->mr->getRepository(loto::class)->getData($transId, $order);
            if ($lotodata != null) {
                $response = [];
                foreach ($lotodata as $lotodata) {
                    $response[] = [
                        'orderId' => $lotodata->getOrderId()->getId(),
                        'transId' => $transId,
                        'ticketId' => $lotodata->getticketId(),
                        'gridSelected' => $lotodata->getgridSelected(),
                        'price' => $lotodata->getprice(),
                        'currency' => $lotodata->getcurrency(),
                        'bouquet' => $lotodata->getbouquet(),
                        'zeed' => $lotodata->getwithZeed()

                    ];
                }
                return new JsonResponse([
                    'status' => true,
                    'data' => $response
                ]);
            } else {
                return new JsonResponse([
                    'status' => true,
                    'data' => 'No data for the user Found'
                ]);
            }
        } else {
            return new JsonResponse([
                'message' => 'No data Founds'
            ]);
        }
    }

    /**
     * @Route("/loto/notification", name="app_notification",methods="GET")
     * 
     */
    public function notification(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $withZeed = $data['withZeed'];

        if ($withZeed) {
            $notification = $this->mr->getRepository(notification::class)->findBy(['withZeed' => $withZeed]);
            foreach ($notification as $withZEED) {
                $response[] = [
                    'id'=>$withZEED->getId(),
                    'order_id'=>$withZEED->getOrderId()->getId()
                ];
            }
        } else {
            $notification = $this->mr->getRepository(notification::class)->findBy(['withZeed' => $withZeed]);
            foreach ($notification as $notZEED) {
                $response[] = [
                    'id'=>$notZEED->getId(),
                    'order_id'=>$notZEED->getorderId()
                ];
            }
        }

        return new JsonResponse([
            'status' => true,
            'data' => $response
        ], 200);
    }
}
