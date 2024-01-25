<?php

namespace App\Command\loto;

use App\Entity\Loto\loto;
use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_numbers;
use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\order;
use App\Entity\Loto\subscription;
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

class autoPlaySubUser extends Command
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
        $this
            ->setName('app:subscription');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Auto Play for subscribers'
        ]);
        $gridsToPlay = $this->mr->getRepository(subscription::class)->getGridsToPlay();
        // dd($gridsToPlay);
        foreach($gridsToPlay as $gridsToPlay){
            $orders = $this->mr->getRepository(order::class)->findOneBy(['suyoolUserId'=>$gridsToPlay->getsuyoolUserId(),'status'=>'pending']);
            if(is_null($orders)){
                $orders = new order;
                $orders->setsuyoolUserId($gridsToPlay->getsuyoolUserId())
                ->setMobileNo($gridsToPlay->getMobileNo())
                ->setstatus($orders::$statusOrder['PENDING']);
                $grid = explode("|",$gridsToPlay->getgridSelected());
                if($gridsToPlay->getIsBouquet()){
                    $gridArrayBouquet = explode("B",$grid[0]);
                    dd($gridArrayBouquet[1]);
                }else{
                    $gridArray = explode(" ",$grid[0]);
                    dd(count($gridArray));
                }
                
            }else{
                
            }
        }
        

        return 1;
    }
}
