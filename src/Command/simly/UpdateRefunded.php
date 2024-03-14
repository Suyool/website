<?php

namespace App\Command\simly;

use App\Entity\Simly\Esim;
use App\Entity\Simly\Logs;
use App\Entity\Simly\Order;
use App\Service\LogsService;
use App\Service\NotificationServices;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateRefunded extends Command
{
    private $mr;
    private $notificationServices;
    private $suyoolServices;

    public function __construct(ManagerRegistry $mr, NotificationServices $notificationServices,SuyoolServices $suyoolServices)
    {
        parent::__construct();
        $this->notificationServices = $notificationServices;
        $this->mr = $mr->getManager('simly');
        $this->suyoolServices = $suyoolServices;
    }

    protected function configure()
    {
        //php bin/console 
        $this
            ->setName('app:updateSimlyRefundedeSims');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Checking...'
        ]);

        $esims = $this->mr->getRepository(Esim::class)->refundedEsims();
        // dd($esims);
        foreach($esims as $esim)
        {
            $update=$this->suyoolServices->UpdateUtilities(0,"",$esim[0]->getTransId());
            $logs = new LogsService($this->mr);
            $logs->pushLogs(new Logs, "app:updateSimlyRefundedeSims", @$update[3], @$update[2], @$update[4],@$update[5]);
            if($update[0]){
                $output->writeln([
                    "{$esim[0]->getTransId()} :  reversed , amount : {$esim[0]->getAmount()}"
                ]);
                $esim[0]->setStatus(Order::$statusOrder['CANCELED']);
                $esim[0]->setError("Reversed Refunded esim");
                $this->mr->persist($esim[0]);
                $this->mr->flush();
            }
        }
        $output->writeln([
            "Finishing ..."
        ]);

        return 1;


    }
}
