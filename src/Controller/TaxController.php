<?php


namespace App\Controller;


use App\Service\BobServices;
use App\Service\LogsService;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TaxController extends AbstractController
{
    private $mr;
    private $params;
    private $session;

    public function __construct(ManagerRegistry $mr, ParameterBagInterface $params, SessionInterface $sessionInterface)
    {
        $this->mr = $mr->getManager('tax');
        $this->params = $params;
        $this->session = $sessionInterface;
    }

    /**
     * @Route("/tax", name="app_ogero")
     */
    public function index(Request $request, BobServices $bobServices, NotificationServices $notificationServices): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $webkey = apache_request_headers();
            $webkeyDecrypted = SuyoolServices::decryptWebKey($webkey);

            if ($notificationServices->checkUser($webkeyDecrypted['merchantId'], $webkeyDecrypted['lang']) &&  $webkeyDecrypted['devicesType'] == "CORPORATE") {
                $suyoolUserId = $webkeyDecrypted['merchantId'];

                if ($data != null) {
                    $requestData = [
                        "ChannelType" => "API",
                        "ItemId" => 2497,
                        "VenId" => 20,
                        "ProductId" => 25,
                        "TaxMeta" => [
                            "Amount" => $data["amount"],
                            "DocumentNumber" => $data['documentNumber']
                        ]
                    ];
                    $RetrieveChannel = $bobServices->RetrieveReqResults($requestData);

                    $pushlog = new LogsService($this->mr);
                    $pushlog->pushLogs(new Logs,"ap2_tax_bill",@$RetrieveChannel[6],@$RetrieveChannel[3],@$RetrieveChannel[4],@$RetrieveChannel[5]);
                    // dd($RetrieveChannel);
                    if ($RetrieveChannel[0] == true) {
                        $resp = $RetrieveChannel[1]["Values"];
                        $displayedFees = intval($resp["Fees"]) + intval($resp["Fees1"]) + intval($resp["AdditionalFees"]) + intval($resp["OgeroFees"]);

                        $LandlineReq = new LandlineRequest;
                        $LandlineReq
                            ->setsuyoolUserId($suyoolUserId)
                            ->setgsmNumber($data["mobileNumber"])
                            ->setresponse($RetrieveChannel[3])
                            ->seterrordesc($RetrieveChannel[2])
                            ->settransactionId($resp["TransactionId"])
                            ->setogeroBills(json_encode($resp["OgeroBills"]))
                            ->setogeroPenalty($resp["OgeroPenalty"])
                            ->setogeroInitiationDate($resp["OgeroInitiationDate"])
                            ->setogeroClientName($resp["OgeroClientName"])
                            ->setogeroAddress($resp["OgeroAddress"])
                            ->setcurrency($resp["Currency"])
                            ->setamount($resp["Amount"])
                            ->setamount1($resp["Amount1"])
                            ->setamount2($resp["Amount2"])
                            ->settotalAmount($resp["TotalAmount"])
                            ->setogeroTotalAmount($resp["OgeroTotalAmount"])
                            ->setogeroFees($resp["OgeroFees"])
                            ->setadditionalFees($resp["AdditionalFees"])
                            ->setfees($resp["Fees"])
                            ->setfees1($resp["Fees1"])
                            ->setdisplayedFees($displayedFees)
                            ->setrounding($resp["Rounding"]);
                        $this->mr->persist($LandlineReq);
                        $this->mr->flush();

                        $LandlineReqId = $LandlineReq->getId();
                        $messageBack = "Success";
                        $message = $resp;
                        $mobileNb = $data["mobileNumber"];
                        $parameters = [
                            'mobileNb' => $mobileNb,
                            'displayBill' => $message,
                            'LandlineReqId' => $LandlineReqId,
                            'displayedFees' => $displayedFees,
                        ];
                    } else {
                        $LandlineReq = new LandlineRequest;
                        $LandlineReq
                            ->setsuyoolUserId($suyoolUserId)
                            ->setgsmNumber($data["mobileNumber"])
                            ->setresponse($RetrieveChannel[3])
                            ->seterrordesc($RetrieveChannel[2]);
                        $this->mr->persist($LandlineReq);
                        $this->mr->flush();
                        $error = explode("-", $RetrieveChannel[2]);
                        $errorcode = $error[1];
                        // if($errorcode == 113){
                        //     dd("ok");
                        // }
                        $messageBack = $RetrieveChannel[2];
                        $LandlineReqId = -1;
                        $mobileNb = $data["mobileNumber"];
                        switch ($error[1]) {
                            case 111:
                                $title = "No Pending Bill";
                                $body = "There is no pending bill on the mobile number {$data["mobileNumber"]}<br/>Kindly try again later";
                                break;
                            case 108:
                                $title = "No Pending Bill";
                                $body = "There is no pending bill on the mobile number {$data["mobileNumber"]}<br/>Kindly try again later";
                                break;
                            case 112:
                                $title = "Number Not Found";
                                $body = "The number you entered was not found in the system.<br>Kindly try another number.";
                                break;
                            default:
                                $title = "Number Not Found";
                                $body = "The number you entered was not found in the system.<br>Kindly try another number.";
                                break;
                        }
                        $popup = [
                            "Title" => @$title,
                            "globalCode" => 0,
                            "flagCode" => 800,
                            "Message" => @$body,
                            "isPopup" => true
                        ];
                        $parameters = [
                            'Popup' => $popup
                        ];
                    }
                } else {
                    $message = "not connected";
                    $LandlineReqId = -1;
                    $mobileNb = -1;
                }

                return new JsonResponse([
                    'status' => true,
                    'message' => @$messageBack,
                    'data' => @$parameters
                ], 200);
            } else {
                return new JsonResponse([
                    'status' => false,
                    'message' => 'Unauthorize'
                ], 401);
            }
        } catch (Exception $e) {
            return new JsonResponse([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}