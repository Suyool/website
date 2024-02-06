<?php


namespace App\Command\gift2Games;


use App\Entity\Gift2Games\Products;
use App\Service\Gift2GamesService;
use App\Service\SuyoolServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckBalance extends Command
{

    private $mr;
    private $gamesService;
    private $suyoolServices;

    public function __construct(ManagerRegistry $mr, Gift2GamesService $gamesService,SuyoolServices $suyoolServices)
    {
        parent::__construct();

        $this->mr = $mr->getManager('gift2games');
        $this->gamesService = $gamesService; // Add this line
        $this->suyoolServices = $suyoolServices;

    }

    protected function configure()
    {
        //php bin/console
        $this
            ->setName('app:checkGift2GamesBalance');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Checking Balance..'
        ]);

        $checkBalance = $this->gamesService->checkBalance();
        $checkBalance = json_decode($checkBalance[1]);
        $balance = $checkBalance->data->balanceUSD;
        if($balance < 300) {
            $emailMessageBlacklistedCard = "Dear,<br><br>Kindly refill your Balance at Gift2games<br><br> Your current Balance is $balance";
            $this->suyoolServices->sendDotNetEmail('Balance to law please refill your account', 'elio.najem@elbarid.com,web@suyool.com', $emailMessageBlacklistedCard, "", "", "suyool@noreply.com", "Suyool", 1, 0);
        }

        return Command::SUCCESS;
    }
}