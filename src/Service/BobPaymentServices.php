<?php

namespace App\Service;

use App\Entity\topup\attempts;
use App\Entity\topup\bob_transactions;
use App\Entity\topup\orders;
use App\Entity\topup\session;
use App\Utils\Helper;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

class BobPaymentServices
{
    private $BASE_API;
    private $client;
    private $session;
    private $mr;
    private $suyoolServices;
    private $username;
    private $password;
    private $logger;
    private $notificationServices;

    public function __construct(HttpClientInterface $client, LoggerInterface $logger, SessionInterface $session, ManagerRegistry $mr, SuyoolServices $suyoolServices, NotificationServices $notificationServices)
    {
        $this->client = $client;
        if ($_ENV['APP_ENV'] == "dev") {
            $this->BASE_API = "https://test-bobsal.gateway.mastercard.com/api/rest/version/73/merchant/testsuyool/";
            $this->username = "merchant.TESTSUYOOL";
            $this->password = "002bcc643011b3cef6967ff40d140d71";
        } else {
            $this->BASE_API = "https://bobsal.gateway.mastercard.com/api/rest/version/73/merchant/suyool/";
            $this->username = "merchant.SUYOOL";
            $this->password = "652cdf87fd1c82530b7bfdd0c36662f3";
        }
        $this->session = $session;
        $this->mr = $mr->getManager('topup');
        $this->suyoolServices = $suyoolServices;
        $this->logger = $logger;
        $this->notificationServices = $notificationServices;
    }

    public function SessionFromBobPayment($amount, $currency, $transId, $suyooler = null)
    {
        try {
            $order = new orders;
            $order->setstatus(orders::$statusOrder['PENDING']);
            $order->setsuyoolUserId($suyooler);
            $order->settransId($transId);
            $order->setamount($amount);
            $order->setcurrency($currency);
            $order->settype("topup");
            $this->mr->persist($order);
            $this->mr->flush();

            // $currency == "USD" ? $amount = number_format($amount,2) : $amount;
            $url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
            $this->session->remove('order');
            $this->session->set('order', $transId);
            $body = [
                "apiOperation" => "INITIATE_CHECKOUT",
                "interaction" => [
                    "operation" => "PURCHASE",
                    "merchant" => [
                        "name" => "SUYOOL"
                    ],
                    "returnUrl" => "$url/topup",
                    "cancelUrl" => "$url/ToTheAPP",
                    "displayControl" => [
                        "billingAddress" => "HIDE"
                    ]
                ],
                "order" => [
                    "currency" => $currency,
                    "id" => $transId,
                    "amount" => $amount,
                    "description" => "Topup"
                ]
            ];
            // print_r($body);
            $this->logger->error(json_encode($body));
            $response = $this->client->request('POST', $this->BASE_API . "session", [
                'body' => json_encode($body),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth_basic' => [$this->username, $this->password],
            ]);

            $content = $response->toArray(false);
            // dd($content);
            $session = new session;
            $session->setOrders($order);
            $session->setSession($content['session']['id']);
            $session->setResponse(json_encode($content));
            $session->setIndicator($content['successIndicator']);
            $this->mr->persist($session);
            $this->mr->flush();
            return array(true, $content['session']['id'], $order);
        } catch (Exception $e) {
            return array(false);
        }
    }

    public function RetrievePaymentDetails()
    {
        try {
            $session = $this->session->get('order');
            // echo $session;
            if (isset($session)) {
                $response = $this->client->request('GET', $this->BASE_API . "order/" . $session, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'auth_basic' => [$this->username, $this->password],
                ]);

            $content = $response->toArray(false);
            // dd(end($content['transaction']));
            $this->logger->error(json_encode($content));
            if($content['result'] != 'ERROR'){
                $attempts=new attempts();
                $attempts->setResponse(json_encode($content))
                ->setTransactionId($content['id'])
                ->setAmount($content['amount'])
                ->setCurrency($content['currency'])
                ->setStatus($content['status'])
                ->setResult($content['result'])
                ->setAuthStatus($content['authenticationStatus'])
                ->setCard(end($content['transaction'])['sourceOfFunds']['provided']['card']['number'])
                ->setName(end($content['transaction'])['sourceOfFunds']['provided']['card']['nameOnCard']);

                $this->mr->persist($attempts);
                $this->mr->flush();
            }
            if ($content['result'] == "SUCCESS") return array(true, $content);
        }
            return array(false, "ERROR");
        } catch (Exception $e) {
            return array(false, $e->getMessage());
        }
    }

    public function retrievedataForTopUp($auth, $status, $indicator, $res, $transId, $suyooler, $cardnumber)
    {
        // echo $indicator;
        $parameters = array();
        $session = $this->mr->getRepository(session::class)->findOneBy(['indicator' => $indicator]);
        $transaction = new bob_transactions;
        $transaction->setSession($session);
        $transaction->setResponse(json_encode($res));
        $transaction->setStatus($status);
        $this->mr->persist($transaction);
        $this->mr->flush();
        $session->getOrders()->getcurrency() == "USD" ? $currency = "$" : $currency = "LL";
        if ($status == "CAPTURED") {
            $order = $session->getOrders();
            $order->setstatus(orders::$statusOrder['PAID']);
            $topup = $this->suyoolServices->UpdateCardTopUpTransaction($session->getOrders()->gettransId(), 3, strval($session->getOrders()->gettransId()), $session->getOrders()->getamount(), $session->getOrders()->getcurrency(), substr($cardnumber, -4));
            $this->mr->persist($order);
            $this->mr->flush();
            $transaction->setflagCode($topup[2]);
            $transaction->setError($topup[3]);
            $this->mr->persist($transaction);
            if ($topup[0]) {
                $currency == "$" ? $amount = number_format($topup[1], 2) : $amount = number_format($topup[1]);
                $status = true;
                $imgsrc = "build/images/Loto/success.png";
                $title = "Money Added Successfully";
                $description = "You have successfully added {$currency} {$amount} to your Suyool wallet. <br>Check your new balance";
                $button = "Continue";

                $parameters = [
                    'status' => $status,
                    'title' => $title,
                    'imgsrc' => $imgsrc,
                    'description' => $description,
                    'button' => $button,
                    'infoSuccess' => true
                ];
                // $order = $session->getOrders();
                $order->setstatus(orders::$statusOrder['COMPLETED']);
                $this->mr->persist($order);
                $this->mr->flush();
                $params = json_encode(['currency' => $currency, 'amount' => $topup[1]]);
                $content = $this->notificationServices->getContent('CardTopUp');
                $this->notificationServices->addNotification($suyooler, $content, $params, 0, "");
                return array(true, $parameters);
            } else {
                $this->logger->error(json_encode($topup));
                $status = false;
                $imgsrc = "build/images/Loto/error.png";
                $title = "Please Try Again";
                $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                $button = "Try Again";
                $parameters = [
                    'status' => $status,
                    'title' => $title,
                    'imgsrc' => $imgsrc,
                    'description' => $description,
                    'button' => $button,
                    'infoFailed' => true
                ];
                // $order = $session->getOrders();
                // $order->setstatus(orders::$statusOrder['CANCELED']);
                // $this->mr->persist($order);
                // $this->mr->flush();
                return array(true, $parameters);
            }
        } else {
            $topup = $this->suyoolServices->UpdateCardTopUpTransaction($session->getOrders()->gettransId(), 9,  $session->getOrders()->getId() . "-" . $session->getOrders()->gettransId(), (float)$session->getOrders()->getamount(), $session->getOrders()->getcurrency(), substr($cardnumber, -4));
            $this->logger->error(json_encode($topup));
            $transaction->setflagCode($topup[2]);
            $transaction->setError($topup[3]);
            $this->mr->persist($transaction);
            $status = false;
            $imgsrc = "build/images/Loto/error.png";
            $title = "Please Try Again";
            $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
            $button = "Try Again";
            $parameters = [
                'status' => $status,
                'title' => $title,
                'imgsrc' => $imgsrc,
                'description' => $description,
                'button' => $button,
                'infoFailed' => true
            ];
            $order = $session->getOrders();
            $order->setstatus(orders::$statusOrder['CANCELED']);
            $this->mr->persist($order);
            $this->mr->flush();
            return array(true, $parameters);
        }
    }

    public function retrievedataForTopUpTest($auth, $status, $indicator, $res, $transId, $suyooler)
    {
        // echo $indicator;
        $parameters = array();
        $session = $this->mr->getRepository(session::class)->findOneBy(['indicator' => $indicator]);
        $transaction = new bob_transactions;
        $transaction->setSession($session);
        $transaction->setResponse(json_encode($res));
        $transaction->setStatus($status);
        $this->mr->persist($transaction);
        $this->mr->flush();
        $session->getOrders()->getcurrency() == "USD" ? $currency = "$" : $currency = "LL";
        if ($status == "CAPTURED") {
            $amount = number_format($session->getOrders()->getamount());
            $status = true;
            $imgsrc = "build/images/Loto/success.png";
            $title = "Money Added Succesfully";
            $description = "You have succesfully added {$currency} {$amount} to your Suyool wallet. <br>Check your new balance";
            $button = "Continue";
            $parameters = [
                'status' => $status,
                'title' => $title,
                'imgsrc' => $imgsrc,
                'description' => $description,
                'button' => $button,
                'infoSuccess' => true
            ];
            $order = $session->getOrders();
            $order->setstatus(orders::$statusOrder['COMPLETED']);
            $this->mr->persist($order);
            $this->mr->flush();
            return array(true, $parameters);
        } else {
            $status = false;
            $imgsrc = "build/images/Loto/error.png";
            $title = "Please Try Again";
            $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
            $button = "Try Again";
            $parameters = [
                'status' => $status,
                'title' => $title,
                'imgsrc' => $imgsrc,
                'description' => $description,
                'button' => $button,
                'infoFailed' => true
            ];
            $order = $session->getOrders();
            $order->setstatus(orders::$statusOrder['CANCELED']);
            $this->mr->persist($order);
            $this->mr->flush();
            return array(true, $parameters);
        }
    }

    public function paymentGateWayTest($amount, $currency, $transId, $suyooler = null)
    {
        $order = new orders;
        $order->setstatus(orders::$statusOrder['PENDING']);
        $order->setsuyoolUserId($suyooler);
        $order->settransId($transId);
        $order->setamount($amount);
        $order->setcurrency($currency);
        $this->mr->persist($order);
        $this->mr->flush();
        $url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
        $this->session->remove('order');
        $this->session->set('order', $order->getId());
        $body = [
            "apiOperation" => "INITIATE_CHECKOUT",
            "interaction" => [
                "operation" => "PURCHASE",
                "merchant" => [
                    "name" => "ARZ MURR"
                ],
                "returnUrl" => "$url/rtp",
                "cancelUrl" => "$url/rtp",
                "displayControl" => [
                    "billingAddress" => "HIDE"
                ]
            ],
            "order" => [
                "currency" => $currency,
                "id" => $order->getId(),
                "amount" => $amount,
                "description" => "ordered goods"
            ]
        ];
        // print_r($body);
        $this->logger->info(json_encode($body));
        $response = $this->client->request('POST', $this->BASE_API . "session", [
            'body' => json_encode($body),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [$this->username, $this->password],
        ]);

        $content = $response->toArray(false);
        $this->logger->error(json_encode($content));
        $session = new session;
        $session->setOrders($order);
        $session->setSession($content['session']['id']);
        $session->setResponse(json_encode($content));
        $session->setIndicator($content['successIndicator']);
        $this->mr->persist($session);
        $this->mr->flush();
        return array(true, $content['session']['id'], $order);
    }

    public function retrievedataForTopUpRTP($auth, $status, $indicator, $res, $transId, $suyooler, $cardnumber, $phone, $senderId)
    {
        // echo $indicator;
        try {
            $parameters = array();
            $session = $this->mr->getRepository(session::class)->findOneBy(['indicator' => $indicator]);
            $transaction = new bob_transactions;
            $transaction->setSession($session);
            $transaction->setResponse(json_encode($res));
            $transaction->setStatus($status);
            $this->mr->persist($transaction);
            $this->mr->flush();
            if ($status == "CAPTURED") {
                $this->session->remove('sessionBobId');
                $order = $session->getOrders();
                $order->setstatus(orders::$statusOrder['PAID']);
                $this->mr->persist($order);
                $this->mr->flush();
                $topup = $this->suyoolServices->UpdateCardTopUpTransaction($session->getOrders()->gettransId(), 3, strval($session->getOrders()->gettransId()), (float)$session->getOrders()->getamount(), $session->getOrders()->getcurrency(), substr($cardnumber, -4));
                $transaction->setflagCode($topup[2]);
                $transaction->setError($topup[3]);
                $this->mr->persist($transaction);
                $session->getOrders()->getcurrency() == "USD" ? $currency = "$" : $currency = "LL";
                if ($topup[0]) {
                    $currency == "$" ? $amount = number_format($topup[1], 2) : $amount = number_format($topup[1]);
                    $status = true;
                    $imgsrc = "build/images/Loto/success.png";
                    $title = "Money Added Successfully";
                    $description = "You have successfully added {$currency} {$amount} to {$this->session->get('SenderInitials')}' Suyool wallet.";
                    $button = "Continue";

                    $parameters = [
                        'status' => $status,
                        'title' => $title,
                        'imgsrc' => $imgsrc,
                        'description' => $description,
                        'button' => $button,
                        'redirect' => $this->session->get('Code')
                    ];
                    $order = $session->getOrders();
                    $order->setstatus(orders::$statusOrder['COMPLETED']);
                    $this->mr->persist($order);
                    $this->mr->flush();
                    $params = json_encode(['currency' => $currency, 'amount' => $topup[1], 'nonsuyooler' => $phone]);
                    $content = $this->notificationServices->getContent('CardTopUpRtp');
                    $this->notificationServices->addNotification($senderId, $content, $params, 0, "");
                    return array(true, $parameters);
                } else {
                    $this->logger->error(json_encode($topup));
                    $status = false;
                    $imgsrc = "build/images/Loto/error.png";
                    $title = "Please Try Again";
                    $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                    $button = "Try Again";
                    $parameters = [
                        'status' => $status,
                        'title' => $title,
                        'imgsrc' => $imgsrc,
                        'description' => $description,
                        'button' => $button,
                        'redirect' => $this->session->get('Code')
                    ];
                    // $order = $session->getOrders();
                    // $order->setstatus(orders::$statusOrder['CANCELED']);
                    // $this->mr->persist($order);
                    // $this->mr->flush();
                    return array(true, $parameters);
                }
            } else {
                $topup = $this->suyoolServices->UpdateCardTopUpTransaction($session->getOrders()->gettransId(), 9, $session->getOrders()->getId() . "-" . $session->getOrders()->gettransId(), (float)$session->getOrders()->getamount(), $session->getOrders()->getcurrency(), substr($cardnumber, -4));
                $this->logger->error(json_encode($topup));
                $transaction->setflagCode($topup[2]);
                $transaction->setError($topup[3]);
                $this->mr->persist($transaction);
                if ($topup[0] == true) {
                    $status = false;
                    $imgsrc = "build/images/Loto/error.png";
                    $title = "Please Try Again";
                    $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                    $button = "Try Again";
                    $parameters = [
                        'status' => $status,
                        'title' => $title,
                        'imgsrc' => $imgsrc,
                        'description' => $description,
                        'button' => $button,
                        'redirect' => $this->session->get('Code')
                    ];
                    $order = $session->getOrders();
                    $order->setstatus(orders::$statusOrder['CANCELED']);
                    $this->mr->persist($order);
                    $this->mr->flush();

                    return array(true, $parameters);
                } else {
                    $this->logger->error(json_encode($topup));
                    $status = false;
                    $imgsrc = "build/images/Loto/error.png";
                    $title = "Please Try Again";
                    $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                    $button = "Try Again";
                    $parameters = [
                        'status' => $status,
                        'title' => $title,
                        'imgsrc' => $imgsrc,
                        'description' => $description,
                        'button' => $button,
                        'redirect' => $this->session->get('Code')
                    ];
                    $order = $session->getOrders();
                    $order->setstatus(orders::$statusOrder['CANCELED']);
                    $this->mr->persist($order);
                    $this->mr->flush();
                    return array(true, $parameters);
                }
            }
        } catch (Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }

    public function SessionRTPFromBobPayment($amount, $currency, $transId, $suyooler = null)
    {
        try {
            $order = $this->mr->getRepository(orders::class)->findOneBy(['transId' => $transId, 'status' => 'PENDING'], ['created' => 'DESC']);
            // dd($order);
            if (is_null($order)) {
                $this->session->remove('sessionBobId');
                $order = new orders;
                $order->setstatus(orders::$statusOrder['PENDING']);
                $order->setsuyoolUserId($suyooler);
                $order->settransId($transId);
                $order->setamount($amount);
                $order->setcurrency($currency);
                $order->settype("rtp");
                $this->mr->persist($order);
                $this->mr->flush();
            } else {
                $now = new DateTime();
                $now = $now->format('Y-m-d H:i:s');
                $date = $order->getCreated();
                $date->modify('+9 minutes');
                $dateAfter09Minutes = $date->format('Y-m-d H:i:s');
                if ($now > $dateAfter09Minutes) {
                    $this->session->remove('sessionBobId');
                    $order = new orders;
                    $order->setstatus(orders::$statusOrder['PENDING']);
                    $order->setsuyoolUserId($suyooler);
                    $order->settransId($transId);
                    $order->setamount($amount);
                    $order->setcurrency($currency);
                    $order->settype("rtp");
                    $this->mr->persist($order);
                    $this->mr->flush();
                } else {
                    $order->setAttempt($order->getAttempt() + 1);
                    $this->mr->persist($order);
                    $this->mr->flush();
                }
            }
            $url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
            $this->session->remove('order');
            $this->session->set('order', $transId);
            $session = $this->session->get('sessionBobId');
            if (is_null($session)) {
                $sessionfromDB = $this->mr->getRepository(session::class)->findOneBy(['orders' => $order->getId()]);
                if (is_null($sessionfromDB)) {
                    $body = [
                        "apiOperation" => "INITIATE_CHECKOUT",
                        "interaction" => [
                            "operation" => "PURCHASE",
                            "merchant" => [
                                "name" => "SUYOOL"
                            ],
                            "returnUrl" => "$url/topupRTP",
                            // "cancelUrl" => "$url/topupRTP",
                            "displayControl" => [
                                "billingAddress" => "HIDE"
                            ]
                        ],
                        "order" => [
                            "currency" => $currency,
                            "id" => $transId,
                            "amount" => $amount,
                            "description" => "rtp"
                        ]
                    ];
                    // echo json_encode($body,true);
                    // print_r($body);
                    $response = $this->client->request('POST', $this->BASE_API . "session", [
                        'body' => json_encode($body),
                        'headers' => [
                            'Content-Type' => 'application/json',
                        ],
                        'auth_basic' => [$this->username, $this->password],
                    ]);

                    $content = $response->toArray(false);
                    // dd($content);
                    // print_r($content);
                    $session = new session;
                    $session->setOrders($order);
                    $session->setSession($content['session']['id']);
                    $session->setResponse(json_encode($content));
                    $session->setIndicator($content['successIndicator']);
                    $this->mr->persist($session);
                    $this->mr->flush();

                    $this->session->set('sessionBobId', $content['session']['id']);

                    $sessionToBeSet = $content['session']['id'];
                } else {
                    $sessionToBeSet = $sessionfromDB->getSession();
                }
            } else {
                $sessionToBeSet = $this->session->get('sessionBobId');
            }
            return array(true, $sessionToBeSet, $order);
        } catch (Exception $e) {
            return array(false);
        }
    }
}
