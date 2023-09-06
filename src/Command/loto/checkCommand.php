<?php

namespace App\Command\loto;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class checkCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        //php bin/console 
        $this
            ->setName('app:health');
        $this->setDescription('Check if the play lottery and the notification send commands are running');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Checking....');

        exec('ps aux | grep app:play | grep -v grep', $processOutput, $returnValuePlay);
        exec('ps aux | grep app:notificationSend | grep -v grep', $processOutput, $returnValueNotification);

        if ($returnValuePlay === 0) {
            $output->writeln('Populate Process is running.');
        } else {
            $output->writeln('Populate Process is not running.');
            exec('php bin/console app:play');
        }

        if ($returnValueNotification === 0) {
            $output->writeln('Populate Process is running.');
        } else {
            $output->writeln('Populate Process is not running.');
            exec('php bin/console app:notificationSend');
        }

        return Command::SUCCESS;

        #!/bin/bash
        // date
        // cd /home/developer/public_html/suyool.com;
        // #Check Loto Play cron
        // ps aux | grep "app:play" |grep -v grep
        // if [ $? -eq 0 ]; then
        //   echo "Loto Play cron is running."
        // else
        //   echo "Loto Play is not running."
        //   php bin/console app:play >> /home/developer/crons/loto_play.log 2>&1 &
        // fi
        // #Check Notification cron
        // ps aux | grep "app:notificationSend" |grep -v grep
        // if [ $? -eq 0 ]; then
        //   echo "notificationSend cron is running."
        // else
        //   echo "notificationSend cron is not running."
        //   php bin/console app:notificationSend >> /home/developer/crons/notification_send.log 2>&1 &
        // fi
        // #sleep 10
    }
}
