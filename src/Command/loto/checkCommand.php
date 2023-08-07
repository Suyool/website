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
        exec('ps aux | grep app:play | grep -v grep', $processOutput, $returnValueNotification);

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
    }
}
