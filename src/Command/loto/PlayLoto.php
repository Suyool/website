<?php

namespace App\Command\loto;

use App\Entity\Loto\loto;
use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\order;
use App\Entity\Notification\content;
use App\Entity\Notification\Template;
use App\Service\LotoServices;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;

class PlayLoto extends Command
{
    private $mr;
    private $lotoServices;
    private $suyoolServices;
    private $certificate;
    private $hash_algo;
    private $notificationService;
    private $notifyMr;
    private $factory;
    private $logger;

    public function __construct(ManagerRegistry $mr, LotoServices $lotoServices, SuyoolServices $suyoolServices, $certificate, $hash_algo, NotificationServices $notificationService, LockFactory $lockFactory, LoggerInterface $logger)
    {
        parent::__construct();

        $this->mr = $mr->getManager('loto');
        $this->suyoolServices = $suyoolServices;
        $this->lotoServices = $lotoServices;
        $this->certificate = $certificate;
        $this->hash_algo = $hash_algo;
        $this->notificationService = $notificationService;
        $this->notifyMr = $mr->getManager('notification');
        $this->factory = $lockFactory;
        $this->logger = $logger;
    }

    protected function configure()
    {
        //php bin/console 
        $this
            ->setName('app:play');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $lock = $this->factory->createLock('loto_play_command');

        if (!$lock->acquire()) {
            $output->writeln('Another instance of the command is already running.');
            return 0;
        }

        $output->writeln([
            'Successfully Playing Loto'
        ]);

        $play  = 1;
        $newsum = 0;
        $drawNumber = 0;
        $bulk = 0; // 0 for unicast
        while ($play) {
            set_time_limit(0);
            $purchaseOrder=[];
            $purchaseOrder = $this->mr->getRepository(loto::class)->CheckPurchasedStatus();
            // dd($purchaseOrder);
            foreach ($purchaseOrder as $purchaseOrder) {
                $this->mr->clear();
                $additionalDataArray = [];
                $GetPurchasedOrder = $this->mr->getRepository(order::class)->findOneBy(['id' => $purchaseOrder['orderId']]);
                $ticketDataArray = [];
                foreach ($purchaseOrder['additionalData'] as $addData) {
                    if($addData['ticketId']!=0){
                        $ticketDataArray[] = $addData;
                        if ($addData['withZeed'] && $addData['bouquet']) {
                            $gridsBouquetAsString=count(explode("|",$addData['grids']));
                            $content = $this->notificationService->getContent('bouquet with zeed');
                            $params = json_encode(['draw' =>  $purchaseOrder['drawNumber'], 'bouquetgrids' => $gridsBouquetAsString, 'result' => $purchaseOrder['result'], 'ticket' => $addData['ticketId'], 'zeed' => $addData['zeed']], true);
                            $this->notificationService->addNotification($purchaseOrder['userId'], $content, $params, $bulk, "https://www.suyool.com/loto?goto=Result");
                        } else if (!$addData['withZeed'] && $addData['bouquet']) {
                            $gridsBouquetAsString=count(explode("|",$addData['grids']));
                            $content = $this->notificationService->getContent('bouquet without zeed');
                            $params = json_encode(['draw' => $purchaseOrder['drawNumber'], 'bouquetgrids' => $gridsBouquetAsString, 'result' => $purchaseOrder['result'], 'ticket' => $addData['ticketId']], true);
                            $this->notificationService->addNotification($purchaseOrder['userId'], $content, $params, $bulk, "https://www.suyool.com/loto?goto=Result");
                        } else if ($addData['withZeed'] && !$addData['bouquet']) {
                            $gridsToBeMergedd[] = explode("|", $addData['grids']);

                            $gridss = array_merge(...$gridsToBeMergedd);
                            $gridsAsStrings = implode(" \n", $gridss);
                            $content = $this->notificationService->getContent('with zeed & without bouquet');
                            $params = json_encode(['draw' => $purchaseOrder['drawNumber'], 'grids' => $gridsAsStrings, 'result' => $purchaseOrder['result'], 'ticket' => $addData['ticketId'], 'zeed' => $addData['zeed']], true);
                            $this->notificationService->addNotification($purchaseOrder['userId'], $content, $params, $bulk, "https://www.suyool.com/loto?goto=Result");
                        } else if (!$addData['withZeed'] && !$addData['bouquet']) {
                            $gridsToBeMergedd[] = explode("|", $addData['grids']);
                            $gridss = array_merge(...$gridsToBeMergedd);
                            $gridsAsStrings = implode(" \n", $gridss);
                            $content = $this->notificationService->getContent('without zeed & without bouquet');
                            $params = json_encode(['draw' => $purchaseOrder['drawNumber'], 'grids' => $gridsAsStrings, 'result' => $purchaseOrder['result'], 'ticket' => $addData['ticketId']], true);
                            $this->notificationService->addNotification($purchaseOrder['userId'], $content, $params, $bulk, "https://www.suyool.com/loto?goto=Result");
                        }
                    }
                }
                if($purchaseOrder['TotalPrice'] > 0){
                    $additionalDataArray[] = $ticketDataArray;
                    $ticket = count($ticketDataArray);
                    $additionalDataArray[] = ['count' => $ticket];
                }
                $additionalData = json_encode($additionalDataArray, true);
                $updateutility = $this->suyoolServices->UpdateUtilities($purchaseOrder['TotalPrice'], $additionalData, $purchaseOrder['transId']);
                if ($updateutility[0]) {
                    $GetPurchasedOrder->setamount($purchaseOrder['TotalPrice'])
                        ->setcurrency("LBP")
                        ->setstatus(order::$statusOrder['COMPLETED']);
                    if ($purchaseOrder['TotalPrice'] != $purchaseOrder['OrderAmount']) {
                        $diff = $purchaseOrder['OrderAmount'] - $purchaseOrder['TotalPrice'];
                        $params = json_encode(['currency' => "L.L", 'amount' => $diff, 'draw' => $purchaseOrder['drawNumber']], true);
                        $content = $this->notificationService->getContent('Payment reversed loto');
                        $this->notificationService->addNotification($purchaseOrder['userId'], $content, $params, $bulk);
                    }
                } else {
                        $GetPurchasedOrder->setstatus(order::$statusOrder['CANCELED']);
                        $GetPurchasedOrder->seterror($updateutility[1]);
                }
                $this->mr->persist($GetPurchasedOrder);
                $this->mr->flush();
            }
            $heldOrder = $this->mr->getRepository(order::class)->findBy(['status' => order::$statusOrder['HELD']], null, 1);
            if ($heldOrder == null) {
                $play = 1;
                sleep(10);
                continue;
            }
            foreach ($heldOrder as $held) {
                $held->setstatus(order::$statusOrder['PURCHASED']);
                $this->mr->persist($held);
                $this->mr->flush();
                $userId = $held->getsuyoolUserId();
                $sum = $held->getamount();
                $lotoToBePlayed = $this->mr->getRepository(loto::class)->lotoToBePlayed($held->getId());
                $additionaldata = [];
                $newElement = [];
                $grids = [];
                $gridsBouquet = [];
                $ticketscount = 0;
                $newsum = 0;
                foreach ($lotoToBePlayed as $lotoToBePlayed) {
                    $gridsToBeMerged = [];
                    $gridsBouquetToBeMerged = [];
                    $ticketscount++;
                    $newElement = [];
                    $submit = $this->lotoServices->playLoto($lotoToBePlayed->getdrawnumber(), $lotoToBePlayed->getwithZeed(), $lotoToBePlayed->getgridSelected(), $lotoToBePlayed->getnumdraws(),SuyoolServices::aesDecryptString($held->getMobileNo()));
                    if ($lotoToBePlayed->getbouquet()) {
                        if ($submit[0]) {
                            sleep(2);
                            $ticketId = $this->lotoServices->GetTicketId();
                            sleep(2);
                            $BouquetGrids = $this->lotoServices->BouquetGrids($ticketId);
                            $lotoToBePlayed->setticketId($ticketId);
                            if ($submit[1] != null || $submit[1] != "") {
                                $lotoToBePlayed->setzeednumber(str_pad($submit[1], 5, "0", STR_PAD_LEFT));
                            }
                            $lotoToBePlayed->setgridSelected($BouquetGrids);

                            $this->mr->persist($lotoToBePlayed);
                            $this->mr->flush();

                            $gridsBouquetToBeMerged[] = explode("|", $BouquetGrids);
                            $gridsBouquet = array_merge(...$gridsBouquetToBeMerged);
                            $gridsBouquetAsString = sizeof($gridsBouquet);
                            $draw = $this->mr->getRepository(LOTO_draw::class)->findOneBy(['drawId' => $lotoToBePlayed->getdrawnumber()]);
                            $drawNumber = $lotoToBePlayed->getdrawnumber();
                            $result = $draw->getdrawdate()->format('d/m/Y');
                            sleep(1);
                            $retryCountToCatchTheError = 3;

                            while ($retryCountToCatchTheError > 0) {
                                try {
                                    if ($lotoToBePlayed->getwithZeed() && $lotoToBePlayed->getbouquet()) {
                                        $content = $this->notificationService->getContent('bouquet with zeed');
                                        $params = json_encode(['draw' => $lotoToBePlayed->getdrawnumber(), 'bouquetgrids' => $gridsBouquetAsString, 'result' => $result, 'ticket' => $ticketId, 'zeed' => $lotoToBePlayed->getzeednumber()], true);
                                        $this->notificationService->addNotification($userId, $content, $params, $bulk, "https://www.suyool.com/loto?goto=Result");
                                        $newElement = ['ticketId' => $ticketId, 'zeed' => $lotoToBePlayed->getwithZeed(), 'bouquet' => $lotoToBePlayed->getbouquet()];
                                    } else if (!$lotoToBePlayed->getwithZeed() && $lotoToBePlayed->getbouquet()) {
                                        $content = $this->notificationService->getContent('bouquet without zeed');
                                        $params = json_encode(['draw' => $lotoToBePlayed->getdrawnumber(), 'bouquetgrids' => $gridsBouquetAsString, 'result' => $result, 'ticket' => $ticketId], true);
                                        $this->notificationService->addNotification($userId, $content, $params, $bulk, "https://www.suyool.com/loto?goto=Result");
                                        $newElement = ['ticketId' => $ticketId, 'bouquet' => $lotoToBePlayed->getbouquet()];
                                    }
                                    break;
                                } catch (Exception $e) {
                                    $this->logger->error($e->getMessage());
                                    $retryCountToCatchTheError--;
                                    sleep(1);
                                }
                            }



                            $drawnumber = $lotoToBePlayed->getdrawnumber();
                            if ($lotoToBePlayed->getnumdraws() > 1) {
                                for ($x = 1; $x < $lotoToBePlayed->getnumdraws(); $x++) {
                                    $drawnumber++;
                                    $loto = new loto;
                                    $loto->setticketId($ticketId)
                                        ->setOrderId($held)
                                        ->setdrawnumber($drawnumber)
                                        ->setnumdraws($lotoToBePlayed->getnumdraws())
                                        ->setWithZeed($lotoToBePlayed->getwithZeed())
                                        ->setbouquet($lotoToBePlayed->getbouquet())
                                        ->setgridSelected($BouquetGrids)
                                        ->setzeednumber($lotoToBePlayed->getzeednumber())
                                        ->setprice($lotoToBePlayed->getprice())
                                        ->setcurrency($lotoToBePlayed->getcurrency());
                                    $this->mr->persist($loto);
                                }
                                $this->mr->flush();
                            }
                        } else {
                            $errorInfo = ['errorCode' => $submit[1], 'errorMsg' => $submit[2]];
                            $errorInfo = json_encode($errorInfo, true);
                            $lotoToBePlayed->seterror($errorInfo);
                            $this->mr->persist($lotoToBePlayed);
                            $this->mr->flush();
                        }
                    } else {
                        if ($submit[0]) {
                            sleep(2);
                            $ticketId = $this->lotoServices->GetTicketId();
                            $lotoToBePlayed->setticketId($ticketId);
                            if ($submit[1] != null || $submit[1] != "") {
                                $lotoToBePlayed->setzeednumber(str_pad($submit[1], 5, "0", STR_PAD_LEFT));
                            }

                            $this->mr->persist($lotoToBePlayed);
                            $this->mr->flush();
                            sleep(1);
                            $gridsToBeMerged[] = explode("|", $lotoToBePlayed->getgridSelected());

                            $grids = array_merge(...$gridsToBeMerged);
                            $gridsAsString = implode(" \n", $grids);
                            $draw = $this->mr->getRepository(LOTO_draw::class)->findOneBy(['drawId' => $lotoToBePlayed->getdrawnumber()]);
                            $drawNumber = $lotoToBePlayed->getdrawnumber();
                            $result = $draw->getdrawdate()->format('d/m/Y');
                            sleep(1);
                            $retryCountToCatchTheError = 3;

                            while ($retryCountToCatchTheError > 0) {
                                try {
                                    if ($lotoToBePlayed->getwithZeed() && !$lotoToBePlayed->getbouquet()) {
                                        $content = $this->notificationService->getContent('with zeed & without bouquet');
                                        $params = json_encode(['draw' => $lotoToBePlayed->getdrawnumber(), 'grids' => $gridsAsString, 'result' => $result, 'ticket' => $ticketId, 'zeed' => $lotoToBePlayed->getzeednumber()], true);
                                        $this->notificationService->addNotification($userId, $content, $params, $bulk, "https://www.suyool.com/loto?goto=Result");
                                        $newElement = ['ticketId' => $ticketId, 'zeed' => $lotoToBePlayed->getwithZeed()];
                                    } else if (!$lotoToBePlayed->getwithZeed() && !$lotoToBePlayed->getbouquet()) {
                                        $content = $this->notificationService->getContent('without zeed & without bouquet');
                                        $params = json_encode(['draw' => $lotoToBePlayed->getdrawnumber(), 'grids' => $gridsAsString, 'result' => $result, 'ticket' => $ticketId], true);
                                        $this->notificationService->addNotification($userId, $content, $params, $bulk, "https://www.suyool.com/loto?goto=Result");
                                        $newElement = ['ticketId' => $ticketId];
                                    }
                                    break;
                                } catch (Exception $e) {
                                    $this->logger->error($e->getMessage());
                                    $retryCountToCatchTheError--;
                                    sleep(1);
                                }
                            }


                            $drawnumber = $lotoToBePlayed->getdrawnumber();
                            if ($lotoToBePlayed->getnumdraws() > 1) {
                                for ($x = 1; $x < $lotoToBePlayed->getnumdraws(); $x++) {
                                    $drawnumber++;
                                    $loto = new loto;
                                    $loto->setticketId($ticketId)
                                        ->setOrderId($held)
                                        ->setdrawnumber($drawnumber)
                                        ->setnumdraws($lotoToBePlayed->getnumdraws())
                                        ->setWithZeed($lotoToBePlayed->getwithZeed())
                                        ->setbouquet($lotoToBePlayed->getbouquet())
                                        ->setgridSelected($lotoToBePlayed->getgridSelected())
                                        ->setzeednumber($lotoToBePlayed->getzeednumber())
                                        ->setprice($lotoToBePlayed->getprice())
                                        ->setcurrency($lotoToBePlayed->getcurrency());
                                    $this->mr->persist($loto);
                                }
                                $this->mr->flush();
                            }
                        } else {
                            $errorInfo = ['errorCode' => $submit[1], 'errorMsg' => $submit[2]];
                            $errorInfo = json_encode($errorInfo, true);
                            $lotoToBePlayed->seterror($errorInfo);
                            $this->mr->persist($lotoToBePlayed);
                            $this->mr->flush();
                        }
                    }
                    if ($newElement != null && !empty($newElement)) {
                        $additionaldata[] = [$newElement];
                    }
                }
                $count['count'] = $ticketscount;
                $additionaldata[] = $count;
                $additionalData = json_encode($additionaldata, true);
                // echo $additionalData;
                $lotoidcompleted = $this->mr->getRepository(loto::class)->completed($held->getId());

                foreach ($lotoidcompleted as $lotoidcompletedsum) {
                    $newsum += $lotoidcompletedsum->getprice();
                }

                $updateutility = $this->suyoolServices->UpdateUtilities($newsum, $additionalData, $held->gettransId());

                if ($updateutility[0]) {
                    $held->setamount($newsum)
                        ->setcurrency("LBP")
                        ->setstatus(order::$statusOrder['COMPLETED']);

                    $this->mr->persist($held);
                    $this->mr->flush();
                    if ($newsum != $sum) {
                        $diff = $sum - $newsum;
                        $params = json_encode(['currency' => "L.L", 'amount' => $diff, 'draw' => $drawNumber], true);
                        $content = $this->notificationService->getContent('Payment reversed loto');
                        $this->notificationService->addNotification($userId, $content, $params, $bulk);
                    }
                } else {
                    $held->setstatus(order::$statusOrder['CANCELED']);
                    $held->seterror($updateutility[1]);

                    $this->mr->persist($held);
                    $this->mr->flush();
                }
            }
        }
        $lock->release();
        return 1;
    }
}
