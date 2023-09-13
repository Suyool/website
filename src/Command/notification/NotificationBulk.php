<?php

namespace App\Command\notification;

use App\Entity\Notification\Notification;
use App\Service\NotificationServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotificationBulk extends Command
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
            ->setName('app:notificationBulk');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Send Notification to pending users'
        ]);

        $not = $this->mr->getRepository(Notification::class)->findBy(['status' => "pending", 'bulk' => 1]);
        foreach ($not as $notify) {
            $content = $notify->getcontentId()->getId();
            $this->notificationServices->PushBulkNotification($notify->getId(), $notify->getuserId(), $content, $notify->getparams(), $notify->getadditionalData());
        }

        $output->writeln([
            'Success!'
        ]);
        return 1;
    }
}
