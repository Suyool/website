<?php

namespace App\Service;

use App\Entity\Alfa\Order as AlfaOrder;
use App\Entity\Loto\loto;
use App\Entity\Loto\order;
use Doctrine\Persistence\ManagerRegistry;

class DashboardService 
{

    public function LotoDashboard($LotoRepository){
        $parameters = array();
       $count = $LotoRepository->getRepository(loto::class)->CompletedTicketsCount();
       $thismonth=$LotoRepository->getRepository(loto::class)->CompletedTicketsCountThisMonth();
       $TotalAmount=number_format($LotoRepository->getRepository(loto::class)->CompletedTicketsSumAmount());
       $resultArray=$LotoRepository->getRepository(order::class)->CountStatusTickets();
       $parameters=[
        'count'=>$count,
        'thismonth'=>$thismonth,
        'TotalAmount'=>$TotalAmount,
        'resultArray'=>$resultArray
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

}