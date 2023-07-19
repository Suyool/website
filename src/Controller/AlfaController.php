<?php

namespace App\Controller;

use App\Entity\Alfa\Order;
use App\Entity\Alfa\Postpaid;
use App\Entity\Alfa\Prepaid;
use App\Service\LotoServices;
use App\Service\BobServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AlfaController extends AbstractController
{
    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('alfa');
    }

    /**
     * @Route("/alfa", name="app_alfa")
     */
    public function index()
    {
        $postpaid = $this->mr->getRepository(Postpaid::class)->findAll();
        $orders = $this->mr->getRepository(Order::class)->findAll();
        // dd($postpaid);
        $parameters['Test'] = "tst";

        return $this->render('alfa/index.html.twig', [
            'parameters' => $parameters
        ]);
    }

    /**
     * PostPaid
     * Provider : BOB
     * Desc: Send Pin to user based on phoneNumber
     * @Route("/alfa/bill", name="app_alfa_bill",methods="POST")
     */
    public function bill(Request $request, BobServices $bobServices)
    {
        $data = json_decode($request->getContent(), true);
        if ($data != null) {
            // $sendBill = $bobServices->Bill($data["mobileNumber"]);
            // dd($sendBill);
            $message = "connected";
        } else {
            $message = "not connected";
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message
        ], 200);
    }

    /**
     * PostPaid
     * Provider : BOB
     * Desc: Retrieve Channel Results 
     * @Route("/alfa/bill/RetrieveResults", name="app_alfa_RetrieveResults",methods="POST")
     */
    public function RetrieveResults(Request $request, BobServices $bobServices)
    {
        $data = json_decode($request->getContent(), true);
        // dd($data);
        if ($data != null) {
            // $retrieveResults = $bobServices->Bill($data["currency"],$data["mobileNumber"],$data["Pin"]);
            // dd($retrieveResults);

            $Postpaid = new Postpaid;
            $Postpaid->setfees("2")
                ->setfees1("0")
                ->setamount("104.58")
                ->setamount1("0")
                ->setamount2("0")
                ->setreferenceNumber("20230700000042")
                ->setinformativeOriginalWSamount("104.58")
                ->settotalamount("106.58")
                ->setcurrency("USD")
                ->setrounding("0")
                ->setadditionalfees("0")
                ->setSuyoolUserId("1234567")
                ->setPin("0000")
                ->setGsmNumber("70102030")
                ->setTransactionId("1735028");

            $this->mr->persist($Postpaid);
            $this->mr->flush();
            // dd($Postpaid->getId());
            $postpayed = $Postpaid->getId();
            $message = "connected";
        } else {
            $message = "not connected";
            $postpayed = -1;
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'postpayed' => $postpayed
        ], 200);
    }

    /**
     * PostPaid
     * Provider : BOB
     * Desc: Retrieve Channel Results 
     * @Route("/alfa/bill/pay", name="app_alfa_bill_pay",methods="POST")
     */
    public function billPay(Request $request, BobServices $bobServices)
    {
        $data = json_decode($request->getContent(), true);

        $Postpaid_With_id = $this->mr->getRepository(Postpaid::class)->findOneBy(['id' => $data["ResponseId"]]);
        dd($Postpaid_With_id);

        if ($data != null) {
            // $billPay = $bobServices->BillPay();
            // dd($billPay);

            $message = "connected";
        } else {
            $message = "not connected";
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message
        ], 200);
    }


    /**
     * PrePaid
     * Provider : LOTO
     * Desc: Fetch ReCharge vouchers
     * @Route("/alfa/ReCharge", name="app_alfa_ReCharge",methods="POST")
     */
    public function ReCharge(LotoServices $lotoServices)
    {
        $filter = $lotoServices->VoucherFilter("ALFA");

        return new JsonResponse([
            'status' => true,
            'message' => $filter
        ], 200);
    }

    /**
     * PrePaid
     * Provider : LOTO
     * Desc: Buy PrePaid vouchers
     * @Route("/alfa/BuyPrePaid", name="app_alfa_BuyPrePaid",methods="POST")
     */
    public function BuyPrePaid(Request $request, LotoServices $lotoServices)
    {
        $data = json_decode($request->getContent(), true);

        // dd($data["amountLBP"]);
        // dd($data["amountUSD"]);

        if ($data != null) {
            $BuyPrePaid = $lotoServices->BuyPrePaid($data["Token"], $data["category"], $data["type"]);
            $PayResonse = $BuyPrePaid["d"];
            // dd($PayResonse);

            if ($PayResonse["errorinfo"]["errormsg"] == "SUCCESS") {
                $prepaid = new Prepaid;
                $prepaid
                    ->setvoucherSerial($PayResonse["voucherSerial"])
                    ->setvoucherCode($PayResonse["voucherCode"])
                    ->setvoucherExpiry($PayResonse["voucherExpiry"])
                    ->setdescription($PayResonse["desc"])
                    ->setdisplayMessage($PayResonse["displayMessage"])
                    ->settoken($PayResonse["token"])
                    ->setbalance($PayResonse["balance"])
                    ->seterrorMsg($PayResonse["errorinfo"]["errormsg"])
                    ->setinsertId($PayResonse["insertId"])
                    ->setSuyoolUserId(345);

                $this->mr->persist($prepaid);
                $this->mr->flush();
                $IsSuccess = true;
                $prepaidId = $prepaid->getId();
                
                $order = new Order;
                $order
                    ->setsuyoolUserId(1234)
                    ->settransId(null)
                    ->setpostpaid_Id(null)
                    ->setprepaid_Id($prepaidId)
                    ->setstatus("true")
                    ->setamount(50000)
                    ->setcurrency("LBP");
                $this->mr->persist($order);
                $this->mr->flush();
                // dd($prepaidId);
            } else {
                $IsSuccess = false;
            }
        } else {
            $BuyPrePaid = "not connected";
            $IsSuccess = false;
        }

        return new JsonResponse([
            'status' => true,
            'message' => $BuyPrePaid,
            'IsSuccess' => $IsSuccess,
        ], 200);
    }
}
