<?php

namespace App\Service;

use App\Entity\topup\attempts;
use App\Entity\topup\blackListCards;
use App\Entity\topup\bob_transactions;
use App\Entity\topup\invoices;
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
    private $BASE_API_HOSTED_SESSION;

    public function __construct(HttpClientInterface $client, LoggerInterface $logger, SessionInterface $session, ManagerRegistry $mr, SuyoolServices $suyoolServices, NotificationServices $notificationServices)
    {
        $this->client = $client;
        if ($_ENV['APP_ENV'] == "dev") {
            $this->BASE_API = "https://test-bobsal.gateway.mastercard.com/api/rest/version/73/merchant/testsuyool/";
            $this->username = "merchant.TESTSUYOOL";
            $this->password = "002bcc643011b3cef6967ff40d140d71";
            $this->BASE_API_HOSTED_SESSION = "https://test-bobsal.gateway.mastercard.com/api/rest/version/72/merchant/testsuyool/";
        } else {
            $this->BASE_API = "https://bobsal.gateway.mastercard.com/api/rest/version/73/merchant/suyool/";
            $this->username = "merchant.SUYOOL";
            $this->password = "652cdf87fd1c82530b7bfdd0c36662f3";
            $this->BASE_API_HOSTED_SESSION = "https://bobsal.gateway.mastercard.com/api/rest/version/72/merchant/suyool/";
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
            $this->session->remove('indicator');

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

            $this->session->set('indicator', $content['successIndicator']);

            $this->mr->persist($session);
            $this->mr->flush();
            return array(true, $content['session']['id'], $order);
        } catch (Exception $e) {
            return array(false);
        }
    }

    public function RetrievePaymentDetails($suyoolUserId = null, $senderPhone = null, $receiverPhone = null)
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
                if ($response->getStatusCode() === 400) {
                    $this->logger->error("400");
                    return array(false, "ERROR");
                }
                $content = json_decode($response->getContent(), true, 512, JSON_INVALID_UTF8_IGNORE);

                //$content = $response->toArray(false);
                // dd(end($content['transaction']));
                $this->logger->error(json_encode($content));
                if ($content['result'] != 'ERROR') {
                    $attempts = new attempts();
                    $attempts->setResponse(json_encode($content))
                        ->setReceiverPhone($receiverPhone)
                        ->setSenderPhone($senderPhone)
                        ->setSuyoolUserId($suyoolUserId)
                        ->setTransactionId($content['id'])
                        ->setAmount($content['amount'])
                        ->setCurrency($content['currency'])
                        ->setStatus($content['status'])
                        ->setResult($content['result'])
                        ->setAuthStatus($content['authenticationStatus'])
                        ->setCard(end($content['transaction'])['sourceOfFunds']['provided']['card']['number'])
                        ->setName(@end($content['transaction'])['sourceOfFunds']['provided']['card']['nameOnCard']);

                    $this->mr->persist($attempts);
                    $this->mr->flush();
                }
                if ($content['result'] == "SUCCESS") return array(true, $content);
            }
            return array(false, "ERROR");
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return array(false, $e->getMessage());
        }
    }

    public function retrievedataForTopUp($auth, $status, $indicator, $res, $transId, $suyooler, $cardnumber, $cardholdername)
    {
        // echo $indicator;
        try {
            $attemptsPerCard = $this->mr->getRepository(attempts::class)->GetTransactionsPerCard($cardnumber);
            $attemptsPerCardSum = $this->mr->getRepository(attempts::class)->GetTransactionPerCardSum($cardnumber);
            $blacklistcards = $this->mr->getRepository(blackListCards::class)->findOneBy(['card' => $cardnumber]);
            if (!is_null($blacklistcards)) {
                $emailMessageBlacklistedCard = "Dear,<br><br>Our automated system has detected a potential fraudulent transaction requiring your attention:<br><br>";

                $emailMessageBlacklistedCard .= "We have identified that the card with the number {$cardnumber} has been blacklisted. <br><br>";

                $emailMessageBlacklistedCard .= "</ul><br><br>Please initiate the necessary protocol for further investigation and action.<br><a href='https://suyool.com'>Suyool.com</a>";
                if ($_ENV['APP_ENV'] == 'dev') {
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent Topup Transaction', 'anthony.saliba@elbarid.com', $emailMessageBlacklistedCard, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                } else {
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent Topup Transaction', 'web@suyool.com,it@suyool.com,arz@elbarid.com', $emailMessageBlacklistedCard, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                }
                $this->logger->info('Send email');
            }
            $this->logger->info(json_encode($attemptsPerCard));
            if ($attemptsPerCard[0] >= 2) {
                $emailMessageUpTo2Times = "Dear,<br><br>Our automated system has detected a potential fraudulent transaction requiring your attention:<br><br>";

                $emailMessageUpTo2Times .= "We have identified more than two transactions associated with the card number {$cardnumber}: <br><br>";

                foreach ($attemptsPerCard[1] as $index => $attemptsPerCardHolder) {
                    $emailMessageUpTo2Times .= "<li>Transaction ID: " . $attemptsPerCardHolder->getTransactionId() . "</li>";
                    $emailMessageUpTo2Times .= "<li>BIN Card: " . $attemptsPerCardHolder->getCard() . "</li>";
                    $emailMessageUpTo2Times .= "<li>Suyooler ID: " . $attemptsPerCardHolder->getSuyoolUserId() . "</li>";
                    $emailMessageUpTo2Times .= "<li>Holder Name: " . $attemptsPerCardHolder->getName() . "</li>&nbsp;<br/>";
                }
                $emailMessageUpTo2Times .= "</ul><br><br>Please initiate the necessary protocol for further investigation and action.<br><a href='https://suyool.com'>Suyool.com</a>";
                if ($_ENV['APP_ENV'] == 'dev') {
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent Topup Transaction', 'anthony.saliba@elbarid.com', $emailMessageUpTo2Times, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                } else {
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent Topup Transaction', 'web@suyool.com,it@suyool.com,arz@elbarid.com', $emailMessageUpTo2Times, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                }
                $this->logger->info('Send email');
            }
            if ($attemptsPerCardSum[0] > 1500.000) {
                $emailMessageUpTo5Thousands = "Dear,<br><br>Our automated system has detected a potential fraudulent transaction requiring your attention:<br><br>";
                $emailMessageUpTo5Thousands .= "The card with the number {$cardnumber} has processed transactions totaling up to $1500. <br><br>";
                $emailMessageUpTo5Thousands .= "<ul>";

                foreach ($attemptsPerCardSum[1] as $index => $attemptsPerCardSumHolder) {
                    $emailMessageUpTo5Thousands .= "<li>Transaction ID: " . $attemptsPerCardSumHolder->getTransactionId() . "</li>";
                    $emailMessageUpTo5Thousands .= "<li>BIN Card: " . $attemptsPerCardSumHolder->getCard() . "</li>";
                    $emailMessageUpTo5Thousands .= "<li>Suyooler ID: " . $attemptsPerCardSumHolder->getSuyoolUserId() . "</li>";
                    $emailMessageUpTo5Thousands .= "<li>Holder Name: " . $attemptsPerCardSumHolder->getName() . "</li><br>";
                }
                $emailMessageUpTo5Thousands .= "</ul><br><br>Please initiate the necessary protocol for further investigation and action.<br><a href='https://suyool.com'>Suyool.com</a>";
                if ($_ENV['APP_ENV'] == 'dev') {
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent Topup Transaction', 'anthony.saliba@elbarid.com', $emailMessageUpTo5Thousands, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                } else {
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent Topup Transaction', 'web@suyool.com,it@suyool.com,arz@elbarid.com', $emailMessageUpTo5Thousands, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                }
                $this->logger->info('Send email 2');
            }
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
                $additionalData = [
                    'cardEnding' => substr($cardnumber, -4),
                    'cardNumber' => $cardnumber,
                    'cardHolderName' => $cardholdername
                ];
                $order->setstatus(orders::$statusOrder['PAID']);
                $topup = $this->suyoolServices->UpdateCardTopUpTransaction($session->getOrders()->gettransId(), 3, strval($session->getOrders()->gettransId()), $session->getOrders()->getamount(), $session->getOrders()->getcurrency(), json_encode($additionalData));
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


                    // $order = $session->getOrders();
                    $order->setstatus(orders::$statusOrder['COMPLETED']);
                    $this->mr->persist($order);
                    $this->mr->flush();
                    if ($topup[2] == 1) {
                        $params = json_encode(['currency' => $currency, 'amount' => $topup[1]]);
                        $content = $this->notificationServices->getContent('CardTopUp');
                        $this->notificationServices->addNotification($suyooler, $content, $params, 0, "");
                    } else {
                        $imgsrc = "build/images/Loto/warning.png";
                        $title = "Compliance Check";
                        $description = "This transaction is subject to a compliance check.<br>You will receive a notification of its status within 24 hours.";
                        $button = "OK";
                    }
                    $parameters = [
                        'status' => $status,
                        'title' => $title,
                        'imgsrc' => $imgsrc,
                        'description' => $description,
                        'button' => $button,
                        'infoSuccess' => true
                    ];
                    return array(true, $parameters);
                } else {
                    // $this->logger->error(json_encode($topup));
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
                // $topup = $this->suyoolServices->UpdateCardTopUpTransaction($session->getOrders()->gettransId(), 9,  $session->getOrders()->getId() . "-" . $session->getOrders()->gettransId(), (float)$session->getOrders()->getamount(), $session->getOrders()->getcurrency(), substr($cardnumber, -4));
                // $this->logger->error(json_encode($topup));
                // $transaction->setflagCode($topup[2]);
                // $transaction->setError($topup[3]);
                // $this->mr->persist($transaction);
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
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
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

    public function retrievedataForTopUpRTP($auth, $status, $indicator, $res, $transId, $suyooler, $cardnumber, $phone, $senderId, $cardholdername)
    {
        // echo $indicator;
        try {
            $attemptsPerCard = $this->mr->getRepository(attempts::class)->GetTransactionsPerCard($cardnumber);
            $attemptsPerCardSum = $this->mr->getRepository(attempts::class)->GetTransactionPerCardSum($cardnumber);
            $blacklistcards = $this->mr->getRepository(blackListCards::class)->findOneBy(['card' => $cardnumber]);
            if (!is_null($blacklistcards)) {
                $emailMessageBlacklistedCard = "Dear,<br><br>Our automated system has detected a potential fraudulent transaction requiring your attention:<br><br>";

                $emailMessageBlacklistedCard .= "We have identified that the card with the number {$cardnumber} has been blacklisted. <br><br>";

                $emailMessageBlacklistedCard .= "</ul><br><br>Please initiate the necessary protocol for further investigation and action.<br><a href='https://suyool.com'>Suyool.com</a>";
                if ($_ENV['APP_ENV'] == 'dev') {
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent Topup Transaction', 'anthony.saliba@elbarid.com', $emailMessageBlacklistedCard, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                } else {
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent Topup Transaction', 'web@suyool.com,it@suyool.com,arz@elbarid.com', $emailMessageBlacklistedCard, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                }
                $this->logger->info('Send email');
            }
            $this->logger->info(json_encode($attemptsPerCardSum));
            if ($attemptsPerCardSum[0] > 1500.000) {
                $emailMessageUpTo5Thousands = "Dear,<br><br>Our automated system has detected a potential fraudulent transaction requiring your attention:<br><br>";
                $emailMessageUpTo5Thousands .= "The card with the number {$cardnumber} has processed transactions totaling up to $1500. <br><br>";
                $emailMessageUpTo5Thousands .= "<ul>";

                foreach ($attemptsPerCardSum[1] as $index => $attemptsPerCardSumHolder) {
                    $emailMessageUpTo5Thousands .= "<li>Transaction ID: " . $attemptsPerCardSumHolder->getTransactionId() . "</li>";
                    $emailMessageUpTo5Thousands .= "<li>BIN Card: " . $attemptsPerCardSumHolder->getCard() . "</li>";
                    $emailMessageUpTo5Thousands .= "<li>Sender Phone: " . $attemptsPerCardSumHolder->getSenderPhone() . "</li>";
                    $emailMessageUpTo5Thousands .= "<li>Holder Name: " . $attemptsPerCardSumHolder->getName() . "</li>&nbsp;<br/>";
                }
                $emailMessageUpTo5Thousands .= "</ul><br><br>Please initiate the necessary protocol for further investigation and action.<br><a href='https://suyool.com'>Suyool.com</a>";
                if ($_ENV['APP_ENV'] == 'dev') {
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent Topup Transaction', 'anthony.saliba@elbarid.com', $emailMessageUpTo5Thousands, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                } else {
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent Topup Transaction', 'web@suyool.com,it@suyool.com,arz@elbarid.com', $emailMessageUpTo5Thousands, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                }
                $this->logger->info('Send email 2');
            }
            $blacklistcards = $this->mr->getRepository(blackListCards::class)->findOneBy(['card' => $cardnumber]);

            $this->logger->info(json_encode($attemptsPerCard));
            if ($attemptsPerCard[0] >= 2) {
                $emailMessageUpTo2Times = "Dear,<br><br>Our automated system has detected a potential fraudulent transaction requiring your attention:<br><br>";
                $emailMessageUpTo2Times .= "We have identified more than two transactions associated with the card number {$cardnumber}: <br><br>";

                foreach ($attemptsPerCard[1] as $index => $attemptsPerCardHolder) {
                    $emailMessageUpTo2Times .= "<li>Transaction ID: " . $attemptsPerCardHolder->getTransactionId() . "</li>";
                    $emailMessageUpTo2Times .= "<li>BIN Card: " . $attemptsPerCardHolder->getCard() . "</li>";
                    $emailMessageUpTo2Times .= "<li>Sender Phone: " . $attemptsPerCardHolder->getSenderPhone() . "</li>";
                    $emailMessageUpTo2Times .= "<li>Holder Name: " . $attemptsPerCardHolder->getName() . "</li><br>";
                }
                $emailMessageUpTo2Times .= "</ul><br><br>Please initiate the necessary protocol for further investigation and action.<br><a href='https://suyool.com'>Suyool.com</a>";
                if ($_ENV['APP_ENV'] == 'dev') {
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent Topup Transaction', 'anthony.saliba@elbarid.com', $emailMessageUpTo2Times, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                } else {
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent Topup Transaction', 'web@suyool.com,it@suyool.com,arz@elbarid.com', $emailMessageUpTo2Times, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                }
                $this->logger->info('Send email');
            }
            $parameters = array();
            $session = $this->mr->getRepository(session::class)->findOneBy(['session' => $this->session->get('sessionBobId')]);
            $transaction = new bob_transactions;
            $transaction->setSession($session);
            $transaction->setResponse(json_encode($res));
            $transaction->setStatus($status);
            $this->mr->persist($transaction);
            $this->mr->flush();
            if ($status == "CAPTURED") {
                $additionalData = [
                    'cardEnding' => substr($cardnumber, -4),
                    'cardNumber' => $cardnumber,
                    'cardHolderName' => $cardholdername
                ];
                $this->session->remove('sessionBobId');
                $order = $session->getOrders();
                $order->setstatus(orders::$statusOrder['PAID']);
                $this->mr->persist($order);
                $this->mr->flush();
                $topup = $this->suyoolServices->UpdateCardTopUpTransaction($session->getOrders()->gettransId(), 3, strval($session->getOrders()->gettransId()), (float)$session->getOrders()->getamount(), $session->getOrders()->getcurrency(), json_encode($additionalData));
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

                    $order = $session->getOrders();
                    $order->setstatus(orders::$statusOrder['COMPLETED']);
                    $this->mr->persist($order);
                    $this->mr->flush();

                    if ($topup[2] == 1) {
                        $params = json_encode(['currency' => $currency, 'amount' => $topup[1], 'nonsuyooler' => $phone]);
                        $content = $this->notificationServices->getContent('CardTopUpRtp');
                        $this->notificationServices->addNotification($senderId, $content, $params, 0, "");
                    } else {
                        $imgsrc = "build/images/Loto/warning.png";
                        $title = "Compliance Check";
                        $description = "This transaction is subject to a compliance check.<br>{$this->session->get('SenderInitials')} will receive a notification of its status within 24 hours.";
                        $button = "OK";
                    }

                    $parameters = [
                        'status' => $status,
                        'title' => $title,
                        'imgsrc' => $imgsrc,
                        'description' => $description,
                        'button' => $button,
                        'redirect' => $this->session->get('Code')
                    ];
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
                    $this->mr->flush();
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
            return new Response('', 500);
        }
    }

    public function SessionRTPFromBobPayment($amount, $currency, $transId, $suyooler = null)
    {
        try {
            $order = $this->mr->getRepository(orders::class)->findOneBy(['transId' => $transId, 'status' => 'PENDING'], ['created' => 'DESC']);
            // dd($order);
            if (is_null($order)) {
                $this->session->remove('indicator');
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

                    $this->session->set('indicator', $content['successIndicator']);
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

    public function RetrievePaymentDetailsOnCheck($order, $suyoolUserId)
    {
        try {
            $response = $this->client->request('GET', $this->BASE_API . "order/" . $order, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth_basic' => [$this->username, $this->password],
            ]);
            //$response = json_encode($response->getContent(), JSON_INVALID_UTF8_IGNORE);
            //$content = $;
            if ($response->getStatusCode() === 400) {
                return array(true, 'CANCELED');
            }
            $content = json_decode($response->getContent(), true, 512, JSON_INVALID_UTF8_IGNORE);
            // dd($content);
            // $this->logger->error(json_encode($content));
            if ($content['result'] != 'ERROR') {
                $attempts = new attempts();
                $attempts->setResponse(json_encode($content))
                    ->setSuyoolUserId($suyoolUserId)
                    ->setTransactionId($content['id'])
                    ->setAmount($content['amount'])
                    ->setCurrency($content['currency'])
                    ->setStatus($content['status'])
                    ->setResult($content['result'])
                    ->setAuthStatus($content['authenticationStatus'])
                    ->setCard(end($content['transaction'])['sourceOfFunds']['provided']['card']['number'])
                    ->setName(@end($content['transaction'])['sourceOfFunds']['provided']['card']['nameOnCard']);

                $this->mr->persist($attempts);
                $this->mr->flush();

                if ($content['status'] == 'CAPTURED') {
                    return array(true, 'PAID');
                }
            }
            return array(true, 'CANCELED');
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return array(false, $e->getMessage());
        }
    }

    public function retrievedataForTopUpAfterCheck($status, $res, $cardnumber, $session)
    {
        // echo $indicator;
        try {
            if ($status == 'CAPTURED') {
                $attemptsPerCard = $this->mr->getRepository(attempts::class)->GetTransactionsPerCard($cardnumber);
                $attemptsPerCardSum = $this->mr->getRepository(attempts::class)->GetTransactionPerCardSum($cardnumber);
                $blacklistcards = $this->mr->getRepository(blackListCards::class)->findOneBy(['card' => $cardnumber]);
                if (!is_null($blacklistcards)) {
                    $emailMessageBlacklistedCard = "Dear,<br><br>Our automated system has detected a potential fraudulent transaction requiring your attention:<br><br>";

                    $emailMessageBlacklistedCard .= "We have identified that the card with the number {$cardnumber} has been blacklisted. <br><br>";

                    $emailMessageBlacklistedCard .= "</ul><br><br>Please initiate the necessary protocol for further investigation and action.<br><a href='https://suyool.com'>Suyool.com</a>";
                    // $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent RTP Transaction', 'anthony.saliba@elbarid.com', $emailMessageBlacklistedCard, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent RTP Transaction', 'web@suyool.com,it@suyool.com,arz@elbarid.com', $emailMessageBlacklistedCard, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                    $this->logger->info('Send email');
                }
                $this->logger->info(json_encode($attemptsPerCardSum));
                if ($attemptsPerCardSum[0] > 1500.000) {
                    $emailMessageUpTo5Thousands = "Dear,<br><br>Our automated system has detected a potential fraudulent transaction requiring your attention:<br><br>";
                    $emailMessageUpTo5Thousands .= "The card with the number {$cardnumber} has processed transactions totaling up to $1500. <br><br>";
                    $emailMessageUpTo5Thousands .= "<ul>";

                    foreach ($attemptsPerCardSum[1] as $index => $attemptsPerCardSumHolder) {
                        $emailMessageUpTo5Thousands .= "<li>Transaction ID: " . $attemptsPerCardSumHolder->getTransactionId() . "</li>";
                        $emailMessageUpTo5Thousands .= "<li>BIN Card: " . $attemptsPerCardSumHolder->getCard() . "</li>";
                        $emailMessageUpTo5Thousands .= "<li>Sender Phone: " . $attemptsPerCardSumHolder->getSenderPhone() . "</li>";
                        $emailMessageUpTo5Thousands .= "<li>Suyooler ID: " . $attemptsPerCardSumHolder->getSuyoolUserId() . "</li>";
                        $emailMessageUpTo5Thousands .= "<li>Holder Name: " . $attemptsPerCardSumHolder->getName() . "</li>&nbsp;<br/>";
                    }
                    $emailMessageUpTo5Thousands .= "</ul><br><br>Please initiate the necessary protocol for further investigation and action.<br><a href='https://suyool.com'>Suyool.com</a>";
                    // $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent RTP Transaction', 'anthony.saliba@elbarid.com', $emailMessageUpTo5Thousands, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent RTP Transaction', 'web@suyool.com,it@suyool.com,arz@elbarid.com', $emailMessageUpTo5Thousands, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                    $this->logger->info('Send email 2');
                }
                $blacklistcards = $this->mr->getRepository(blackListCards::class)->findOneBy(['card' => $cardnumber]);

                $this->logger->info(json_encode($attemptsPerCard));
                if ($attemptsPerCard[0] >= 2) {
                    $emailMessageUpTo2Times = "Dear,<br><br>Our automated system has detected a potential fraudulent transaction requiring your attention:<br><br>";
                    $emailMessageUpTo2Times .= "We have identified more than two transactions associated with the card number {$cardnumber}: <br><br>";

                    foreach ($attemptsPerCard[1] as $index => $attemptsPerCardHolder) {
                        $emailMessageUpTo2Times .= "<li>Transaction ID: " . $attemptsPerCardHolder->getTransactionId() . "</li>";
                        $emailMessageUpTo2Times .= "<li>BIN Card: " . $attemptsPerCardHolder->getCard() . "</li>";
                        $emailMessageUpTo2Times .= "<li>Sender Phone: " . $attemptsPerCardHolder->getSenderPhone() . "</li>";
                        $emailMessageUpTo2Times .= "<li>Suyooler ID: " . $attemptsPerCardHolder->getSuyoolUserId() . "</li>";
                        $emailMessageUpTo2Times .= "<li>Holder Name: " . $attemptsPerCardHolder->getName() . "</li><br>";
                    }
                    $emailMessageUpTo2Times .= "</ul><br><br>Please initiate the necessary protocol for further investigation and action.<br><a href='https://suyool.com'>Suyool.com</a>";
                    // $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent RTP Transaction', 'anthony.saliba@elbarid.com', $emailMessageUpTo2Times, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                    $this->suyoolServices->sendDotNetEmail('[Alert] Suspected Fraudulent RTP Transaction', 'web@suyool.com,it@suyool.com,arz@elbarid.com', $emailMessageUpTo2Times, "", "", "suyool@noreply.com", "Suyool", 1, 0);
                    $this->logger->info('Send email');
                }
                $session = $this->mr->getRepository(session::class)->findOneBy(['session' => $session]);
                $transaction = new bob_transactions;
                $transaction->setSession($session);
                $transaction->setResponse(json_encode($res));
                $transaction->setStatus($status);
                $this->mr->persist($transaction);
                $this->mr->flush();
                $this->session->remove('sessionBobId');
                $order = $session->getOrders();
                $order->setstatus(orders::$statusOrder['PAID']);
                $this->mr->persist($order);
                $this->mr->flush();
                $topup = $this->suyoolServices->UpdateCardTopUpTransaction($session->getOrders()->gettransId(), 3, strval($session->getOrders()->gettransId()), (float)$session->getOrders()->getamount(), $session->getOrders()->getcurrency(), substr($cardnumber, -4));
                $transaction->setflagCode($topup[2]);
                $transaction->setError($topup[3]);
                $this->mr->persist($transaction);
                if ($topup[0]) {
                    $order = $session->getOrders();
                    $order->setstatus(orders::$statusOrder['COMPLETED']);
                    $this->mr->persist($order);
                    $this->mr->flush();
                    return true;
                }
            }
            return true;
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return new Response('', 500);
        }
    }

    public function hostedsession($amount, $currency, $transId, $suyoolUserId, $code)
    {
        $order = $this->mr->getRepository(orders::class)->findTransactionsThatIsNotCompleted($transId);
        // dd($order);
        if (is_null($order)) {
            $this->session->remove('hostedSessionId');
            $attempts = 1;
            $order = new orders;
            $order->setstatus(orders::$statusOrder['PENDING']);
            $order->setsuyoolUserId($suyoolUserId);
            $order->settransId($transId);
            $order->setamount($amount);
            $order->setcurrency($currency);
            $order->setAttempt($attempts);
            $order->settype("rtp");
            $order->setCode($code);
            $this->mr->persist($order);
            $this->mr->flush();
        } else {
            $now = new DateTime();
            $now = $now->format('Y-m-d H:i:s');
            $date = $order->getCreated();
            $date->modify('+9 minutes');
            $dateAfter09Minutes = $date->format('Y-m-d H:i:s');
            if ($now > $dateAfter09Minutes) {
                // dd($order->getAttempt());
                $this->session->remove('hostedSessionId');
                $attempts = $order->getAttempt() + 1;
                $order = new orders;
                $order->setstatus(orders::$statusOrder['PENDING']);
                $order->setsuyoolUserId($suyoolUserId);
                $order->settransId($transId);
                $order->setamount($amount);
                $order->setcurrency($currency);
                $order->setAttempt($attempts);
                $order->settype("rtp");
                $order->setCode($code);
                $this->mr->persist($order);
                $this->mr->flush();
            } else {
                $attempts = $order->getAttempt() + 1;
                $order->setAttempt($order->getAttempt() + 1);
                $this->mr->persist($order);
                $this->mr->flush();
            }
        }
        $session = $this->session->get('hostedSessionId');
        if (is_null($session)) {
            $body = [
                "session" => [
                    "authenticationLimit" => 25
                ]
            ];
            // print_r($body);
            $this->logger->error(json_encode($body));
            $response = $this->client->request('POST', $this->BASE_API_HOSTED_SESSION . "session", [
                'body' => json_encode($body),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth_basic' => [$this->username, $this->password],
            ]);

            $content = $response->toArray(false);
            $session = $content['session']['id'];
            $session = new session;
            $session->setOrders($order);
            $session->setSession($content['session']['id']);
            $session->setResponse(json_encode($content));
            $session->setIndicator($content['session']['version']);
            $this->mr->persist($session);
            $this->mr->flush();
            $session = $content['session']['id'];
            $this->session->set('hostedSessionId', $session);
        }
        $url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
        $body = [
            "authentication" => [
                "channel" => "PAYER_BROWSER",
                "redirectResponseUrl" => $url . "/pay2"
            ],
            "order" => [
                "id" => $transId,
                "amount" => $amount,
                "currency" => $currency
            ],
            "transaction" => [
                "id" => "trans-" . $attempts
            ]
        ];
        $this->logger->error(json_encode($body));
        $response = $this->client->request('PUT', $this->BASE_API_HOSTED_SESSION . "session/" . $session, [
            'body' => json_encode($body),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [$this->username, $this->password],
        ]);
        $this->session->set('orderidhostedsession', $transId);
        $this->session->set('transactionidhostedsession', "trans-" . $attempts);
        $content = $response->toArray(false);
        $this->logger->error(json_encode($content));
        return array($session, $transId, $attempts);
    }

    public function updatedTransactionInHostedSessionToPay($suyooler, $receiverPhone, $senderPhone,$senderInitials)
    {
        $body = [
            "apiOperation" => "PAY",
            "authentication" => [
                "transactionId" => $_COOKIE['transactionidhostedsession']
            ],
            "session" => [
                "id" => "{$_COOKIE['hostedSessionId']}"
            ]
        ];
        $transIdNew = explode("trans-", $_COOKIE['transactionidhostedsession']);
        $response = $this->client->request('PUT', $this->BASE_API_HOSTED_SESSION . "order/{$_COOKIE['orderidhostedsession']}/transaction/{$transIdNew[1]}", [
            'body' => json_encode($body),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [$this->username, $this->password],
        ]);
        $content = $response->toArray(false);
        $this->logger->info(json_encode($body));
        $this->logger->info(json_encode($content));
        $this->logger->info(json_encode($this->BASE_API . "order/{$_COOKIE['orderidhostedsession']}/transaction/{$transIdNew[1]}"));
        // dd($content);
        $attempts = new attempts();
        $attempts->setSuyoolUserId($suyooler)
            ->setReceiverPhone($receiverPhone)
            ->setSenderPhone($senderPhone)
            ->setResponse(json_encode($content))
            ->setTransactionId($_COOKIE['orderidhostedsession'])
            ->setAmount($content['order']['amount'])
            ->setCurrency($content['order']['currency'])
            ->setStatus($content['order']['status'])
            ->setResult($content['result'])
            ->setAuthStatus($content['order']['authenticationStatus'])
            ->setCard($content['sourceOfFunds']['provided']['card']['number'])
            ->setName(@$content['sourceOfFunds']['provided']['card']['nameOnCard']);
        $this->mr->persist($attempts);
        $this->mr->flush();
        $session = $this->mr->getRepository(session::class)->findOneBy(['session' => $_COOKIE['hostedSessionId']]);
        if ($content['order']['status'] == 'CAPTURED') {
            // $session = $this->mr->getRepository(session::class)->findOneBy(['session' => $_COOKIE['hostedSessionId']]);
            $additionalData = [
                'cardEnding'=>substr($content['sourceOfFunds']['provided']['card']['number'], -4),
                'cardNumber'=>$content['sourceOfFunds']['provided']['card']['number'],
                'cardHolderName'=>$content['sourceOfFunds']['provided']['card']['nameOnCard']
            ];
            $transaction = new bob_transactions;
            $transaction->setSession($session);
            $transaction->setResponse(json_encode($content));
            $transaction->setStatus($content['order']['status']);
            $this->mr->persist($transaction);
            $this->mr->flush();
            $order = $session->getOrders();
            $order->setstatus(orders::$statusOrder['PAID']);
            $this->mr->persist($order);
            $this->mr->flush();
            $topup = $this->suyoolServices->UpdateCardTopUpTransaction($_COOKIE['orderidhostedsession'], 3, strval($_COOKIE['orderidhostedsession']), $content['order']['amount'], $content['order']['currency'], json_encode($additionalData));
            $transaction->setflagCode($topup[2]);
            $transaction->setError($topup[3]);
            $this->mr->persist($transaction);
            if ($topup[0]) {
                $content['order']['currency'] == 'USD' ? $currency = "$" : $currency = "LL";
                $currency == "$" ? $amount = number_format($topup[1], 2) : $amount = number_format($topup[1]);
                $order = $session->getOrders();
                $order->setstatus(orders::$statusOrder['COMPLETED']);
                $this->mr->persist($order);
                $this->mr->flush();
                $imgsrc = "build/images/Loto/success.png";
                $title = "Money Added Successfully";
                $description = "You have successfully added {$currency} {$amount} to {$senderInitials}' Suyool wallet.";
                $button = "Continue";

                if ($topup[2] == 1) {
                    $params = json_encode(['currency' => $currency, 'amount' => $topup[1], 'nonsuyooler' => $receiverPhone]);
                    $content = $this->notificationServices->getContent('CardTopUpRtp');
                    $this->notificationServices->addNotification($suyooler, $content, $params, 0, "");
                } else {
                    $title = "Compliance Check";
                    $description = "This transaction is subject to a compliance check.<br>You will receive a notification of its status within 24 hours.";
                    $button = "OK";
                }

                $parameters = [
                    'title' => $title,
                    'imgsrc' => $imgsrc,
                    'description' => $description,
                    'button' => $button,
                    'infoSuccess' => true,
                    'redirect' => $session->getOrders()->getCode()
                ];
                return $parameters;
            } else {
                $imgsrc = "build/images/Loto/error.png";
                $title = "Please Try Again";
                $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
                $button = "Try Again";
                $parameters = [
                    'title' => $title,
                    'imgsrc' => $imgsrc,
                    'description' => $description,
                    'button' => $button,
                    'redirect' => $session->getOrders()->getCode()
                ];
                return $parameters;
            }
        } else {
            $imgsrc = "build/images/Loto/error.png";
            $title = "Please Try Again";
            $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
            $button = "Try Again";
            $parameters = [
                'title' => $title,
                'imgsrc' => $imgsrc,
                'description' => $description,
                'button' => $button,
                'redirect' => "test/" . $session->getOrders()->getCode()
            ];
            return $parameters;
        }
        return true;
    }
    public function checkCardNumber()
    {
        $response = $this->client->request('GET', $this->BASE_API . "session/" . $this->session->get('hostedSessionId'), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [$this->username, $this->password],
        ]);
        $content = $response->toArray(false);
        $this->logger->info(json_encode($content));
        return $content['sourceOfFunds']['provided']['card']['number'];
    }

    //

    public function hostedsessionTest($amount, $currency, $transId, $suyoolUserId, $code)
    {
        $order = $this->mr->getRepository(orders::class)->findTransactionsThatIsNotCompleted($transId);
        // dd($order);
        if (is_null($order)) {
            $this->session->remove('hostedSessionId');
            $attempts = 1;
            $order = new orders;
            $order->setstatus(orders::$statusOrder['PENDING']);
            $order->setsuyoolUserId($suyoolUserId);
            $order->settransId($transId);
            $order->setamount($amount);
            $order->setcurrency($currency);
            $order->setAttempt($attempts);
            $order->settype("rtp");
            $order->setCode($code);
            $this->mr->persist($order);
            $this->mr->flush();
        } else {
            $now = new DateTime();
            $now = $now->format('Y-m-d H:i:s');
            $date = $order->getCreated();
            $date->modify('+9 minutes');
            $dateAfter09Minutes = $date->format('Y-m-d H:i:s');
            if ($now > $dateAfter09Minutes) {
                // dd($order->getAttempt());
                $this->session->remove('hostedSessionId');
                $attempts = $order->getAttempt() + 1;
                $order = new orders;
                $order->setstatus(orders::$statusOrder['PENDING']);
                $order->setsuyoolUserId($suyoolUserId);
                $order->settransId($transId);
                $order->setamount($amount);
                $order->setcurrency($currency);
                $order->setAttempt($attempts);
                $order->settype("rtp");
                $order->setCode($code);
                $this->mr->persist($order);
                $this->mr->flush();
            } else {
                $attempts = $order->getAttempt() + 1;
                $order->setAttempt($order->getAttempt() + 1);
                $this->mr->persist($order);
                $this->mr->flush();
            }
        }
        $session = $this->session->get('hostedSessionId');
        if (is_null($session)) {
            $body = [
                "session" => [
                    "authenticationLimit" => 25
                ]
            ];
            // print_r($body);
            $this->logger->error(json_encode($body));
            $response = $this->client->request('POST', $this->BASE_API_HOSTED_SESSION . "session", [
                'body' => json_encode($body),
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth_basic' => [$this->username, $this->password],
            ]);

            $content = $response->toArray(false);
            $session = $content['session']['id'];
            $session = new session;
            $session->setOrders($order);
            $session->setSession($content['session']['id']);
            $session->setResponse(json_encode($content));
            $session->setIndicator($content['session']['version']);
            $this->mr->persist($session);
            $this->mr->flush();
            $session = $content['session']['id'];
            $this->session->set('hostedSessionId', $session);
        }
        $url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
        $body = [
            "authentication" => [
                "channel" => "PAYER_BROWSER",
                "redirectResponseUrl" => $url . "/pay2test"
            ],
            "order" => [
                "id" => $transId,
                "amount" => $amount,
                "currency" => $currency
            ],
            "transaction" => [
                "id" => "trans-" . $attempts
            ]
        ];
        $this->logger->error(json_encode($body));
        $response = $this->client->request('PUT', $this->BASE_API_HOSTED_SESSION . "session/" . $session, [
            'body' => json_encode($body),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [$this->username, $this->password],
        ]);
        $this->session->set('orderidhostedsession', $transId);
        $this->session->set('transactionidhostedsession', "trans-" . $attempts);
        $content = $response->toArray(false);
        $this->logger->error(json_encode($content));
        return array($session, $transId, $attempts);
    }

    public function updatedTransactionInHostedSessionToPayTest($suyooler, $receiverPhone, $senderPhone)
    {
        $body = [
            "apiOperation" => "PAY",
            "authentication" => [
                "transactionId" => $_COOKIE['transactionidhostedsession']
            ],
            "session" => [
                "id" => "{$_COOKIE['hostedSessionId']}"
            ]
        ];
        $transIdNew = explode("trans-", $_COOKIE['transactionidhostedsession']);
        $response = $this->client->request('PUT', $this->BASE_API_HOSTED_SESSION . "order/{$_COOKIE['orderidhostedsession']}/transaction/{$transIdNew[1]}", [
            'body' => json_encode($body),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [$this->username, $this->password],
        ]);
        $content = $response->toArray(false);
        $this->logger->info(json_encode($body));
        $this->logger->info(json_encode($content));
        $this->logger->info(json_encode($this->BASE_API_HOSTED_SESSION . "order/{$_COOKIE['orderidhostedsession']}/transaction/{$transIdNew[1]}"));
        // dd($content);
        $attempts = new attempts();
        $attempts->setSuyoolUserId($suyooler)
            ->setReceiverPhone($receiverPhone)
            ->setSenderPhone($senderPhone)
            ->setResponse(json_encode($content))
            ->setTransactionId($_COOKIE['orderidhostedsession'])
            ->setAmount($content['order']['amount'])
            ->setCurrency($content['order']['currency'])
            ->setStatus($content['order']['status'])
            ->setResult($content['result'])
            ->setAuthStatus($content['order']['authenticationStatus'])
            ->setCard($content['sourceOfFunds']['provided']['card']['number'])
            ->setName(@$content['sourceOfFunds']['provided']['card']['nameOnCard']);
        $this->mr->persist($attempts);
        $this->mr->flush();
        $session = $this->mr->getRepository(session::class)->findOneBy(['session' => $_COOKIE['hostedSessionId']]);
        if ($content['order']['status'] == 'CAPTURED') {
            // $session = $this->mr->getRepository(session::class)->findOneBy(['session' => $_COOKIE['hostedSessionId']]);
            $transaction = new bob_transactions;
            $transaction->setSession($session);
            $transaction->setResponse(json_encode($content));
            $transaction->setStatus($content['order']['status']);
            $this->mr->persist($transaction);
            $this->mr->flush();
            $order = $session->getOrders();
            $order->setstatus(orders::$statusOrder['PAID']);
            $this->mr->persist($order);
            $this->mr->flush();
            // $topup = $this->suyoolServices->UpdateCardTopUpTransaction($_COOKIE['orderidhostedsession'], 3, strval($_COOKIE['orderidhostedsession']), $content['order']['amount'], $content['order']['currency'], substr($content['sourceOfFunds']['provided']['card']['number'], -4));
            $topup = array(true, 1);
            $transaction->setflagCode(0);
            $transaction->setError("test");
            $this->mr->persist($transaction);
            if ($topup[0]) {
                $content['order']['currency'] == 'USD' ? $currency = "$" : $currency = "LL";
                $currency == "$" ? $amount = number_format($topup[1], 2) : $amount = number_format($topup[1]);
                $order = $session->getOrders();
                $order->setstatus(orders::$statusOrder['COMPLETED']);
                $this->mr->persist($order);
                $this->mr->flush();
                $imgsrc = "build/images/Loto/success.png";
                $title = "Money Added Successfully";
                $description = "You have successfully added {$currency} {$amount} to your Suyool wallet. <br>Check your new balance";
                $button = "Continue";

                $parameters = [
                    'title' => $title,
                    'imgsrc' => $imgsrc,
                    'description' => $description,
                    'button' => $button,
                    'infoSuccess' => true,
                    'redirect' => 'topup2test'
                ];
                return $parameters;
            }
        } else {
            $imgsrc = "build/images/Loto/error.png";
            $title = "Please Try Again";
            $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
            $button = "Try Again";
            $parameters = [
                'title' => $title,
                'imgsrc' => $imgsrc,
                'description' => $description,
                'button' => $button,
                'redirect' => 'topup2test'
            ];
            return $parameters;
        }
        return true;
    }
    public function SessionInvoicesFromBobPayment($amount, $currency, $transId, $suyooler = null, $ref)
    {
        // dd(invoices::$statusOrder['HELD']);
        // dd($invoiceid);
        try {
            $order = new orders;
            $order->setstatus(orders::$statusOrder['PENDING']);
            $order->setsuyoolUserId($suyooler);
            $order->settransId($transId);
            $order->setamount($amount);
            $order->setcurrency($currency);
            $order->setType("Invoices");
            $this->mr->persist($order);
            $this->mr->flush();
            $url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
            $this->session->remove('order');
            $this->session->set('order', $ref);
            $body = [
                "apiOperation" => "INITIATE_CHECKOUT",
                "interaction" => [
                    "operation" => "PURCHASE",
                    "merchant" => [
                        "name" => "SUYOOL"
                    ],
                    "returnUrl" => "$url/payment_bob",
                    // "cancelUrl" => "$url/topupRTP",
                    "displayControl" => [
                        "billingAddress" => "HIDE"
                    ]
                ],
                "order" => [
                    "currency" => $currency,
                    "id" => $ref,
                    "amount" => $amount,
                    "description" => "Payment Gateway"
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
//             dd($content);
            // print_r($content);
            $session = new session;
            $session->setOrders($order);
            $session->setSession($content['session']['id']);
            $session->setResponse(json_encode($content));
            $session->setIndicator($content['successIndicator']);
            $this->mr->persist($session);
            $this->mr->flush();

            return array(true, $content['session']['id'], $order);
        } catch (Exception $e) {
//            dd($e->getMessage());
            return array(false);
        }
    }

    public function retrievedataForInvoices($auth, $status, $indicator, $res, $cardnumber, $invoiceid)
    {
        // echo $indicator;
        try{
            $parameters = array();
            $session = $this->mr->getRepository(session::class)->findOneBy(['indicator' => $indicator]);
            $transaction = new bob_transactions;
            $transaction->setSession($session);
            $transaction->setResponse(json_encode($res));
            $transaction->setStatus($status);
            $this->mr->persist($transaction);
            $this->mr->flush();
            if ($status == "CAPTURED") {
                $order = $session->getOrders();
                $order->setstatus(orders::$statusOrder['PAID']);
                $this->mr->persist($order);
                $this->mr->flush();
                $cardnumber = array("cardEnding"=>substr($cardnumber, -4));
                 $topup = $this->suyoolServices->UpdateCardTopUpTransaction($session->getOrders()->gettransId(), 3, strval($session->getOrders()->gettransId()), (float)$session->getOrders()->getamount(), $session->getOrders()->getcurrency(), json_encode($cardnumber));
                 //$topup = array(true, 1, 1, "SUCESS");
                $transaction->setflagCode($topup[2]);
                $transaction->setError($topup[3]);
                $this->mr->persist($transaction);
                $session->getOrders()->getcurrency() == "USD" ? $currency = "$" : $currency = "LL";
                if ($topup[0]) {
                    $currency == "$" ? $amount = number_format($topup[1], 2) : $amount = number_format($topup[1]);
                    $status = true;
                    $imgsrc = "build/images/Loto/success.png";
                    $title = "Money Retrieve Succesfully";
                    $description = "You have succesfully Pay";
                    $button = "Continue";

                    $parameters = [
                        'status' => $status,
                        'title' => $title,
                        'imgsrc' => $imgsrc,
                        'description' => $description,
                        'button' => $button,
                        // 'redirect' => $this->session->get('Code')
                    ];
                    $order = $session->getOrders();
                    $order->setstatus(orders::$statusOrder['COMPLETED']);
                    $this->mr->persist($order);
                    $this->mr->flush();
                    // $params = json_encode(['currency' => $currency, 'amount' => $topup[1], 'nonsuyooler' => $phone]);
                    // $content = $this->notificationServices->getContent('CardTopUpRtp');
                    // $this->notificationServices->addNotification($senderId, $content, $params, 0, "");
                    $invoice = $this->mr->getRepository(invoices::class)->findOneBy(['reference' => $invoiceid]);
                    $invoice->setStatus(invoices::$statusOrder['COMPLETED']);
                    $this->mr->persist($invoice);
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
                    // $order = $session->getOrders();
                    // $order->setstatus(orders::$statusOrder['CANCELED']);
                    // $this->mr->persist($order);
                    // $this->mr->flush();
                    return array(true, $parameters);
                }
            } else {
//                $topup = $this->suyoolServices->UpdateCardTopUpTransaction($session->getOrders()->gettransId(), 9, $session->getOrders()->getId() . "-" . $session->getOrders()->gettransId(), (float)$session->getOrders()->getamount(), $session->getOrders()->getcurrency(), substr($cardnumber, -4));
//                $this->logger->error(json_encode($topup));
//                $transaction->setflagCode($topup[2]);
//                $transaction->setError($topup[3]);
//                $this->mr->persist($transaction);
//                if ($topup[0] == true) {
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
//                } else {
//                    $this->logger->error(json_encode($topup));
//                    $status = false;
//                    $imgsrc = "build/images/Loto/error.png";
//                    $title = "Please Try Again";
//                    $description = "An error has occurred with your top up. <br>Please try again later or use another top up method.";
//                    $button = "Try Again";
//                    $parameters = [
//                        'status' => $status,
//                        'title' => $title,
//                        'imgsrc' => $imgsrc,
//                        'description' => $description,
//                        'button' => $button,
//                        'redirect' => $this->session->get('Code')
//                    ];
//                    $order = $session->getOrders();
//                    $order->setstatus(orders::$statusOrder['CANCELED']);
//                    $this->mr->persist($order);
//                    $this->mr->flush();
//                    return array(true, $parameters);
//                }
            }
        }catch(Exception $e){
            return array(false);
        }
    }
}
