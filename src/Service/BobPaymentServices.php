<?php

namespace App\Service;

use App\Entity\topup\bob_transactions;
use App\Entity\topup\orders;
use App\Entity\topup\session;
use App\Utils\Helper;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_encode;

class BobPaymentServices
{
    private $BOB_PAYMENT_GATEWAY;
    private $client;
    private $BOB_RETRIEVE_PAYMENT;
    private $session;
    private $mr;
    private $suyoolServices;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, Helper $helper, LoggerInterface $logger, SessionInterface $session, ManagerRegistry $mr, SuyoolServices $suyoolServices)
    {
        $this->client = $client;
        $this->BOB_PAYMENT_GATEWAY = "https://test-bobsal.gateway.mastercard.com/api/rest/version/73/merchant/testsuyool/session";
        $this->BOB_RETRIEVE_PAYMENT = "https://test-bobsal.gateway.mastercard.com/api/rest/version/73/merchant/testsuyool/order/";
        $this->session = $session;
        $this->mr = $mr->getManager('topup');
        $this->suyoolServices = $suyoolServices;
    }

    public function paymentGateWay($amount, $currency, $transId, $suyooler = null)
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
                "returnUrl" => "$url/topup",
                "cancelUrl" => "$url/topup",
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
        $response = $this->client->request('POST', $this->BOB_PAYMENT_GATEWAY, [
            'body' => json_encode($body),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [$_ENV['MERCHANT_SUYOOL_BOB_PAYMENT_GATEWAY'], $_ENV['PASSWORD_BOB_GATEWAY']],
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
    }

    public function RetrievePaymentDetails()
    {
        $session = $this->session->get('order');
        // echo $session;
        if (isset($session)) {
            $response = $this->client->request('GET', $this->BOB_RETRIEVE_PAYMENT . $session, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth_basic' => [$_ENV['MERCHANT_SUYOOL_BOB_PAYMENT_GATEWAY'], $_ENV['PASSWORD_BOB_GATEWAY']],
            ]);

            $content = $response->toArray(false);
            // dd($content);
            if ($content['result'] == "SUCCESS") return array(true, $content);
            return array(false, "ERROR");
        }
        return array(false, "ERROR");
    }

    public function retrievedataForTopUp($status, $indicator, $res, $transId, $suyooler)
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
        if ($status == "CAPTURED") {
            $topup = $this->suyoolServices->UpdateCardTopUpTransaction($session->getOrders()->gettransId(), 3, $indicator, (float)$session->getOrders()->getamount(), $session->getOrders()->getcurrency(), json_encode($res));
            if ($topup[0]) {
                $amount = number_format($session->getOrders()->getamount());
                $status = true;
                $imgsrc = "build/images/Loto/success.png";
                $title = "Money Added Succesfully";
                $description = "You have succesfully added {$session->getOrders()->getcurrency()} {$amount} to your Suyool wallet. <br>Check your new balance";
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

    public function retrievedataForTopUpTest($status, $indicator, $res, $transId, $suyooler)
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
        if ($status == "CAPTURED") {
                $amount = number_format($session->getOrders()->getamount());
                $status = true;
                $imgsrc = "build/images/Loto/success.png";
                $title = "Money Added Succesfully";
                $description = "You have succesfully added {$session->getOrders()->getcurrency()} {$amount} to your Suyool wallet. <br>Check your new balance";
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
        $response = $this->client->request('POST', $this->BOB_PAYMENT_GATEWAY, [
            'body' => json_encode($body),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [$_ENV['MERCHANT_SUYOOL_BOB_PAYMENT_GATEWAY'], $_ENV['PASSWORD_BOB_GATEWAY']],
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
    }
}
