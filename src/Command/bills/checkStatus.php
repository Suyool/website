<?php

namespace App\Command\bills;

use App\Entity\Touch\Order;
use App\Service\LotoServices;
use App\Service\NotificationServices;
use App\Service\sendEmail;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class checkStatus extends Command
{
    private $touchMr;
    private $suyoolServices;

    public function __construct(ManagerRegistry $mr, SuyoolServices $suyoolServices)
    {
        parent::__construct();

        $this->touchMr = $mr->getManager('touch');
        $this->suyoolServices = $suyoolServices;
    }

    protected function configure()
    {
        //php bin/console 
        $this
            ->setName('app:checkstatus');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Checking status...'
        ]);

        $statusHeld = $this->touchMr->getRepository(Order::class)->findBy(['status' => 'held']);
        $statusPending = $this->touchMr->getRepository(Order::class)->findBy(['status' => 'pending']);

        foreach ($statusHeld as $status) {
            $updateUtility = $this->suyoolServices->UpdateUtilities($status->getamount(), "", $status->gettransId());
            if ($updateUtility[0]) {
                $status->setstatus(Order::$statusOrder['COMPLETED']);
            } else {
                $status->setstatus(Order::$statusOrder['CANCELED'])
                    ->seterror("reversed error " . $updateUtility[1]);
            }

            $this->touchMr->persist($status);
            $this->touchMr->flush();
        }

        foreach ($statusPending as $status) {
            $status->setstatus(Order::$statusOrder['CANCELED'])
                ->seterror("status pending changed to canceled");

            $this->touchMr->persist($status);
            $this->touchMr->flush();
        }
        return 1;
    }
}
