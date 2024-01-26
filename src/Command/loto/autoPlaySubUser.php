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
use App\Service\SuyoolServices;
use DateInterval;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class autoPlaySubUser extends Command
{
    private $mr;
    private $lotoServices;
    private $sendEmail;
    private $notificationServices;
    private $notifyMr;
    private $params;
    private $logger;

    public function __construct(ManagerRegistry $mr, LotoServices $lotoServices, sendEmail $sendEmail, NotificationServices $notificationServices,ParameterBagInterface $params,LoggerInterface $logger)
    {
        parent::__construct();

        $this->lotoServices = $lotoServices;
        $this->mr = $mr->getManager('loto');
        $this->sendEmail = $sendEmail;
        $this->notificationServices = $notificationServices;
        $this->notifyMr = $mr->getManager('notification');
        $this->params=$params;
        $this->logger=$logger;
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
        $suyoolServices = new SuyoolServices($this->params->get('LOTO_MERCHANT_ID'),null,null,null,$logger);
        $drawnumber = $this->mr->getRepository(LOTO_draw::class)->findOneBy([],['drawdate'=>'DESC']);
        $gridsToPlay = $this->mr->getRepository(subscription::class)->getGridsToPlay();
        // dd($gridsToPlay);
        foreach($gridsToPlay as $gridsToPlay){
            $price = 0;
            $orders = $this->mr->getRepository(order::class)->findOneBy(['suyoolUserId'=>$gridsToPlay->getsuyoolUserId(),'status'=>'pending']);
            if(is_null($orders)){
                $orders = new order;
                $orders->setsuyoolUserId($gridsToPlay->getsuyoolUserId())
                ->setMobileNo($gridsToPlay->getMobileNo())
                ->setstatus($orders::$statusOrder['PENDING']);

                $this->mr->persist($orders);
                $this->mr->flush();

                $grid = explode("|",$gridsToPlay->getgridSelected());
                if($gridsToPlay->getIsBouquet()){
                    $gridArrayBouquet = explode("B",$grid[0]);
                    $priceFromDb = $this->mr->getRepository(LOTO_numbers::class)->findOneBy(['numbers'=>6]);
                    $price = $priceFromDb->getprice() * $gridArrayBouquet[1];
                    if($gridsToPlay->getIsZeed()){
                        $price += $priceFromDb->getzeed(); 
                    }
                }else{
                    foreach($grid as $grid){
                        $total = $price;
                        $gridArray = explode(" ",$grid);
                        $priceFromDb = $this->mr->getRepository(LOTO_numbers::class)->findOneBy(['numbers'=>count($gridArray)]);
                        $price = $priceFromDb->getprice();
                        $price += $total;
                        if($gridsToPlay->getIsZeed()){
                            $price += $priceFromDb->getzeed(); 
                        }
                    }
                }
                $loto = new loto;
                $loto->setOrderId($orders)
                ->setdrawnumber($drawnumber->getdrawid())
                ->setWithZeed($gridsToPlay->getIsZeed())
                ->setbouquet($gridsToPlay->getIsBouquet())
                ->setnumdraws(1)
                ->setgridSelected($gridsToPlay->getgridSelected())
                ->setprice($price)
                ->setcurrency("LBP");

                $this->mr->persist($loto);
                $this->mr->flush();
                // echo $price . "\n";
                // echo $gridsToPlay->getgridSelected() . "\n";
            }else{
                $grid = explode("|",$gridsToPlay->getgridSelected());
                if($gridsToPlay->getIsBouquet()){
                    $gridArrayBouquet = explode("B",$grid[0]);
                    $priceFromDb = $this->mr->getRepository(LOTO_numbers::class)->findOneBy(['numbers'=>6]);
                    $price = $priceFromDb->getprice() * $gridArrayBouquet[1];
                    if($gridsToPlay->getIsZeed()){
                        $price += $priceFromDb->getzeed(); 
                    }
                }else{
                    foreach($grid as $grid){
                        $total = $price;
                        $gridArray = explode(" ",$grid);
                        $priceFromDb = $this->mr->getRepository(LOTO_numbers::class)->findOneBy(['numbers'=>count($gridArray)]);
                        $price = $priceFromDb->getprice();
                        $price += $total;
                        if($gridsToPlay->getIsZeed()){
                            $price += $priceFromDb->getzeed(); 
                        }
                    }
                }
                $loto = new loto;
                $loto->setOrderId($orders)
                ->setdrawnumber($drawnumber->getdrawid())
                ->setWithZeed($gridsToPlay->getIsZeed())
                ->setnumdraws(1)
                ->setbouquet($gridsToPlay->getIsBouquet())
                ->setgridSelected($gridsToPlay->getgridSelected())
                ->setprice($price)
                ->setcurrency("LBP");

                $this->mr->persist($loto);
                $this->mr->flush();
            }
        }
        $orders->set
        

        return 1;
    }
}
