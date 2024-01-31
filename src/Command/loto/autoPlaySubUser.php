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

    public function __construct(ManagerRegistry $mr, LotoServices $lotoServices, sendEmail $sendEmail, NotificationServices $notificationServices, ParameterBagInterface $params, LoggerInterface $logger)
    {
        parent::__construct();

        $this->lotoServices = $lotoServices;
        $this->mr = $mr->getManager('loto');
        $this->sendEmail = $sendEmail;
        $this->notificationServices = $notificationServices;
        $this->notifyMr = $mr->getManager('notification');
        $this->params = $params;
        $this->logger = $logger;
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
        $gridsToPlayFetch = [];
        $suyoolServices = new SuyoolServices($this->params->get('LOTO_MERCHANT_ID'), null, null, null, $this->logger);
        $drawnumber = $this->mr->getRepository(LOTO_draw::class)->findOneBy([], ['drawdate' => 'DESC']);
        $gridsToPlayAll = $this->mr->getRepository(subscription::class)->getGridsToPlay();
        foreach ($gridsToPlayAll as $gridsToPlayAll) {
            $gridsToPlay = $this->mr->getRepository(subscription::class)->getGridsToPlayPerUser($drawnumber->getdrawid(), $gridsToPlayAll->getSuyoolUserId());
            // dd($gridsToPlay);
            $gridsToPlayFetch = array_merge($gridsToPlayFetch, $gridsToPlay);
        }
        $gridsToPlayFetch = array_unique(array_map(function ($grid) {
            return $grid->getId();
        }, $gridsToPlayFetch));
        $uniqueGrids = $this->mr->getRepository(subscription::class)->findBy(['id' => $gridsToPlayFetch]);
        $sumByUserId = [];

        foreach ($uniqueGrids as $subscription) {
            $suyoolUserId = $subscription->getSuyoolUserId();
            $numGrids = $subscription->getNumGrids();

            if (!isset($sumByUserId[$suyoolUserId])) {
                $sumByUserId[$suyoolUserId] = $numGrids;
            } else {
                $sumByUserId[$suyoolUserId] += $numGrids;
            }
        }
        foreach ($uniqueGrids as $gridsToPlay) {
            $price = 0;
            $orders = $this->mr->getRepository(order::class)->findOneBy(['suyoolUserId' => $gridsToPlay->getsuyoolUserId(), 'status' => 'pending']);
            if (is_null($orders)) {
                $orders = new order;
                $orders->setsuyoolUserId($gridsToPlay->getsuyoolUserId())
                    ->setMobileNo($gridsToPlay->getMobileNo())
                    ->setstatus($orders::$statusOrder['PENDING'])
                    ->setFromSub(true);

                $this->mr->persist($orders);
                $this->mr->flush();

                $grid = explode("|", $gridsToPlay->getgridSelected());
                if (strpos($gridsToPlay->getgridSelected(), 'B') !== 0) {
                    $gridsToCount[] = explode("|", $gridsToPlay->getgridSelected());
                }
                if ($gridsToPlay->getIsBouquet()) {
                    $gridArrayBouquet = explode("B", $grid[0]);
                    $priceFromDb = $this->mr->getRepository(LOTO_numbers::class)->findOneBy(['numbers' => 6]);
                    $price = $priceFromDb->getprice() * $gridArrayBouquet[1];
                    if ($gridsToPlay->getIsZeed()) {
                        $price += $priceFromDb->getzeed();
                    }
                } else {
                    foreach ($grid as $grid) {
                        $total = $price;
                        $gridArray = explode(" ", $grid);
                        $priceFromDb = $this->mr->getRepository(LOTO_numbers::class)->findOneBy(['numbers' => count($gridArray)]);
                        $price = $priceFromDb->getprice();
                        $price += $total;
                        if ($gridsToPlay->getIsZeed()) {
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
            } else {
                $grid = explode("|", $gridsToPlay->getgridSelected());
                if ($gridsToPlay->getIsBouquet()) {
                    $gridArrayBouquet = explode("B", $grid[0]);
                    $priceFromDb = $this->mr->getRepository(LOTO_numbers::class)->findOneBy(['numbers' => 6]);
                    $price = $priceFromDb->getprice() * $gridArrayBouquet[1];
                    if ($gridsToPlay->getIsZeed()) {
                        $price += $priceFromDb->getzeed();
                    }
                } else {
                    foreach ($grid as $grid) {
                        $total = $price;
                        $gridArray = explode(" ", $grid);
                        $priceFromDb = $this->mr->getRepository(LOTO_numbers::class)->findOneBy(['numbers' => count($gridArray)]);
                        $price = $priceFromDb->getprice();
                        $price += $total;
                        if ($gridsToPlay->getIsZeed()) {
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
        $orders = $this->mr->getRepository(loto::class)->getOrdersFromSubscripyionPerUser();
        foreach ($orders as $orders) {
            $orders[0]->setamount($orders['totalAmount'])
                ->setcurrency("LBP");

            $this->mr->persist($orders[0]);
            $this->mr->flush();
            $pushutility = $suyoolServices->PushUtilities($orders[0]->getsuyoolUserId(), $this->params->get('LOTO_MERCHANT_ID') . "-" . $orders[0]->getId(), $orders['totalAmount'], "LBP", 0);
            if ($pushutility[0]) {
                $orders[0]
                    ->settransId($pushutility[1])
                    ->setstatus(order::$statusOrder['HELD'])
                    ->setsubscription(1);
                $this->mr->persist($orders[0]);
                $this->mr->flush();
                foreach ($uniqueGrids as $gridsToPlay) {
                    if ($gridsToPlay->getsuyoolUserId() == $orders[0]->getsuyoolUserId()) {
                        $remaining = $gridsToPlay->getRemaining();
                        $gridsToPlay->setRemaining($remaining - 1);
                        $this->mr->persist($gridsToPlay);
                        $this->mr->flush();
                    }
                }
                $content = $this->notificationServices->getContent('Payment taken loto');
                $params = json_encode(['amount' => $orders['totalAmount'], 'currency' => "L.L", 'numgrids' => $sumByUserId[$orders[0]->getsuyoolUserId()]], true);
                $bulk = 0;
                $this->notificationServices->addNotification($orders[0]->getsuyoolUserId(), $content, $params, $bulk);
            } else {
                $orders[0]
                    ->setstatus(order::$statusOrder['CANCELED'])
                    ->seterror($pushutility[3]);
                $this->mr->persist($orders[0]);
                $this->mr->flush();
                echo "Cannot play " . $orders[0]->getsuyoolUserId() . " for the amount of " . $orders['totalAmount'];
            }
        }
        return 1;
    }
}
