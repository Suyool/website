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
use App\Service\SuyoolServices;
use DateInterval;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
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
    private $suyoolServices;
    private $logger;
    public function __construct(ManagerRegistry $mr, LotoServices $lotoServices, sendEmail $sendEmail, NotificationServices $notificationServices,SuyoolServices $suyoolServices,LoggerInterface $logger)
    {
        parent::__construct();

        $this->lotoServices = $lotoServices;
        $this->mr = $mr->getManager('loto');
        $this->sendEmail = $sendEmail;
        $this->notificationServices = $notificationServices;
        $this->notifyMr = $mr->getManager('notification');
        $this->suyoolServices=$suyoolServices;
        $this->logger=$logger;
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
            'Winning details send'
        ]);

        $listWinners=[];
        

        $getLastResults = $this->mr->getRepository(LOTO_results::class)->findOneBy([], ['drawdate' => 'DESC']);
        $drawId = $getLastResults->getdrawid();

        $winningBalls[] = $getLastResults->getnumbers();
        $winningBallsExplode[] = explode(",", $winningBalls[0]);

        $winningBallsZeed['prize1'] = $getLastResults->getzeednumber1();
        $winningBallsZeed['prize2']=$getLastResults->getzeednumber2();
        $winningBallsZeed['prize3']=$getLastResults->getzeednumber3();
        $winningBallsZeed['prize4']=$getLastResults->getzeednumber4();

        $getGridsinThisDraw = $this->mr->getRepository(loto::class)->findBy(['drawNumber'=>$drawId]);
        foreach($getGridsinThisDraw as $gridsTobeUpdated){
            $sum=0;
            $keyInArray1=-1;
            $result=[];
            $won=null;
                $gridSelected = $gridsTobeUpdated->getgridSelected();
                $zeednumbers = $gridsTobeUpdated->getzeednumber();

                for ($i = 0; $i < strlen($zeednumbers); $i++) {
                    $result[] = substr($zeednumbers, $i);
                }

                foreach($winningBallsZeed as $winningBallsZeeds){
                    if(in_array($winningBallsZeeds,$result)){
                        $keyInArray1 = array_search($winningBallsZeeds, $result);
                        break;
                    }
                }
                if($keyInArray1 == 0){
                    $prizezeed=1;
                    $gridsTobeUpdated->setwinzeed($getLastResults->getwinner1zeed());
                }else if($keyInArray1 == 1){
                    $prizezeed=2;
                    $gridsTobeUpdated->setwinzeed($getLastResults->getwinner2zeed());
                }else if($keyInArray1 == 2){
                    $prizezeed=3;
                    $gridsTobeUpdated->setwinzeed($getLastResults->getwinner3zeed());
                }else if($keyInArray1 == 3){
                    $prizezeed=4;
                    $gridsTobeUpdated->setwinzeed($getLastResults->getwinner4zeed());
                }else{
                    $prizezeed=null;
                }
                $grids = explode("|", $gridSelected);
                foreach ($grids as $Selectedgrids) {
                    $count = 0;
                    $SelectedgridsExplode = [];
                    $SelectedgridsExplode[] = explode(" ", $Selectedgrids);

                    $commonElements = array_intersect($winningBallsExplode[0],  $SelectedgridsExplode[0]);
                    $count = count($commonElements);
                    if ($count >= 6) {
                        if (in_array($winningBallsExplode[0][6], $commonElements) && !in_array($winningBallsExplode[0][5], $commonElements)) {
                            $count=7;
                        }else{
                            $count=6;
                        }
                    }
                    if($count==3){
                        $won=5;
                        $sum+=$getLastResults->getwinner5();
                        // $gridsTobeUpdated->setwinloto($sum);
                    }else if($count==4){
                        $won=4;
                        $sum+=$getLastResults->getwinner4();
                    }else if($count==5){
                        $won=3;
                        $sum+=$getLastResults->getwinner3();
                    }else if($count==7){
                        $won=2;
                        $sum+=$getLastResults->getwinner2();
                    }else if($count==6){
                        $won=1;
                        $sum+=$getLastResults->getwinner1();
                    }else{
                        $won=null;
                    }
                }
                // echo json_encode($won);
                $gridsTobeUpdated->setwinloto($sum);
                $gridsTobeUpdated->setwonloto($won);
                $gridsTobeUpdated->setwonzeed($prizezeed);
                $this->mr->persist($gridsTobeUpdated);
                $this->mr->flush();
        }

        $getUsersWhoWon=$this->mr->getRepository(loto::class)->getUsersWhoWon($drawId);
        $this->logger->debug(json_encode($getUsersWhoWon));
        if(!empty($getUsersWhoWon)){
            foreach($getUsersWhoWon as $getUsersWhoWon){
                $Amount = 0;
                foreach($getUsersWhoWon['Amount'] as $amount){
                    $Amount += $amount;
                }
                    $orders=implode(",",$getUsersWhoWon['OrderID']);
                    $tickets=implode(",",$getUsersWhoWon['TicketID']);
                $listWinners[]=['UserAccountID'=>(int)$getUsersWhoWon['UserAccountID'],'Amount'=>(float)$Amount,'Currency'=>'LBP','OrderID'=>$orders,'TicketID'=>$tickets];
            }
    
    
            $response=$this->suyoolServices->PushUserPrize($listWinners);
            if($response[0]){
                $data=json_decode($response[1],true);
                $this->logger->debug(json_encode($data));

                foreach($data as $data){
                    $order=explode(",",$data['OrderID']);
    
                    if($data['FlagCode'] == 136){
                        foreach($order as $order){
                            $loto=$this->mr->getRepository(loto::class)->getWinTicketsWinStNull($order,$drawId);
                            foreach($loto as $loto){
                                $loto->setwinningStatus('pending');
                                $this->mr->persist($loto);
                                $this->mr->flush();
                            }
                        }
                        $params=json_encode(['currency'=>'L.L','amount'=>$data['Amount'],'number'=>$drawId]);
                        $content=$this->notificationServices->getContent('L1-ExceedMonthlyLimit');
                        $this->notificationServices->addNotification($data['UserAccountID'],$content,$params,0);
                    }else if($data['FlagCode'] == 135){
                        foreach($order as $order){
                            $loto=$this->mr->getRepository(loto::class)->getWinTicketsWinStNull($order,$drawId);
                            foreach($loto as $loto){
                                $loto->setwinningStatus('redirected');
                                $this->mr->persist($loto);
                                $this->mr->flush();
                            }
                            
                        }
                        $params=json_encode(['currency'=>'L.L','amount'=>$data['Amount'],'number'=>$drawId]);
                        $content=$this->notificationServices->getContent('ExceedLimitMoreThanTenThousandsUSD');
                        $this->notificationServices->addNotification($data['UserAccountID'],$content,$params,0);
                    }else if($data['FlagCode'] == 1){
                        foreach($order as $order){
                            $loto=$this->mr->getRepository(loto::class)->getWinTicketsWinStNull($order,$drawId);
                            foreach($loto as $loto){
                                $loto->setwinningStatus('paid');
                                $this->mr->persist($loto);
                                $this->mr->flush();
                            }
                        }
                        $params=json_encode(['currency'=>'L.L','amount'=>$data['Amount'],'number'=>$drawId]);
                        $content=$this->notificationServices->getContent('won loto added to suyool wallet');
                        $this->notificationServices->addNotification($data['UserAccountID'],$content,$params,0,"https://www.suyool.com/loto?goto=Result");
                    }
                }
            }
    
        }
        
        return 1;
    }
}
