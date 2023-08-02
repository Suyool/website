<?php

namespace App\Command\loto;

use App\Entity\Loto\loto;
use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_numbers;
use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\LOTO_tickets;
use App\Entity\Loto\notification;
use App\Entity\Loto\order;
use App\Entity\Notification\content;
use App\Entity\Notification\Template;
use App\Service\NotificationServices;
use App\Utils\Helper;
use DateInterval;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class reminderNotification extends Command
{
    private $mr;
    private $notifMr;
    private $notificationServices;
    public function __construct(ManagerRegistry $mr,NotificationServices $notificationServices)
    {
        parent::__construct();

        $this->mr = $mr->getManager('loto');
        $this->notifMr=$mr->getManager('notification');
        $this->notificationServices=$notificationServices;
    }

    protected function configure()
    {
        //php bin/console 
        $this
            ->setName('app:reminder');
        //reminder users if played loto once every monday and thursday and don't play loto for 6 months at 10am
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Notification reminder send'
        ]);

        $notify=$this->mr->getRepository(loto::class)->findPlayedUserAndDontPlayThisWeek();
        // dd($notify);

        $draw = $this->mr->getRepository(LOTO_draw::class)->findOneBy([],['drawdate'=>'desc']);

        $date=new DateTime();
        $day=$date->format('w');
        // dd($day);

        foreach($notify as $notify)
        {
            $params=json_encode(['currency'=>'LBP','amount'=>number_format($draw->getlotoprize())]);
            $template=$this->notifMr->getRepository(Template::class)->findOneBy(['identifier'=>"reminder notification"]);
            $index=$template->getIndex();
            $content=$this->notifMr->getRepository(content::class)->findOneBy(['template'=>$template->getId(),'version'=>$index]);
            $this->notificationServices->addNotification($notify['suyoolUserId'],$content,$params);
            

        }

        if($day==4){
            if($index==6){
                $index = 1;
            }else{
                $index += 1;
            }
            $template->setindex($index);
            $this->notifMr->persist($template);
            $this->notifMr->flush();
        }
        


        return 1;
    }
}
