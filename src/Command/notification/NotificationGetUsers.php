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
        // $suyoolUsers = [
        //     "data" => [
        //         "users"=>[
        //             "AccountID" => 20,
        //             "FirstName" => "Geo",
        //             "LastName" => "Ass",
        //             "MobileNo" => "0",
        //             "LanguageID" => 1,
        //             "type"=>1
        //         ],
        //         "legal"=>[
        //             "AccountID" => 21,
        //             "CompanyName" => "Suyool",
        //             "type"=>3
        //         ],
        //         "merchants"=>[
        //             "AccountID" => 22,
        //             "CompanyName" => "LOTO",
        //             "type"=>5
        //         ]
        //     ],
        // ];
        
        // Convert the array to JSON for printing or further use
        // dd($suyoolUsers);
        $output->writeln([
            'Getting All suyool users'
        ]);
        if (is_array($suyoolUsers)) {
            $users=$this->mr->getRepository(Users::class)->findAll();
            foreach($users as $users){
                $this->mr->remove($users);
            }
            $this->mr->flush();
            foreach ($suyoolUsers as $types) {
                foreach($types as $item){
                    $user = new Users;
                    $user
                        ->setsuyoolUserId($item["AccountID"])
                        ->setfname(@$item["FirstName"])
                        ->setlname(@$item["LastName"])
                        ->setMobileNo(@$item['MobileNo'])
                        ->setlang(@$item["LanguageID"])
                        ->settype($item['Type'])
                        ->setCompanyName(@$item['CompanyName']);
    
                    $this->mr->persist($user);
                    $this->mr->flush();
                }

                // $user = new Users;
                // $user
                //     ->setsuyoolUserId($item["UserAccountID"])
                //     ->setfname($item["FirstName"])
                //     ->setlname($item["LastName"])
                //     ->setMobileNo($item['MobileNo'])
                //     ->setlang($item["LanguageID"]);

                // $this->mr->persist($user);
                // $this->mr->flush();
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
