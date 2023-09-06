<?php


namespace App\Controller;

use App\Entity\Loto\loto;
use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_numbers;
use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\notification;
use App\Entity\Loto\order;
use App\Entity\Notification\content;
use App\Entity\Notification\Template;
use App\Service\LotoServices;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LotoController extends AbstractController
{
    private $mr;
    private $session;
    private $LotoServices;
    private $suyoolServices;
    private $notificationServices;
    private $notMr;
    public $cipher_algorithme = "AES128";
    public $key = "SY1X24elh9eG3fpOaHcWlQ9h2bHaqimdIDoyoOaFoi0rukAj3Z";
    public $iv = "fgu26y9e43wc8dj2"; //initiallization vector for decrypt
    private $CURRENCY_LBP;
    private $CURRENCY_USD;
    private $params;

    public function __construct(ManagerRegistry $mr, SessionInterface $session, LotoServices $LotoServices, NotificationServices $notificationServices, ParameterBagInterface $params)
    {
        $this->mr = $mr->getManager('loto');
        $this->session = $session;
        $this->LotoServices = $LotoServices;
        $this->suyoolServices = new SuyoolServices($params->get('LOTO_MERCHANT_ID'));
        $this->notificationServices = $notificationServices;
        $this->notMr = $mr->getManager('notification');
        $this->CURRENCY_LBP = $params->get('CURRENCY_LBP');
        $this->CURRENCY_USD = $params->get('CURRENCY_USD');
        $this->params = $params;
    }

    /**
     * @Route("/loto", name="app_loto")
     */
    public function index(Request $request)
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $data = json_decode($request->getContent(), true);

        if (isset($data)) {
            $suyoolUserId = $this->session->get('suyoolUserId');

            $loto_prize_result = $this->mr->getRepository(LOTO_results::class)->findBy([], ['drawdate' => 'desc']);

            $data = json_decode($request->getContent(), true);

            $drawId = $data['drawNumber'];
            $loto_prize = $this->mr->getRepository(LOTO_results::class)->findOneBy(['drawId' => $drawId]);
            if ($loto_prize != null) {
                $loto_prize_per_days = $this->mr->getRepository(loto::class)->getResultsPerUser($suyoolUserId, $loto_prize->getDrawId());
            } else {
                $loto_prize_per_days = $this->mr->getRepository(loto::class)->getfetchhistory($suyoolUserId, $drawId);
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



            return new JsonResponse([
                'parameters' => $parameters
            ]);
        }

        $_POST['infoString'] = "3mzsXlDm5DFUnNVXA5Pu8T1d5nNACEsiiUEAo7TteE/x3BGT3Oy3yCcjUHjAVYk3";

        if (isset($_POST['infoString'])) {
            $string_to_decrypt = $_POST['infoString'];
            if ($_POST['infoString'] == "") {
                return $this->render('ExceptionHandling.html.twig');
            }
            $decrypted_string = openssl_decrypt($string_to_decrypt, $this->cipher_algorithme, $this->key, 0, $this->iv);
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($useragent, $suyoolUserInfo[1]);
            if ($this->notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && $devicetype) {

                $parameters['deviceType'] = $suyoolUserInfo[1];

                $date = date('w');
                if ($date > 1 && $date <= 4) {

                    $PlayOnce = date("l", strtotime("next thursday"));
                } else {
                    $PlayOnce = date("l", strtotime("next monday"));
                }
                $current_time = strtotime('now');

                if ($date == 1 && $current_time > strtotime('today 19:30:00')) {
                    $PlayOnce = date("l", strtotime("next thursday"));
                } else if ($date == 4 && $current_time > strtotime('today 19:30:00')) {
                    $PlayOnce = date("l", strtotime("next monday"));
                }


                $OneWeek = date("d-m-Y", strtotime("+1 week"));
                $OneMonth = date("d-m-Y", strtotime("+1 month"));
                $SixMonth = date("d-m-Y", strtotime("+6 month"));
                $OneYear = date("d-m-Y", strtotime("+1 year"));

                $parameters['HowOftenDoYouWantToPlay'] = [
                    $PlayOnce, $OneWeek, $OneMonth, $SixMonth, $OneYear
                ];

                $suyoolUserId = $suyoolUserInfo[0];
                $this->session->set('suyoolUserId', $suyoolUserId);
                $loto_draw = $this->mr->getRepository(LOTO_draw::class)->findOneBy([], ['drawdate' => 'DESC']);

                $loto_numbers = $this->mr->getRepository(LOTO_numbers::class)->findPriceByNumbers(11);

                $loto_prize_result = $this->mr->getRepository(LOTO_results::class)->findBy([], ['drawdate' => 'desc']);

                $data = json_decode($request->getContent(), true);

                $loto_prize = $this->mr->getRepository(LOTO_results::class)->findOneBy([], ['drawdate' => 'desc']);
                $lotohistory = $this->mr->getRepository(LOTO_draw::class)->findOneBy([], ['drawdate' => 'desc']);

                $checkdraw = $this->mr->getRepository(LOTO_results::class)->findOneBy(['drawId' => $lotohistory->getDrawId()]);
                if ($checkdraw != null) {
                    $loto_prize_per_days = $this->mr->getRepository(loto::class)->getResultsPerUser($suyoolUserId, $loto_prize->getDrawId());
                } else {
                    $loto_prize_per_days = $this->mr->getRepository(loto::class)->getfetchhistory($suyoolUserId, $lotohistory->getDrawId());
                }
                if ($loto_prize_per_days == null) {
                    $fetchLastDraw = 1;
                    $loto_prize_per_days = $this->mr->getRepository(loto::class)->getResultsPerUser($suyoolUserId, $loto_prize->getDrawId());
                }



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
                $parameters['gridprice'] =
                    $parameters['unit_price'];
                if (isset($fetchLastDraw)) {
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
                } else if ($checkdraw != null) {
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
                $parameters['prize_loto_result'] =  array_map("unserialize", array_unique(array_map("serialize", $prize_loto_result)));
                $parameters['prize_loto_result'] = array_values($parameters['prize_loto_result']);

                return $this->render('loto/index.html.twig', [
                    'parameters' => $parameters
                ]);
            } else return $this->render('ExceptionHandling.html.twig');
        } else return $this->render('ExceptionHandling.html.twig');
    }

    /**
     * @Route("/loto/play", name="app_loto_play",methods="POST")
     */
    public function play(Request $request)
    {
        $bulk = 0; //0 if unicast
        $suyoolUserId = $this->session->get('suyoolUserId');
        $numGrids = 0;
        $loto_draw = $this->mr->getRepository(LOTO_draw::class)->findOneBy([], ['drawdate' => 'DESC']);

        $today = new DateTime();
        $grids = [];


        if (isset($suyoolUserId)) {
            $data = json_decode($request->getContent(), true);

            $getPlayedBalls = $data['selectedBalls'];
            $getPlayedBalls = json_decode($getPlayedBalls, true);

            if ($getPlayedBalls != null && !empty($getPlayedBalls)) {

                $drawnumber = $loto_draw->getdrawId();
                $ballsArray = [];

                $ballsArrayNoZeed = [];
                $ballsArrayNoZeedBouquet = null;
                $amounttotal = 0;
                $amounttotalBouquet = 0;
                $selected = [];



                $order = new order;
                $order->setsuyoolUserId($suyoolUserId)
                    ->setstatus(order::$statusOrder['PENDING']);

                $this->mr->persist($order);
                $this->mr->flush();
                foreach ($getPlayedBalls as $item) {
                    $numDraws = $item['subscription'];
                    $currency = $item['currency'];
                    $withZeed = $item['withZeed'];
                    if ($withZeed == false) {
                        $withZeed = 0;
                    } else {
                        $withZeed = 1;
                    }
                    if ($withZeed == false) {
                        if (isset($item['balls']) && $item['balls'] != null) {
                            $ballsnumbers = count($item['balls']);
                            $price = $this->mr->getRepository(LOTO_numbers::class)->findOneBy(['numbers' => $ballsnumbers]);
                            $item['price'] = $price->getprice();
                            $balls = implode(" ", $item['balls']);
                            $ballsArrayNoZeed[] = $balls;
                            $amounttotal += $item['price'];
                        } else {
                            $bouquetNum = explode('B', $item['bouquet']);
                            $price = $this->mr->getRepository(LOTO_numbers::class)->findOneBy(['numbers' => 6]);
                            $item['price'] = $price->getprice() * $bouquetNum[1];
                            $amounttotalBouquet += $item['price'];
                            $ballsArrayNoZeedBouquet = $item['bouquet'];
                            $numGrids += $bouquetNum[1];
                        }

                        $withZeed = 0;
                    } else {
                        if (isset($item['balls']) && $item['balls'] != null) {
                            $ballsnumbers = count($item['balls']);
                            $price = $this->mr->getRepository(LOTO_numbers::class)->findOneBy(['numbers' => $ballsnumbers]);
                            $item['price'] = $price->getprice() + $price->getzeed();
                            $balls = implode(" ", $item['balls']);
                            $ballsArray = $balls;
                            $bouquet = false;
                        } else {
                            $bouquetNum = explode('B', $item['bouquet']);
                            $price = $this->mr->getRepository(LOTO_numbers::class)->findOneBy(['numbers' => 6]);
                            $item['price'] = $price->getprice() * $bouquetNum[1] + $price->getzeed();
                            $ballsArray = $item['bouquet'];
                            $bouquet = true;
                            $numGrids += $bouquetNum[1];
                        }

                        $orderid = $this->mr->getRepository(order::class)->findBy(['suyoolUserId' => $suyoolUserId, 'status' => 'pending']);

                        foreach ($orderid as $orderid) {
                            $loto = new loto;
                            $loto->setOrderId($orderid)
                                ->setdrawnumber($drawnumber)
                                ->setnumdraws($numDraws)
                                ->setWithZeed(true)
                                ->setgridSelected($ballsArray)
                                ->setprice($item['price'])
                                ->setcurrency($currency)
                                ->setbouquet($bouquet);

                            $this->mr->persist($loto);
                            $this->mr->flush();
                        }
                    }
                }
                if ($ballsArrayNoZeed != null) {

                    $selected = implode('|', $ballsArrayNoZeed);
                    $orderid = $this->mr->getRepository(order::class)->findOneBy(['suyoolUserId' => $suyoolUserId, 'status' => 'pending']);
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
                if ($ballsArrayNoZeedBouquet != null) {
                    $orderid = $this->mr->getRepository(order::class)->findOneBy(['suyoolUserId' => $suyoolUserId, 'status' => 'pending']);
                    $loto = new loto;
                    $loto->setOrderId($orderid)
                        ->setdrawnumber($drawnumber)
                        ->setnumdraws($numDraws)
                        ->setWithZeed(false)
                        ->setgridSelected($ballsArrayNoZeedBouquet)
                        ->setprice($amounttotalBouquet)
                        ->setcurrency($currency)
                        ->setbouquet(true);

                    $this->mr->persist($loto);
                    $this->mr->flush();
                }
                $lotoid = $this->mr->getRepository(loto::class)->findBy(['order' => $orderid]);
                $sum = 0;
                if ($today >= $loto_draw->getdrawdate()->modify('-15 minutes')) {
                    $nextThursday = $today->modify('+2 hour 30 minutes');

                    $nextDate = $nextThursday->format('d/m/Y');
                    $warning = ['Title' => 'Too Late for Today’s Draw!', 'SubTitle' => 'Play these numbers for the next draw on: ' . $nextDate . ' at 20:00', 'Text' => 'Play', 'flag' => '?goto=Play'];

                    $orderid->setstatus(order::$statusOrder['CANCELED']);

                    $this->mr->persist($orderid);
                    $this->mr->flush();
                    return new JsonResponse([
                        'status' => false,
                        'message' => $warning,
                        'flagCode' => 150
                    ], 200);
                }

                foreach ($lotoid as $lotoid) {
                    if (strpos($lotoid->getgridSelected(), 'B') !== 0) {
                        $grids[] = explode("|", $lotoid->getgridSelected());
                    }
                    $sum += $lotoid->getprice();
                }
                $mergegrids = array_merge(...$grids); // merge the grids into arrays to get the size
                $numGrids += sizeof($mergegrids);
                $id = $orderid->getId();


                $merchantId = $this->params->get('LOTO_MERCHANT_ID'); // 1 for loto merchant
                $order_id = $merchantId . "-" . $id;

                $sum = $sum * $numDraws;

                $pushutility = $this->suyoolServices->PushUtilities($suyoolUserId, $order_id, $sum, $this->CURRENCY_LBP);

                if ($pushutility[0]) {
                    $orderid->setamount($sum)
                        ->setcurrency($lotoid->getcurrency())
                        ->settransId($pushutility[1])
                        ->setstatus(order::$statusOrder['HELD'])
                        ->setsubscription($numDraws);

                    $this->mr->persist($orderid);
                    $this->mr->flush();

                    $content = $this->notificationServices->getContent('Payment taken loto');

                    $params = json_encode(['amount' => $sum, 'currency' => "L.L", 'numgrids' => $numGrids], true);

                    $this->notificationServices->addNotification($suyoolUserId, $content, $params, $bulk);

                    $status = true;
                    $message = "You have played your grid , Best of luck :)";

                    return new JsonResponse([
                        'status' => $status,
                        'message' => $message,
                        'amount' => $sum
                    ], 200);
                } else {

                    $orderid->setstatus(order::$statusOrder['CANCELED']);
                    $orderid->seterror($pushutility[3]);
                    $orderid->setamount($sum);
                    $orderid->setcurrency($this->CURRENCY_LBP);

                    $this->mr->persist($orderid);
                    $this->mr->flush();

                    $status = false;
                    $message = $pushutility[1];
                    $flagCode = "";
                    if (isset($pushutility[2])) {
                        $flagCode = $pushutility[2];
                    }
                    $message = json_decode($message);

                    return new JsonResponse([
                        'status' => $status,
                        'flagCode' => $flagCode,
                        'message' => $message
                    ], 200);
                }
            } else {
                $status = false;
                $message = "You dont have grid available";
            }
        } else {
            $status = false;
            $message = "Don't have userId in session please contact the administrator or login";
        }

        return new JsonResponse([
            'status' => $status,
            'message' => $message
        ], 200);
    }

    /**
     * @Route("/api/winningPrizeUpdated", name="winningPrizeUpdated",methods="POST")
     * 
     */
    public function winningPrizeUpdated(Request $request)
    {
        $lastdrawnumber = 0;
        $data = json_decode($request->getContent(), true);
        try {
            if (isset($data)) {

                if (isset($data['paidWinners'])) {
                    foreach ($data['paidWinners'] as $data) {
                        $orderId = explode(",", $data['OrderID']);
                        $amount = $data['Amount'];
                        $userid = $data['suyoolUserId'];
                        foreach ($orderId as $orderId) {
                            $loto = $this->mr->getRepository(loto::class)->getWinTickets($orderId);

                            foreach ($loto as $loto) {
                                $loto->setwinningStatus('paid');
                                $this->mr->persist($loto);
                                $this->mr->flush();
                                if ($lastdrawnumber != $loto->getdrawnumber()) {
                                    $params = json_encode(['currency' => 'L.L', 'amount' => $amount, 'number' => $loto->getdrawnumber()]);
                                    $content = $this->notificationServices->getContent('L1-NotExceedMonthlylimit');
                                    $this->notificationServices->addNotification($userid, $content, $params, 0, "https://www.suyool.com/loto?goto=Result");
                                }
                                $lastdrawnumber = $loto->getdrawnumber();
                            }
                        }
                        // dd($drawnumber);

                    }
                    return new JsonResponse([
                        'status' => true,
                        'message' => 'Success'
                    ]);
                } else {
                    return new JsonResponse([
                        'status' => false,
                        'message' => 'Missing request'
                    ]);
                }
            } else {
                return new JsonResponse([
                    'status' => false,
                    'message' => 'Missing request'
                ]);
            }
        } catch (Exception $e) {
            return new JsonResponse([
                'status' => false,
                'message' => 'An error occured'
            ]);
        }
    }
}
