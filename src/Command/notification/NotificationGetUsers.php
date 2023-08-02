<?php

namespace App\Command\notification;

use App\Entity\Notification\Users;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NotificationGetUsers extends Command
{
    private $mr;
    private $suyoolServices;
    private $hash_algo;
    private $certificate;

    public function __construct(ManagerRegistry $mr, SuyoolServices $suyoolServices, $certificate, $hash_algo)
    {
        parent::__construct();
        $this->suyoolServices = $suyoolServices;
        $this->mr = $mr->getManager('notification');
        $this->hash_algo = $hash_algo;
        $this->certificate = $certificate;
    }

    protected function configure()
    {
        //php bin/console 
        $this
            ->setName('app:notificationUsers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $ChannelID = 20;
        $suyoolUsers = $this->suyoolServices->GetAllUsers($ChannelID, $this->hash_algo, $this->certificate);

        $output->writeln([
            'Getting All suyool users'
        ]);


        if (is_array($suyoolUsers)) {
            $this->mr->getRepository(Users::class)->truncate();

            $output->writeln([
                'Truncate users table'
            ]);

            foreach ($suyoolUsers as $item) {

                $user = new Users;
                $user
                    ->setsuyoolUserId($item["UserAccountID"])
                    ->setfname($item["FirstName"])
                    ->setlname($item["LastName"])
                    ->setlang($item["LanguageID"]);

                $this->mr->persist($user);
                $this->mr->flush();
            }
        } else {
            $output->writeln([
                'Somrthings went wrong!!'
            ]);
            return 0;
        }
        $output->writeln([
            'Success!'
        ]);
        return 1;
    }
}
