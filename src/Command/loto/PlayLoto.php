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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PlayLoto extends Command
{
    private $mr;
    private $lotoServices;
    private $suyoolServices;
    private $certificate;
    private $hash_algo;
    private $notificationService;
    private $notifyMr;

    public function __construct(ManagerRegistry $mr, LotoServices $lotoServices, SuyoolServices $suyoolServices, $certificate, $hash_algo, NotificationServices $notificationService)
    {
        parent::__construct();

        $this->mr = $mr->getManager('loto');
        $this->suyoolServices = $suyoolServices;
        $this->lotoServices = $lotoServices;
        $this->certificate = $certificate;
        $this->hash_algo = $hash_algo;
        $this->notificationService = $notificationService;
        $this->notifyMr = $mr->getManager('notification');
    }

    protected function configure()
    {
        //php bin/console 
        $this
            ->setName('app:play');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Successfully Playing Loto'
        ]);

        $play  = 1;
        $newsum = 0;
        $drawNumber = 0;
        $bulk=0;// o for unicast
        while ($play) {
            // $output->writeln([
            //     'Successfully RePlaying Loto'
            // ]);
    
            $heldOrder = $this->mr->getRepository(order::class)->findBy(['status' => 'held']);
            if ($heldOrder == null) {
                $play=1;
                sleep(10);
                continue;
            }
            foreach ($heldOrder as $held) {
                $userId = $held->getsuyoolUserId();
                $sum = $held->getamount();
                $currency = $held->getcurrency();
                $lotoToBePlayed = $this->mr->getRepository(loto::class)->lotoToBePlayed($held->getId());
                $additionaldata = [];
                $newElement = [];
                $grids=[];
                $gridsBouquet=[];
                $ticketscount = 0;
                $newsum = 0;
                // dd($lotoToBePlayed);
                foreach ($lotoToBePlayed as $lotoToBePlayed) {
                    $ticketscount++;
                    $newElement = [];
                    $submit = $this->lotoServices->playLoto($lotoToBePlayed->getdrawnumber(), $lotoToBePlayed->getwithZeed(), $lotoToBePlayed->getgridSelected());
                    if ($lotoToBePlayed->getbouquet()) {
                        if ($submit[0]) {
                            sleep(1);
                            $ticketId = $this->lotoServices->GetTicketId();
                            sleep(1);
                            // $BouquetGrids = $this->lotoServices->BouquetGrids($ticketId);
                            $BouquetGrids = "1 2 3 4 5 6";
                            $lotoToBePlayed->setticketId($ticketId);
                            $lotoToBePlayed->setzeednumber($submit[1]);
                            $lotoToBePlayed->setgridSelected($BouquetGrids);

                            $this->mr->persist($lotoToBePlayed);
                            $this->mr->flush();

                            $gridsBouquet[] = explode("|", $BouquetGrids);
                            $gridsBouquet = array_merge(...$gridsBouquet);
                            $gridsBouquetAsString = sizeof($gridsBouquet);
                            $draw = $this->mr->getRepository(LOTO_draw::class)->findOneBy(['drawId' => $lotoToBePlayed->getdrawnumber()]);
                            $drawNumber = $lotoToBePlayed->getdrawnumber();
                            $result = $draw->getdrawdate()->format('d/m/Y');

                            if ($lotoToBePlayed->getwithZeed() && $lotoToBePlayed->getbouquet()) {
                                $content=$this->notificationService->getContent('bouquet with zeed');

                                $params = json_encode(['draw' => $lotoToBePlayed->getdrawnumber(), 'grids' => $gridsBouquetAsString, 'result' => $result, 'ticket' => $ticketId, 'zeed' => $lotoToBePlayed->getwithZeed()], true);
                                $this->notificationService->addNotification($userId, $content, $params,$bulk);
                                $newElement = ['ticketId' => $ticketId, 'zeed' => $lotoToBePlayed->getwithZeed(), 'bouquet' => $lotoToBePlayed->getbouquet()];
                            } else if (!$lotoToBePlayed->getwithZeed() && $lotoToBePlayed->getbouquet()) {
                                $content=$this->notificationService->getContent('bouquet without zeed');

                                $params = json_encode(['draw' => $lotoToBePlayed->getdrawnumber(), 'grids' => $gridsBouquetAsString, 'result' => $result, 'ticket' => $ticketId], true);
                                $this->notificationService->addNotification($userId, $content, $params,$bulk);
                                $newElement = ['ticketId' => $ticketId, 'bouquet' => $lotoToBePlayed->getbouquet()];
                            }
                            // $additionaldata[] = $newElement;
                        } else {
                            $errorInfo = ['errorCode' => $submit[1], 'errorMsg' => $submit[2]];
                            $errorInfo = json_encode($errorInfo, true);
                            $lotoToBePlayed->seterror($errorInfo);
                            $this->mr->persist($lotoToBePlayed);
                            $this->mr->flush();
                        }
                    } else {
                        if ($submit[0]) {
                            sleep(1);
                            $ticketId = $this->lotoServices->GetTicketId();
                            $lotoToBePlayed->setticketId($ticketId);
                            $lotoToBePlayed->setzeednumber($submit[1]);

                            $this->mr->persist($lotoToBePlayed);
                            $this->mr->flush();
                            sleep(1);
                            // dd($lotoToBePlayed->getgridSelected());
                            $grids[] = explode("|", $lotoToBePlayed->getgridSelected());
                            
                            $grids = array_merge(...$grids);
                            // var_dump( $grids);
                            $gridsAsString= implode(" \n",$grids);
                            // $grids = json_encode($grids, true);
                            $draw = $this->mr->getRepository(LOTO_draw::class)->findOneBy(['drawId' => $lotoToBePlayed->getdrawnumber()]);
                            $drawNumber = $lotoToBePlayed->getdrawnumber();
                            $result = $draw->getdrawdate()->format('d/m/Y');


                            // $ticketId = 522;
                            if ($lotoToBePlayed->getwithZeed() && !$lotoToBePlayed->getbouquet()) {
                                $content=$this->notificationService->getContent('with zeed & without bouquet');

                                $params = json_encode(['draw' => $lotoToBePlayed->getdrawnumber(), 'grids' => $gridsAsString, 'result' => $result, 'ticket' => $ticketId, 'zeed' => $lotoToBePlayed->getwithZeed()], true);
                                $this->notificationService->addNotification($userId, $content, $params,$bulk);
                                $newElement = ['ticketId' => $ticketId, 'zeed' => $lotoToBePlayed->getwithZeed()];
                            } else if (!$lotoToBePlayed->getwithZeed() && !$lotoToBePlayed->getbouquet()) {
                                $content=$this->notificationService->getContent('without zeed & without bouquet');

                                $params = json_encode(['draw' => $lotoToBePlayed->getdrawnumber(), 'grids' => $gridsAsString, 'result' => $result, 'ticket' => $ticketId], true);
                                $this->notificationService->addNotification($userId, $content, $params,$bulk);
                                $newElement = ['ticketId' => $ticketId];
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

                $held->setstatus("purchased");
                $this->mr->persist($held);
                $this->mr->flush();

                $lotoidcompleted = $this->mr->getRepository(loto::class)->completed($held->getId());


                foreach ($lotoidcompleted as $lotoidcompletedsum) {
                    $newsum += $lotoidcompletedsum->getprice();
                }

                $additionalData = json_encode($additionaldata, true);

                $updateutility = $this->suyoolServices->UpdateUtilities($newsum, $this->hash_algo, $this->certificate, $additionalData, $held->gettransId());
                // var_dump($additionaldata);
                echo $additionalData;
                if ($updateutility) {
                    $held->setamount($newsum)
                        ->setcurrency("LBP")
                        ->setstatus("completed");

                    $this->mr->persist($held);
                    $this->mr->flush();

                    if ($newsum != $sum) {
                        $diff = $sum - $newsum;
                        $params = json_encode(['currency' => $currency, 'amount' => $diff, 'draw' => $drawNumber], true);
                        $content=$this->notificationService->getContent('Payment reversed loto');
                        $this->notificationService->addNotification($userId, $content, $params,$bulk);
                    }
                    $status = true;
                    $message = "You have played your grid , Best of luck :)";
                } else {
                    $status = false;
                    $message = $updateutility[1];
                }
                
            }
        }

        return 1;
    }
}
