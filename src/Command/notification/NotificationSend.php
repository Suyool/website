<?php

namespace App\Command\notification;

use App\Entity\Notification\Notification;
use App\Service\NotificationServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotificationSend extends Command
{
    private $mr;
    private $notificationServices;

    public function __construct(ManagerRegistry $mr, NotificationServices $notificationServices)
    {
        parent::__construct();
        $this->notificationServices = $notificationServices;
        $this->mr = $mr->getManager('notification');
    }

    protected function configure()
    {
        //php bin/console 
        $this
            ->setName('app:notificationSend');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Send Notification to pending users'
        ]);

        $notification = 1;
        while ($notification) {
            $notBulk = $this->mr->getRepository(Notification::class)->findBy(['status' => "pending", 'bulk' => 1]);
            $notSingle = $this->mr->getRepository(Notification::class)->findBy(['status' => "pending", 'bulk' => 0]);
            if ($notSingle != null) {
                foreach ($notSingle as $notify) {
                    $this->notificationServices->PrcessingNot($notify->getId());
                    $output->writeln([
                        'proccessing!'
                    ]);
                }
            }
            if ($notBulk != null) {
                foreach ($notBulk as $notify) {
                    $this->notificationServices->PrcessingNot($notify->getId());
                    $output->writeln([
                        'proccessing!'
                    ]);
                }
            }

            if ($notBulk != null) {
                foreach ($notBulk as $notify) {
                    $content = $notify->getcontentId()->getId();
                    $this->notificationServices->PushBulkNotification($notify->getId(), $notify->getuserId(), $content, $notify->getparams(), $notify->getadditionalData());
                    $output->writeln([
                        'Success!'
                    ]);
                }
            }

            if ($notSingle != null) {
                foreach ($notSingle as $notify) {
                    $content = $notify->getcontentId()->getId();
                    $this->notificationServices->PushSingleNotification($notify->getId(), $notify->getuserId(), $content, $notify->getparams(), $notify->getadditionalData());
                    $output->writeln([
                        'Success!'
                    ]);
                }
            }
            sleep(10);
        }

        return 1;
    }
}
