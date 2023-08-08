<?php

namespace App\Command\loto;

use App\Entity\Loto\loto;
use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\order;
use App\Entity\Notification\content;
use App\Entity\Notification\Template;
use App\Service\LotoServices;
use App\Service\NotificationServices;
use App\Service\sendEmail;
use DateInterval;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class winningTickets extends Command
{
    private $mr;
    private $lotoServices;
    private $sendEmail;
    private $notificationServices;
    private $notifyMr;
    public function __construct(ManagerRegistry $mr, LotoServices $lotoServices, sendEmail $sendEmail, NotificationServices $notificationServices)
    {
        parent::__construct();

        $this->lotoServices = $lotoServices;
        $this->mr = $mr->getManager('loto');
        $this->sendEmail = $sendEmail;
        $this->notificationServices = $notificationServices;
        $this->notifyMr = $mr->getManager('notification');
    }

    protected function configure()
    {
        //php bin/console 
        $this
            ->setName('app:winning');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Fetch details send'
        ]);
        // $arr1=[4, 12, 8, 39, 24, 9];
        // dd($arr1);
        // $arr2=[4, 9, 12, 23, 26, 34, 2];
        // $commonElements = array_intersect($arr1,  $arr2);
        //                 dd($commonElements);

        $getLastResults = $this->mr->getRepository(LOTO_results::class)->findOneBy([], ['drawdate' => 'DESC']);
        $drawId = $getLastResults->getdrawid();

        $getgridsSelectedInThisDraw = $this->mr->getRepository(loto::class)->getUsersIdWhoPlayesLotoInThisDraw($drawId);
        //   dd($getgridsSelectedInThisDraw);

        $winningBalls[] = $getLastResults->getnumbers();
        $winningBallsExplode[] = explode(",", $winningBalls[0]);
        // dd($winningBallsExplode);


        foreach ($getgridsSelectedInThisDraw as $getgridsSelectedInThisDraw) {
            $winnerPrize = 0;
            // $orderId = $getgridsSelectedInThisDraw->getOrderId()->getId();
            // $PlayerInfo = $this->mr->getRepository(order::class)->getPlayerInfo($orderId);
            $lotoGridsForSelect = $this->mr->getRepository(loto::class)->findOrdersIds($getgridsSelectedInThisDraw['suyoolUserId'], $drawId);
            // dd($lotoGridsForSelect);
            foreach ($lotoGridsForSelect as $lotoGridsForSelect) {
                // dd($lotoGridsForSelect);
                $gridSelected = $lotoGridsForSelect->getgridSelected();
                $grids = explode("|", $gridSelected);
                foreach ($grids as $Selectedgrids) {
                    $count = 0;
                    $SelectedgridsExplode = [];
                    // dd($Selectedgrids);
                    $SelectedgridsExplode[] = explode(" ", $Selectedgrids);
                    // dd($SelectedgridsExplode);
                    // dd($winningBallsExplode);

                    // $Selectedgrids=str_replace(' ',',',$Selectedgrids);
                    // var_dump($Selectedgrids[0]);
                    // var_dump($winningBalls[0]);
                    // $info[]=['orderId'=>$orderId,'grid'=>$Selectedgrids];

                    $commonElements = array_intersect($winningBallsExplode[0],  $SelectedgridsExplode[0]);
                    // var_dump( $commonElements);
                    $count = count($commonElements);
                    if ($count == 6) {
                        if (in_array($winningBallsExplode[0][6], $commonElements)) {
                            $count = 7;
                        }
                    }
                    // echo $orderId;
                    // dd($count);
                    switch ($count) {
                        case 3:
                            $winnerPrize += $getLastResults->getwinner5();
                            break;
                        case 4:
                            $winnerPrize += $getLastResults->getwinner4();
                            break;
                        case 5:
                            $winnerPrize += $getLastResults->getwinner3();
                            break;
                        case 6:
                            $winnerPrize += $getLastResults->getwinner1();
                            break;
                        case 7:
                            $winnerPrize += $getLastResults->getwinner2();
                            break;
                        default:
                            
                    }
                }
                // dd($winnerPrize);
                
            }
            if($winnerPrize != 0){
                $params=json_encode(['currency'=>'LBP','amount'=>number_format($winnerPrize),'number'=>$drawId]);
                $content=$this->notificationServices->getContent('won loto added to suyool wallet');
                $this->notificationServices->addNotification($getgridsSelectedInThisDraw['suyoolUserId'],$content,$params,0);
            }
           
        }
        
        // dd($winnerPrize);
        //   dd($count);

        return 1;
    }
}
