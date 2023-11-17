<?php

namespace App\Service;

use App\Entity\Alfa\Order as AlfaOrder;
use App\Entity\Loto\loto;
use App\Entity\Loto\order;
use App\Entity\Support;
use App\Entity\topup\orders;
use App\Entity\Touch\Order as TouchOrder;
use Doctrine\Persistence\ManagerRegistry;

class DashboardService 
{

    public function LotoDashboard($LotoRepository,$drawId){
        $parameters = array();
       $count = $LotoRepository->getRepository(loto::class)->CompletedTicketsCount();
       $thismonth=$LotoRepository->getRepository(loto::class)->CompletedTicketsCountThisMonth();
       $TotalAmount=number_format($LotoRepository->getRepository(loto::class)->CompletedTicketsSumAmount());
       $resultArray=$LotoRepository->getRepository(order::class)->CountStatusTickets();
       $lastdrawTickets = $LotoRepository->getRepository(loto::class)->LastDrawTickets($drawId);
       $parameters=[
        'count'=>$count,
        'thismonth'=>$thismonth,
        'TotalAmount'=>$TotalAmount,
        'resultArray'=>$resultArray,
        'lastdraw'=>$lastdrawTickets
       ];

       return $parameters;

    }

    public function AlfaDashboard($alfaRepository){
        $parameters = array();
       $resultArray=$alfaRepository->getRepository(AlfaOrder::class)->CountStatusOrders();
       $postpaidcount=$alfaRepository->getRepository(AlfaOrder::class)->getMethodPaid('postpaid');
       $prepaidcount=$alfaRepository->getRepository(AlfaOrder::class)->getMethodPaid('prepaid');
       $postpaidsum=number_format($alfaRepository->getRepository(AlfaOrder::class)->getMethodPaidSum('postpaid'));
       $prepaidsum=number_format($alfaRepository->getRepository(AlfaOrder::class)->getMethodPaidSum('prepaid'));
       $parameters=[
        'postpaidcount'=>$postpaidcount,
        'prepaidcount'=>$prepaidcount,
        'resultArray'=>$resultArray,
        'postpaidsum'=>$postpaidsum,
        'prepaidsum'=>$prepaidsum
       ];

       return $parameters;

    }

    public function TouchDashboard($touchRepository){
        $parameters = array();
       $resultArray=$touchRepository->getRepository(TouchOrder::class)->CountStatusOrders();
       $postpaidcount=$touchRepository->getRepository(TouchOrder::class)->getMethodPaid('postpaid');
       $prepaidcount=$touchRepository->getRepository(TouchOrder::class)->getMethodPaid('prepaid');
       $postpaidsum=number_format($touchRepository->getRepository(TouchOrder::class)->getMethodPaidSum('postpaid'));
       $prepaidsum=number_format($touchRepository->getRepository(TouchOrder::class)->getMethodPaidSum('prepaid'));
       $parameters=[
        'postpaidcount'=>$postpaidcount,
        'prepaidcount'=>$prepaidcount,
        'resultArray'=>$resultArray,
        'postpaidsum'=>$postpaidsum,
        'prepaidsum'=>$prepaidsum
       ];

       return $parameters;

    }

    public function SupportDashboard($supportRepository){
        $parameters = array();
        $countMessage=$supportRepository->getRepository(Support::class)->CountSupports();
       $parameters=[
        'messageCount'=>$countMessage
       ];

       return $parameters;

    }

    public function PaymentDashboard($paymentRepository){
        $parameters = array();
        $resultArray=$paymentRepository->getRepository(orders::class)->CountStatus();
       $parameters=[
        'statusCount'=>$resultArray
       ];

       return $parameters;

    }

}