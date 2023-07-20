<?php

namespace App\Controller;

use App\Entity\Alfa\Order;
use App\Entity\Alfa\Postpaid;
use App\Entity\Alfa\Prepaid;
use App\Entity\Alfa\Invoices;
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
        // dd($orders);
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
            $sendBill = $bobServices->Bill($data["mobileNumber"]);
            $sendBillRes = json_decode($sendBill, true);
            if ($sendBillRes["ResponseText"] == "Success") {
                // dd($sendBillRes);
                $invoices = new Invoices;
                $invoices
                    ->setfees(null)
                    ->setfees1(null)
                    ->setamount(null)
                    ->setamount1(null)
                    ->setamount2(null)
                    ->setreferenceNumber(null)
                    ->setinformativeOriginalWSamount(null)
                    ->settotalamount(null)
                    ->setcurrency(null)
                    ->setrounding(null)
                    ->setadditionalfees(null)
                    ->setPin(null)
                    ->setTransactionId(null)
                    ->setSuyoolUserId(rand())
                    ->setGsmNumber($data["mobileNumber"]);
                $this->mr->persist($invoices);
                $this->mr->flush();

                $invoicesId = $invoices->getId();
                // dd($invoicesId);
            } else {
                echo "error";
                $invoicesId = -1;
            }
            $message = "connected";
        } else {
            $message = "not connected";
            $invoicesId = -1;
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'invoicesId' => $invoicesId
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
            $retrieveResults = $bobServices->RetrieveResults($data["currency"], $data["mobileNumber"], $data["Pin"]);
            $jsonResult = json_decode($retrieveResults, true);
            // dd($jsonResult["Values"]["ReferenceNumber"]);

            $Pin = implode("", $data["Pin"]);
            $RandSuyoolUserId = rand();
            $invoicesId = $data["invoicesId"];
            // dd($invoicesId);

            $invoices =  $this->mr->getRepository(Invoices::class)->findOneBy(['id' => $invoicesId]);
            // $Postpaid = new Postpaid;
            $invoices
                ->setfees($jsonResult["Values"]["Fees"])
                ->setfees1($jsonResult["Values"]["Fees1"])
                ->setamount($jsonResult["Values"]["Amount"])
                ->setamount1($jsonResult["Values"]["Amount1"])
                ->setamount2($jsonResult["Values"]["Amount2"])
                ->setreferenceNumber($jsonResult["Values"]["ReferenceNumber"])
                ->setinformativeOriginalWSamount($jsonResult["Values"]["InformativeOriginalWSAmount"])
                ->settotalamount($jsonResult["Values"]["TotalAmount"])
                ->setcurrency($jsonResult["Values"]["Currency"])
                ->setrounding($jsonResult["Values"]["Rounding"])
                ->setadditionalfees($jsonResult["Values"]["AdditionalFees"])
                ->setSuyoolUserId($RandSuyoolUserId)
                ->setPin($Pin)
                ->setGsmNumber($data["mobileNumber"])
                ->setTransactionId($jsonResult["Values"]["TransactionId"]);

            $this->mr->persist($invoices);
            $this->mr->flush();

            // dd($invoices->getId());

            $invoicesId = $invoices->getId();
            // $postpaid = $this->mr->getRepository(Postpaid::class)->findOneBy(['id' => $postpayedId]);


            // $order = new Order;
            // $order
            //     ->setsuyoolUserId($RandSuyoolUserId)
            //     ->settransId(null)
            //     ->setpostpaidId($postpaid)
            //     ->setprepaidId(null)
            //     ->setstatus("Pending")
            //     ->setamount($jsonResult["Values"]["TotalAmount"])
            //     ->setcurrency($jsonResult["Values"]["Currency"]);
            // $this->mr->persist($order);
            // $this->mr->flush();

            // dd($order);
            $message = "connected";
        } else {
            $message = "not connected";
            $invoicesId = -1;
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'postpayed' => $invoicesId
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
        // dd($data);

        $Postpaid_With_id = $this->mr->getRepository(Invoices::class)->findOneBy(['id' => $data["ResponseId"]]);
        // dd($Postpaid_With_id);

        if ($data != null) {
            $billPay = $bobServices->BillPay($Postpaid_With_id);
            dd($billPay);

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
                $prepaid = $this->mr->getRepository(Prepaid::class)->findOneBy(['id' => $prepaidId]);

                $order = new Order;
                $order
                    ->setsuyoolUserId(1234)
                    ->settransId(null)
                    ->setpostpaidId(null)
                    ->setprepaidId($prepaid)
                    ->setstatus("true")
                    ->setamount($data["amountLBP"])
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

    // /**
    //  * @Route("/tst", name="app_alfa_tst")
    //  */
    // public function test()
    // {
    //     // $prepaid = $this->mr->getRepository(Prepaid::class)->findOneBy(['suyoolUserId'=>123]);
    //     // $order=$this->mr->getRepository(Order::class)->findAll();
    //     // dd($order[0]->getprepaidId());
    //     // $order = new Order;
    //     // $order
    //     //     ->setsuyoolUserId(345)
    //     //     ->settransId(null)
    //     //     ->setpostpaidId(null)
    //     //     ->setprepaidId($prepaid)
    //     //     ->setstatus("Pending")
    //     //     ->setamount("ss")
    //     //     ->setcurrency("lbp");
    //     // $this->mr->persist($order);
    //     // $this->mr->flush();

    //     // $postpaid = $this->mr->getRepository(Postpaid::class)->findOneBy(['suyoolUserId'=>1189401235]);
    //     $order = $this->mr->getRepository(Order::class)->findAll();
    //     dd($order[1]->getpostpaidId());
    //     // dd($order);
    //     $order = new Order;
    //     $order
    //         ->setsuyoolUserId(1189401235)
    //         ->settransId(null)
    //         ->setpostpaidId($postpaid)
    //         ->setprepaidId(null)
    //         ->setstatus("Pending")
    //         ->setamount("ss")
    //         ->setcurrency("lbp");
    //     $this->mr->persist($order);
    //     $this->mr->flush();

    //     dd();
    //     // $orders = $this->mr->getRepository(Order::class)->findAll();
    //     // dd($postpaid);
    //     $parameters['Test'] = "tst";
    //     return $this->render('alfa/index.html.twig', [
    //         'parameters' => $parameters
    //     ]);
    // }
}
