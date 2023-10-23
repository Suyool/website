<?php

namespace App\Service;

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

}