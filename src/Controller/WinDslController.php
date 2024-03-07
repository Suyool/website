<?php

namespace App\Controller;

use App\Entity\Windsl\Logs;
use App\Entity\Windsl\Transactions;
use App\Entity\Windsl\Users;
use App\Service\LogsService;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use App\Service\WindslService;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class WinDslController extends AbstractController
{

    private $mr;
    private $windslService;
    private $session;
    private $suyoolServices;

    public function __construct(ManagerRegistry $managerRegistry, WindslService $windslService, SessionInterface $session, SuyoolServices $suyoolServices)
    {
        $this->mr = $managerRegistry->getManager("windsl");
        $this->windslService = $windslService;
        $this->session = $session;
        $this->suyoolServices = $suyoolServices;
    }

    /**
     * @Route("/windsl", name="app_windsl")
     */
    public function index(NotificationServices $notificationServices)
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $_POST['infoString'] = "Mwx9v3bq3GNGIWBYFJ1f1PcdL3j8SjmsS6y+Hc76TEtMxwGjwZQJHlGv0+EaTI7c";

        if (isset($_POST['infoString'])) {
            $decrypted_string = SuyoolServices::decrypt($_POST['infoString']); //['device'=>"aad", asdfsd]
            $suyoolUserInfo = explode("!#!", $decrypted_string);
            $devicetype = stripos($useragent, $suyoolUserInfo[1]);

            if ($notificationServices->checkUser($suyoolUserInfo[0], $suyoolUserInfo[2]) && !$devicetype) {
                $SuyoolUserId = $suyoolUserInfo[0];
                $this->session->set('suyoolUserId', $SuyoolUserId);
                // $this->session->set('suyoolUserId', 155);

                $parameters['deviceType'] = $suyoolUserInfo[1];

                return $this->render('windsl/index.html.twig', [
                    'parameters' => $parameters
                ]);
            } else {
                return $this->render('ExceptionHandling.html.twig');
            }
        } else {
            return $this->render('ExceptionHandling.html.twig');
        }
    }

    /**
     * @Route("/windsl/login", name="app_windsl_login",methods={"POST"})
     */
    public function login(Request $request)
    {
        $log = new LogsService($this->mr);
        try {
            $data = json_decode($request->getContent(false), true);
            if (isset($data['username']) && isset($data['password'])) {
                $login = $this->windslService->login($data['username'], $data['password']);
                $log->pushLogs(new Logs, "app_windsl_login", $login[3], $login[2], $login[4], $login[5]);
                if ($login[0]) {
                    $checkusers = $this->mr->getRepository(Users::class)->findOneBy(['winDslUserId' => $login[1]]);
                    if (is_null($checkusers)) {
                        $users = new Users();
                        $users->setUsername($data['username'])
                            ->setPassword(md5($data['password']))
                            ->setWinDslUserId($login[1])
                            ->setLastLogin();
                        $this->mr->persist($users);
                    } else {
                        $checkusers->setLastLogin();
                        $this->mr->persist($checkusers);
                    }
                    $this->mr->flush();
                    $balance = $this->windslService->checkBalance($login[1]);
                    $log->pushLogs(new Logs, "app_windsl_login", $login[3], $login[2], $login[4], $login[5]);
                    $this->session->set('userid', $login[1]);
                    $log->pushLogs(new Logs, "checkBalance", null, $balance[2], $balance[3], $balance[4]);
                    if ($balance) {
                        return new JsonResponse([
                            'status' => true,
                            'userid' => $login[1],
                            'balance' => $balance[1]
                        ]);
                    }
                    return new JsonResponse([
                        'status' => true,
                        'userid' => $login[1]
                    ]);
                }
                return new JsonResponse([
                    'status' => true,
                    'isSuccess' => $login[0],
                    'message' => $login[1]
                ]);
            }
        } catch (Exception $e) {
            $log->pushLogs(new Logs, "app_windsl_login", "", $e->getMessage(), "", 500);
            return new JsonResponse([
                'status' => true,
                'isSuccess' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/windsl/topup", name="app_windsl_topup",methods={"POST"})
     */
    public function topup(Request $request)
    {
        $log = new LogsService($this->mr);
        try {
            if (isset($data)) {
                $this->session->set('userid', 407860731284928);
                $user = $this->mr->getRepository(Users::class)->findOneBy(['winDslUserId' => $this->session->get('userid')]);
                $SuyoolUserId = $this->session->get('suyoolUserId');
                $data = json_decode($request->getContent(false), true);
                $transaction = new Transactions();
                $transaction->setUserId($user)
                    ->setsuyoolUserId(12)
                    ->setamount($data['amount'])
                    ->setfees(0)
                    ->setcurrency($data['currency'])
                    ->setstatus(Transactions::$statusOrder['PENDING']);
                $this->mr->persist($transaction);
                $this->mr->flush();
                // $pushutility = $this->suyoolServices->PushUtilities($SuyoolUserId,,$data['amount'],$data['currency'],0);
                $topup = $this->windslService->topup($this->session->get('userid'), $data['amount'], $data['currency']);
                $log->pushLogs(new Logs, "app_windsl_topup", $topup[1], $topup[2], $topup[3], $topup[4]);
                if ($topup) {
                    $transactionPending = $this->mr->getRepository(Transactions::class)->findOneBy(['users'=>$user->getId(),'status'=>Transactions::$statusOrder['PENDING']],['id'=>'desc']);
                    $transactionPending
                        ->setstatus(Transactions::$statusOrder['PURCHASED'])
                        ->settransId(123)
                        ->seterror("success");
                    $this->mr->persist($transaction);
                    $isSuccess = true;
                    //update by the amount
                } else {
                    //update amount to 0
                    $transactionPending = $this->mr->getRepository(Transactions::class)->findOneBy(['users'=>$user->getId(),'status'=>Transactions::$statusOrder['PENDING']],['id'=>'desc']);
                    $transactionPending
                        ->setstatus(Transactions::$statusOrder['CANCELED'])
                        ->seterror("ERROR IN TOPUP");
                    $this->mr->persist($transactionPending);
                    $isSuccess=false;
                }
                $this->mr->flush();
                return new JsonResponse([
                    'status'=>true,
                    'isSuccess'=>$isSuccess
                ]);
            }
        } catch (Exception $e) {
            $log->pushLogs(new Logs, "app_windsl_topup", "", $e->getMessage(), "", 500);
            return new JsonResponse([
                'status'=>false,
                'isSuccess'=>false,
                'message'=>$e->getMessage()
            ]);
        }
    }
}
