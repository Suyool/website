<?php

namespace App\Command;

use App\Entity\Loto\loto;
use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_numbers;
use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\LOTO_tickets;
use App\Entity\Loto\notification;
use App\Utils\Helper;
use DateInterval;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class notificationresult extends Command
{
    private $mr;
    public function __construct(ManagerRegistry $mr)
    {
        parent::__construct();

        $this->mr = $mr->getManager('loto');
    }

    protected function configure()
    {
        //php bin/console 
        $this
            ->setName('app:result');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Notification result send'
        ]);

        $lastdraw=$this->mr->getRepository(LOTO_draw::class)->findOneBy([],['drawdate'=>'desc']);
        // dd(($lastdraw->getdrawdate()));
        $lastdrawdate=$lastdraw->getdrawdate()->format('Y-m-d H:i:s');
        dd($this->mr->getRepository(notification::class)->findPlayedUser($lastdrawdate));


        return 1;
    }
}
