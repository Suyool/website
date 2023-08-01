<?php

namespace App\Controller;

use App\Entity\Loto\loto;
use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_numbers;
use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\notification;
use App\Entity\Loto\order;
use App\Service\LotoServices;
use App\Service\SuyoolServices;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LotoController extends AbstractController
{
    private $mr;
    private $session;
    private $certificate;
    private $hash_algo;
    private $LotoServices;
    private $suyoolServices;

    public function __construct(ManagerRegistry $mr, SessionInterface $session, $certificate, $hash_algo, LotoServices $LotoServices, SuyoolServices $suyoolServices)
    {
        $this->mr = $mr->getManager('loto');
        $this->session = $session;
        $this->certificate = $certificate;
        $this->hash_algo = $hash_algo;
        $this->LotoServices = $LotoServices;
        $this->suyoolServices = $suyoolServices;
    }

    /**
     * @Route("/loto", name="app_loto")
     */
    public function index(Request $request, ManagerRegistry $em, HttpClientInterface $client)
    {
        // dd($this->LotoServices->playLoto(1,2,1));
        // $string_to_encrypt = "89Android";
        // $password = "password";
        // $encrypted_string = openssl_encrypt($string_to_encrypt, "AES-128-ECB", $password);
        // $decrypted_string = openssl_decrypt($encrypted_string, "AES-128-ECB", $password);
        // dd($decrypted_string);
        // $useragent = $_SERVER['HTTP_USER_AGENT'];
        $session = 155;
        $this->session->set('userId', $session);

        $loto_draw = $this->mr->getRepository(LOTO_draw::class)->findOneBy([], ['drawdate' => 'DESC']);

        $loto_numbers = $this->mr->getRepository(LOTO_numbers::class)->findPriceByNumbers(11);

        $loto_prize_result = $this->mr->getRepository(LOTO_results::class)->findBy([], ['drawdate' => 'desc']);

        $data = json_decode($request->getContent(), true);
        if (isset($data)) {
            $drawId = $data['drawNumber'];
            $loto_prize = $this->mr->getRepository(LOTO_results::class)->findOneBy(['drawId' => $drawId]);
            $loto_prize_per_days = $this->mr->getRepository(loto::class)->getResultsPerUser($session, $drawId);
            // dd($loto_prize_per_days);

        } else {
            $loto_prize = $this->mr->getRepository(LOTO_results::class)->findOneBy([], ['drawdate' => 'desc']);
            $loto_prize_per_days = $this->mr->getRepository(loto::class)->getResultsPerUser($session, $loto_prize->getDrawId());
            // dd($loto_prize_per_days);

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

        $parameters['prize_loto_win'] = $loto_prize_array;
        $prize_loto_perdays = [];
        foreach ($loto_prize_per_days as $days) {
            // dd($days['gridSelected']);
            foreach ($days['gridSelected'] as $gridselected) {
                $grids[] = $gridselected;
            }
            // dd($grids);
            // $gridselected=explode("|",$days['gridSelected']);
            $date = new DateTime($days['date']);


            $prize_loto_perdays[] = [
                'month' => $date->format('M'),
                'day' => $date->format('d'),
                'date' => $date->format('l'),
                'year' => $date->format('Y'),
                'drawNumber' => $days['drawId'],
                'gridSelected' => $grids,
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

        // dd($parameters);

        if (isset($data)) {
            return new JsonResponse([
                'parameters' => $parameters
            ]);
        } else {
            if (isset($session)) {
                return $this->render('loto/index.html.twig', [
                    'parameters' => $parameters
                ]);
            } else {
                return new JsonResponse([
                    'message' => 'Not found'
                ], 404);
            }
        }
    }

    /**
     * @Route("/loto/play", name="app_loto_play",methods="POST")
     */
    public function play(Request $request)
    {
        $session = $this->session->get('userId');
        // dd($session);
        $loto_draw = $this->mr->getRepository(LOTO_draw::class)->findOneBy([], ['drawdate' => 'DESC']);

        $today = new DateTime();


        if (isset($session)) {
            $data = json_decode($request->getContent(), true);

            $getPlayedBalls = $data['selectedBalls'];
            $getPlayedBalls = json_decode($getPlayedBalls, true);

            // dd($getPlayedBalls);
            if ($getPlayedBalls != null && !empty($getPlayedBalls)) {


                $numDraws = 1;

                $drawnumber = $loto_draw->getdrawId();
                $ballsArray = [];

                $ballsArrayNoZeed = [];
                $ballsArrayNoZeedBouquet = null;
                $amounttotal = 0;
                $amounttotalBouquet = 0;
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
                        }

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
                                ->setbouquet($bouquet);

                            $this->mr->persist($loto);
                            $this->mr->flush();
                        }
                    }
                }
                if ($ballsArrayNoZeed != null) {

                    // dd("ok");
                    $selected = implode('|', $ballsArrayNoZeed);

                    // dd();
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
                if ($ballsArrayNoZeedBouquet != null) {
                    // $BouquetGrids=$this->LotoServices->BouquetGrids();
                    // dd($selectedBallsBouquet);
                    $nozeed = 1;
                    $orderid = $this->mr->getRepository(order::class)->findOneBy(['suyoolUserId' => $session, 'status' => 'pending']);
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
                $i = sizeof($lotoid);
                $sum = 0;
                // $WarningPopUp=json_encode($warning,true);
                if ($today >= $loto_draw->getdrawdate()->modify('-15 minutes')) {
                    $nextThursday = $today->modify('+2 hour 30 minutes');

                    $nextDate = $nextThursday->format('Y/m/d');
                    $warning = ['Title' => 'Too Late for Today’s Draw!', 'SubTitle' => 'Play these number for the next draw on ' . $nextDate . ' at 20:00', 'Text' => 'Play', 'flag' => '?goto=Play'];

                    $orderid->setstatus("canceled");

                    $this->mr->persist($orderid);
                    $this->mr->flush();
                    return new JsonResponse([
                        'status' => false,
                        'message' => $warning,
                        'flagCode' => 150
                    ], 200);
                }

                foreach ($lotoid as $lotoid) {
                    $sum += $lotoid->getprice();
                }

                // dd($sum);
                $id = $orderid->getId();

                $pushutility = $this->suyoolServices->PushUtilities($session, $id, $sum, $lotoid->getcurrency(), $this->hash_algo, $this->certificate);
                // dd($pushutility);


                if ($pushutility[0]) {
                    $orderid->setamount($sum)
                        ->setcurrency($lotoid->getcurrency())
                        ->settransId($pushutility[1])
                        ->setstatus("held");

                    $this->mr->persist($orderid);
                    $this->mr->flush();

                    $lotoToBePlayed = $this->mr->getRepository(loto::class)->findBy(['order' => $orderid]);
                    // foreach ($lotoToBePlayed as $lotoToBePlayed) {
                    //     // dd();
                    //     $submit = $this->LotoServices->playLoto($lotoToBePlayed->getdrawnumber(), $lotoToBePlayed->getwithZeed(), $lotoToBePlayed->getgridSelected());
                    //     // $submit[0] = true;
                    //     if ($lotoToBePlayed->getbouquet()) {
                    //         if ($submit[0]) {
                    //             sleep(1);
                    //             $ticketId = $this->LotoServices->GetTicketId();
                    //             sleep(1);
                    //             $BouquetGrids = $this->LotoServices->BouquetGrids($ticketId);
                    //             $lotoToBePlayed->setticketId($ticketId);
                    //             $lotoToBePlayed->setzeednumber($submit[1]);
                    //             $lotoToBePlayed->setgridSelected($BouquetGrids);

                    //             $this->mr->persist($lotoToBePlayed);
                    //             $this->mr->flush();
                    //             $ticketIds['ticketId'] = $ticketId;
                    //         }
                    //     } else {
                    //         if ($submit[0]) {
                    //             sleep(1);
                    //             $ticketId = $this->LotoServices->GetTicketId();
                    //             $lotoToBePlayed->setticketId($ticketId);
                    //             $lotoToBePlayed->setzeednumber($submit[1]);

                    //             $this->mr->persist($lotoToBePlayed);
                    //             $this->mr->flush();
                    //             // $ticketId = 522;
                    //             $ticketIds['ticketId'] = intval($ticketId);
                    //         }
                    //     }
                    // }
                    // $orderid
                    //     ->setstatus("purchased");
                    // $this->mr->persist($orderid);
                    // $this->mr->flush();
                    // dd($res);
                    // dd($lotoToBePlayed[0]->getwithZeed());


                    // $notification = new notification;
                    // $notification->setIdentifier('Payment taken');
                    // $notification->setTitle("LOTO Purchased Successfully");
                    // $notification->setNotify("You have successfully paid " . $lotoid->getcurrency() . $sum . " to purchase " . $i . " Grids");
                    // $notification->setSubject("LOTO Purchased Successfully");
                    // $notification->setOrderId($orderid);
                    // $notification->settransId($orderid->gettransId());
                    // $notification->setText("You have successfully paid " . $lotoid->getcurrency() . $sum . "to purchase " . $i . " Grids");
                    // // $notification->setGrids($i);
                    // $notification->setamount($sum);
                    // $notification->setcurrency($orderid->getcurrency());
                    // $notification->setDrawId($drawId);
                    // $notification->setResultDate($drawId->getdrawdate()->format('Y-m-d H:i:s'));
                    // $notification->setzeed($lotoidcompletedtonot->getwithZeed());
                    // $notification->setbouquet($lotoidcompletedtonot->getbouquet());


                    // $this->mr->persist($notification);

                    // $lotoid = $this->mr->getRepository(loto::class)->findBy(['order' => $orderid]);
                    // dd($lotoid);               
                    // foreach ($lotoid as $index => $lotoid) {
                    //     if ($index === 0 || $index === 1) {
                    //         $arr = 1;
                    //     } else {
                    //         $arr = 0;
                    //     }

                    //     if ($arr == 1) {
                    //         $lotoid->setcompleted(true);
                    //     } else {
                    //         $lotoid->setcompleted(false);
                    //     }

                    //     $this->mr->persist($lotoid);
                    //     $this->mr->flush();
                    // }
                    // $lotoidcompleted = $this->mr->getRepository(loto::class)->completed($orderid);

                    // $i = sizeof($lotoidcompleted);
                    // $newsum = 0;
                    // foreach ($lotoidcompleted as $lotoidcompletedsum) {
                    //     $newsum += $lotoidcompletedsum->getprice();
                    // }

                    //     $transId = $pushutility[1];


                    // // echo $ticketIds;
                    // $additionalData = json_encode($ticketIds, true);
                    // echo $additionalData;
                    // $updateutility = $this->suyoolServices->UpdateUtilities($newsum, $this->hash_algo, $this->certificate, $additionalData,$ticketId);

                    // if ($updateutility) {
                    //     $orderid->setamount($newsum)
                    //         ->setcurrency("LBP")
                    //         ->settransId($transId)
                    //         ->setstatus("completed");

                    //     $this->mr->persist($orderid);
                    //     $this->mr->flush();
                    //     $status = true;
                    //     $message = "You have played your grid , Best of luck :)";

                    //     return new JsonResponse([
                    //         'status' => $status,
                    //         'message' => $message,
                    //         'amount' => $newsum
                    //     ], 200);
                    // } else {
                    //     $status = false;
                    //     $message = $updateutility[1];
                    //     return new JsonResponse([
                    //         'status' => $status,
                    //         'message' => $message,
                    //         'amount' => $newsum
                    //     ], 200);
                    // }


                    // if ($newsum != $sum) {

                    //     $diff = $sum - $newsum;

                    //     $notification = new notification;
                    //     $notification->setIdentifier('Payment retrieved');
                    //     $notification->setTitle("Reversed LOTO Payment ");
                    //     $notification->setNotify(" LOTO has reversed your Suyool payment of " . $lotoidcompletedsum->getcurrency() . $diff . " related the Draw " . $lotoidcompletedsum->getdrawnumber() . "");
                    //     $notification->setSubject("Reversed LOTO Payment ");
                    //     $notification->setOrderId($orderid);
                    //     $notification->settransId($orderid->gettransId());
                    //     $notification->setText("{fname}, LOTO has reversed your Suyool payment of " . $lotoidcompletedsum->getcurrency() . $diff . " related the Draw " . $lotoidcompletedsum->getdrawnumber() . "");
                    //     // $notification->setGrids($i);
                    //     $notification->setamount($newsum);
                    //     $notification->setcurrency($orderid->getcurrency());
                    //     // $notification->setDrawId($drawId);
                    //     // $notification->setResultDate($drawId->getdrawdate()->format('Y-m-d H:i:s'));
                    //     // $notification->setzeed($lotoidcompletedtonot->getwithZeed());
                    //     // $notification->setbouquet($lotoidcompletedtonot->getbouquet());


                    //     $this->mr->persist($notification);
                    // }


                    // $orderCompleted = $this->mr->getRepository(loto::class)->getlotonotify($transId,$orderid);

                    // $orderCompleted = $this->mr->getRepository(order::class)->findOneBy(['suyoolUserId' => $session, 'status' => 'completed', 'transId' => $transId]);
                    // dd($orderCompleted);
                    // foreach ($lotoidcompleted as $lotoidcompletedtonot) {
                    //     $drawId = $this->mr->getRepository(LOTO_draw::class)->findOneBy(['drawId' => $lotoidcompletedtonot->getdrawnumber()]);
                    //     if ($lotoidcompletedtonot->getwithZeed()) {
                    //         if (!$lotoidcompletedtonot->getbouquet()) {
                    //             $notification = new notification;
                    //             $notification->setIdentifier('Play With Zeed');
                    //             $notification->setTitle("LOTO Ticket Confirmed");
                    //             $notification->setNotify("You have successfully purchased a LOTO ticket with Zeed");
                    //             $notification->setSubject("LOTO Ticket Confirmed");
                    //             $notification->setOrderId($orderCompleted);
                    //             $notification->settransId($orderCompleted->gettransId());
                    //             $notification->setText("Draw " . $lotoidcompletedtonot->getdrawnumber() . "<br>" . $lotoidcompletedtonot->getgridSelected() . "");
                    //             $notification->setGrids($lotoidcompletedtonot->getgridSelected());
                    //             $notification->setamount($lotoidcompletedtonot->getprice());
                    //             $notification->setcurrency($lotoidcompletedtonot->getcurrency());
                    //             $notification->setDrawId($drawId);
                    //             $notification->setResultDate($drawId->getdrawdate()->format('Y-m-d H:i:s'));
                    //             $notification->setzeed($lotoidcompletedtonot->getwithZeed());
                    //             $notification->setbouquet($lotoidcompletedtonot->getbouquet());


                    //             $this->mr->persist($notification);
                    //         } else {
                    //             $bouquetgrids = explode("B", $lotoidcompletedtonot->getgridSelected());
                    //             $notification = new notification;
                    //             $notification->setIdentifier('Play Bouquet With Zeed');
                    //             $notification->setTitle("LOTO Bouquet Confirmed ");
                    //             $notification->setNotify("You have successfully purchased the Bouquet of {$bouquetgrids[0]}Grids with Zeed. ");
                    //             $notification->setSubject("LOTO Bouquet Confirmed ");
                    //             $notification->setOrderId($orderCompleted);
                    //             $notification->settransId($orderCompleted->gettransId());
                    //             $notification->setText("Draw " . $lotoidcompletedtonot->getdrawnumber() . "<br>" . $lotoidcompletedtonot->getgridSelected() . "");
                    //             $notification->setGrids($lotoidcompletedtonot->getgridSelected());
                    //             $notification->setamount($lotoidcompletedtonot->getprice());
                    //             $notification->setcurrency($lotoidcompletedtonot->getcurrency());
                    //             $notification->setDrawId($drawId);
                    //             $notification->setResultDate($drawId->getdrawdate()->format('Y-m-d H:i:s'));
                    //             $notification->setzeed($lotoidcompletedtonot->getwithZeed());
                    //             $notification->setbouquet($lotoidcompletedtonot->getbouquet());


                    //             $this->mr->persist($notification);
                    //         }
                    //     } else {
                    //         if (!$lotoidcompletedtonot->getbouquet()) {
                    //             $notification = new notification;
                    //             $notification->setIdentifier('Play Without Zeed');
                    //             $notification->setTitle("LOTO Ticket Confirmed");
                    //             $notification->setNotify("You have successfully purchased a LOTO ticket");
                    //             $notification->setSubject("LOTO Ticket Confirmed");
                    //             $notification->setOrderId($orderCompleted);
                    //             $notification->settransId($orderCompleted->gettransId());
                    //             $notification->setText("Draw " . $lotoidcompletedtonot->getdrawnumber() . "<br>" . $lotoidcompletedtonot->getgridSelected() . "");
                    //             $notification->setGrids($lotoidcompletedtonot->getgridSelected());
                    //             $notification->setamount($lotoidcompletedtonot->getprice());
                    //             $notification->setcurrency($lotoidcompletedtonot->getcurrency());
                    //             $notification->setDrawId($drawId);
                    //             $notification->setResultDate($drawId->getdrawdate()->format('Y-m-d H:i:s'));
                    //             $notification->setzeed($lotoidcompletedtonot->getwithZeed());
                    //             $notification->setbouquet($lotoidcompletedtonot->getbouquet());

                    //             $this->mr->persist($notification);
                    //         } else {
                    //             $bouquetgrids = explode("B", $lotoidcompletedtonot->getgridSelected());

                    //             $notification = new notification;
                    //             $notification->setIdentifier('Play Bouquet Without Zeed');
                    //             $notification->setTitle("LOTO Bouquet Confirmed ");
                    //             $notification->setNotify(" You have successfully purchased the Bouquet of {$bouquetgrids[0]}Grids.");
                    //             $notification->setSubject("LOTO Bouquet Confirmed ");
                    //             $notification->setOrderId($orderCompleted);
                    //             $notification->settransId($orderCompleted->gettransId());
                    //             $notification->setText("Draw " . $lotoidcompletedtonot->getdrawnumber() . "<br>" . $lotoidcompletedtonot->getgridSelected() . "");
                    //             $notification->setGrids($lotoidcompletedtonot->getgridSelected());
                    //             $notification->setamount($lotoidcompletedtonot->getprice());
                    //             $notification->setcurrency($lotoidcompletedtonot->getcurrency());
                    //             $notification->setDrawId($drawId);
                    //             $notification->setResultDate($drawId->getdrawdate()->format('Y-m-d H:i:s'));
                    //             $notification->setzeed($lotoidcompletedtonot->getwithZeed());
                    //             $notification->setbouquet($lotoidcompletedtonot->getbouquet());

                    //             $this->mr->persist($notification);
                    //         }
                    //         // dd($drawId->getdrawdate());

                    //     }
                    //     // dd($lotoidcompletedtonot);

                    //     $this->mr->flush();
                    // }
                    // ->settransId($parameters['push_utility_response']['data']);

                    // $message = $parameters['push_utility_response']['message'];
                    // $orderid
                    //     ->setstatus("canceled");
                    //     $this->mr->persist($orderid);
                    //     $this->mr->flush();


                    // $Hash = base64_encode(hash($this->hash_algo, $parameters['push_utility_response']['data'] . "testing" . $this->certificate, true));

                    // $form_data = [
                    //     'transactionID' => $parameters['push_utility_response']['data'],
                    //     "additionalData" => "testing",
                    //     'secureHash' =>  $Hash,
                    // ];

                    // $params['data'] = json_encode($form_data);
                    // $params['url'] = 'SuyoolGlobalAPIs/api/Utilities/UpdateUtilityPayment';
                    // /*** Call the api ***/
                    // $response = Helper::send_curl($params);
                    // $parameters['update_utility_response'] = json_decode($response, true);
                    // print_r($parameters['update_utility_response']);
                    $status = true;
                    $message = "You have played your grid , Best of luck :)";

                    return new JsonResponse([
                        'status' => $status,
                        'message' => $message,
                        'amount' => $sum
                    ], 200);
                } else {
                    // $transId = rand();

                    $orderid->setstatus("canceled");

                    $this->mr->persist($orderid);
                    $this->mr->flush();

                    $status = false;
                    $message = $pushutility[1];
                    $flagCode = $pushutility[2];
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
                    'id' => $withZEED->getId(),
                    'order_id' => $withZEED->getOrderId()->getId()
                ];
            }
        } else {
            $notification = $this->mr->getRepository(notification::class)->findBy(['withZeed' => $withZeed]);
            foreach ($notification as $notZEED) {
                $response[] = [
                    'id' => $notZEED->getId(),
                    'order_id' => $notZEED->getorderId()
                ];
            }
        }

        return new JsonResponse([
            'status' => true,
            'data' => $response
        ], 200);
    }
}
