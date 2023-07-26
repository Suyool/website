<?php

namespace App\Command;

use App\Entity\Loto\loto;
use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_numbers;
use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\LOTO_tickets;
use App\Entity\Loto\notification;
use App\Entity\Loto\order;
use App\Services\LotoServices;
use App\Services\SuyoolServices;
use App\Utils\Helper;
use DateInterval;
use DateTime;
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

    public function __construct(ManagerRegistry $mr, LotoServices $lotoServices, SuyoolServices $suyoolServices, $certificate, $hash_algo)
    {
        parent::__construct();

        $this->mr = $mr->getManager('loto');
        $this->suyoolServices = $suyoolServices;
        $this->lotoServices = $lotoServices;
        $this->certificate = $certificate;
        $this->hash_algo = $hash_algo;
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
        // $additionaldata[] = ['ticketId'=>123,'zeed'=>1,'bouquet'=>false];
        // dd(json_encode($additionaldata));
        // $array1=[1];
        // $array=[1,2,3,4];
        // foreach($array as $array){
        //     $ticketId=1;
        //     $zeed=1;
        //     $bouquet=1;
        //     $newElement = ['ticketId' => $ticketId, 'zeed' => $zeed, 'bouquet' => $bouquet];
        //     $additionaldata[] = [$newElement];
        // }
        // $count['count']=count($additionaldata);
        // $additionaldata[]=$count;
        // $additionalData=json_encode($additionaldata,true);
        // dd($additionalData);
        while ($play) {
            $heldOrder = $this->mr->getRepository(order::class)->findBy(['status' => 'held']);
            if ($heldOrder == null) {
                $play = 0;
            }
            foreach ($heldOrder as $held) {
                $lotoToBePlayed = $this->mr->getRepository(loto::class)->lotoToBePlayed($held->getId());
                // dd($lotoToBePlayed);
                foreach ($lotoToBePlayed as $lotoToBePlayed) {
                    $submit = $this->lotoServices->playLoto($lotoToBePlayed->getdrawnumber(), $lotoToBePlayed->getwithZeed(), $lotoToBePlayed->getgridSelected());
                    if ($lotoToBePlayed->getbouquet()) {
                        if ($submit[0]) {
                            sleep(1);
                            $ticketId = $this->lotoServices->GetTicketId();
                            sleep(1);
                            $BouquetGrids = $this->lotoServices->BouquetGrids($ticketId);
                            $lotoToBePlayed->setticketId($ticketId);
                            $lotoToBePlayed->setzeednumber($submit[1]);
                            $lotoToBePlayed->setgridSelected($BouquetGrids);

                            $this->mr->persist($lotoToBePlayed);
                            $this->mr->flush();
                            if($lotoToBePlayed->getwithZeed() && $lotoToBePlayed->getbouquet()){
                                $newElement = ['ticketId' => $ticketId, 'zeed' => $lotoToBePlayed->getwithZeed(), 'bouquet' => $lotoToBePlayed->getbouquet()];
                            }else if(!$lotoToBePlayed->getwithZeed() && $lotoToBePlayed->getbouquet()){
                                $newElement = ['ticketId' => $ticketId, 'bouquet' => $lotoToBePlayed->getbouquet()];
                            }else if($lotoToBePlayed->getwithZeed() && !$lotoToBePlayed->getbouquet()){
                                $newElement = ['ticketId' => $ticketId, 'zeed' => $lotoToBePlayed->getwithZeed()];
                            }else{
                                $newElement = ['ticketId' => $ticketId];
                            }
                            // $additionaldata[] = $newElement;
                        }
                    } else {
                        if ($submit[0]) {
                            sleep(1);
                            $ticketId = $this->lotoServices->GetTicketId();
                            $lotoToBePlayed->setticketId($ticketId);
                            $lotoToBePlayed->setzeednumber($submit[1]);

                            $this->mr->persist($lotoToBePlayed);
                            $this->mr->flush();
                            // $ticketId = 522;
                            if($lotoToBePlayed->getwithZeed() && $lotoToBePlayed->getbouquet()){
                                $newElement = ['ticketId' => $ticketId, 'zeed' => $lotoToBePlayed->getwithZeed(), 'bouquet' => $lotoToBePlayed->getbouquet()];
                            }else if(!$lotoToBePlayed->getwithZeed() && $lotoToBePlayed->getbouquet()){
                                $newElement = ['ticketId' => $ticketId, 'bouquet' => $lotoToBePlayed->getbouquet()];
                            }else if($lotoToBePlayed->getwithZeed() && !$lotoToBePlayed->getbouquet()){
                                $newElement = ['ticketId' => $ticketId, 'zeed' => $lotoToBePlayed->getwithZeed()];
                            }else{
                                $newElement = ['ticketId' => $ticketId];
                            }

                        }
                    }
                    $additionaldata[] = [$newElement];
                    $count['count']=count($additionaldata);
                    $additionaldata[]=$count;
                }
                $held->setstatus("purchased");
                $this->mr->persist($held);
                $this->mr->flush();

                $lotoidcompleted = $this->mr->getRepository(loto::class)->completed($held->getId());


                foreach ($lotoidcompleted as $lotoidcompletedsum) {
                    $newsum += $lotoidcompletedsum->getprice();
                }

                $additionalData = json_encode($additionaldata, true);

                $updateutility = $this->suyoolServices->UpdateUtilities(40000, $this->hash_algo, $this->certificate, $additionalData, $held->gettransId());
                // var_dump($additionaldata);
                echo $additionalData;
                if ($updateutility) {
                    $held->setamount(40000)
                        ->setcurrency("LBP")
                        ->setstatus("completed");

                    $this->mr->persist($held);
                    $this->mr->flush();
                    $status = true;
                    $message = "You have played your grid , Best of luck :)";
                } else {
                    $status = false;
                    $message = $updateutility[1];
                }
                sleep(10);
            }
        }

        return 1;
    }
}
