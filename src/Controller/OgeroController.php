<?php

namespace App\Controller;

use App\Entity\Ogero\LandlineRequest;
use App\Entity\Ogero\Order;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BobServices;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OgeroController extends AbstractController
{
    private $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr->getManager('ogero');
    }

    /**
     * @Route("/ogero", name="app_ogero")
     */
    public function index(): Response
    {
        $parameters['Test'] = "tst";
        // dd("oki");
        // $orders = $this->mr->getRepository(Order::class)->findAll();
        // dd($orders);

        return $this->render('ogero/index.html.twig', [
            'parameters' => $parameters
        ]);
    }

    /**
     * Landline
     * Provider : BOB
     * Desc: Get TrasactionId to user based on phoneNumber
     * @Route("/ogero/landline", name="app_ogero_landline",methods="POST")
     */
    public function bill(Request $request, BobServices $bobServices)
    {
        $data = json_decode($request->getContent(), true);
        if ($data != null) {
            $RetrieveChannel = $bobServices->RetrieveChannelResults($data["mobileNumber"]);
            if ($RetrieveChannel[0] == true) {
                $resp = $RetrieveChannel[1]["Values"];

                $LandlineReq = new LandlineRequest;
                $LandlineReq
                    ->setsuyoolUserId(89)
                    ->setgsmNumber($data["mobileNumber"])
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
                    ->setrounding($resp["Rounding"]);
                $this->mr->persist($LandlineReq);
                $this->mr->flush();

                $LandlineReqId = $LandlineReq->getId();
            } else {
                echo "error";
                $LandlineReqId = -1;
            }
            $message = "connected";
        } else {
            $message = "not connected";
            $LandlineReqId = -1;
        }

        return new JsonResponse([
            'status' => true,
            'message' => $message,
            'invoicesId' => $LandlineReqId
        ], 200);
    }
}
