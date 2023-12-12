<?php

namespace App\Command\Topup;

use App\Entity\topup\attempts;
use App\Entity\topup\orders;
use App\Service\BobPaymentServices;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class checkPendingTransactions extends Command
{

    private $mr;
    private $bobPaymentServices;

    public function __construct(ManagerRegistry $mr, BobPaymentServices $bobPaymentServices)
    {
        parent::__construct();

        $this->mr = $mr->getManager('topup');
        $this->bobPaymentServices = $bobPaymentServices;
    }

    protected function configure()
    {
        //php bin/console 
        $this
            ->setName('app:topupCheck');
        $this->setDescription('Canceled the transactions that don`t have response after 10 minutes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Checking....');

        $topupPendingAfter10minutes = $this->mr->getRepository(orders::class)->findPendingTransactionsAfter10Minutes();
        // dd($topupPendingAfter10minutes);
        foreach ($topupPendingAfter10minutes as $topupPendingAfter10minutes) {
            if($topupPendingAfter10minutes != null){
            if (is_null($this->mr->getRepository(attempts::class)->findOneBy(['transactionId' => $topupPendingAfter10minutes->getOrders()->getTransId()]))) {
                $changestatus = $this->bobPaymentServices->RetrievePaymentDetailsOnCheck($topupPendingAfter10minutes->getOrders()->getTransId(), $topupPendingAfter10minutes->getOrders()->getSuyoolUserId());
                $topupPendingAfter10minutes->getOrders()->setStatus(orders::$statusOrder["{$changestatus[1]}"]);
                $this->mr->persist($topupPendingAfter10minutes);
                $this->mr->flush();
                $attempt = $this->mr->getRepository(attempts::class)->findOneBy(['transactionId' => $topupPendingAfter10minutes->getOrders()->getTransId()]);
                if(!is_null($attempt)){
                    $changestatusWithoutPutOnDb = $this->bobPaymentServices->retrievedataForTopUpAfterCheck($attempt->getStatus(), $attempt->getResponse(), $attempt->getCard(),$topupPendingAfter10minutes->getSession());
                }
            } else {
                $topupPendingAfter10minutes->getOrders()->setStatus(orders::$statusOrder["CANCELED"]);
                $this->mr->persist($topupPendingAfter10minutes);
                $this->mr->flush();
                $attempt = $this->mr->getRepository(attempts::class)->findOneBy(['transactionId' => $topupPendingAfter10minutes->getOrders()->getTransId()]);
                $changestatusWithoutPutOnDb = $this->bobPaymentServices->retrievedataForTopUpAfterCheck($attempt->getStatus(), $attempt->getResponse(), $attempt->getCard(),$topupPendingAfter10minutes->getSession());
            }
        }else{
            $topupPendingAfter10minutesOrder =  $this->mr->getRepository(orders::class)->findPendingTransactionsAfter10MinutesOrder();
            // dd($topupPendingAfter10minutesOrder);
            foreach($topupPendingAfter10minutesOrder as $topupPendingAfter10minutesOrder)
            {
                $topupPendingAfter10minutesOrder->setStatus(orders::$statusOrder['CANCELED']);
                $this->mr->persist($topupPendingAfter10minutesOrder);
                $this->mr->flush();
            }
        }
        }
        $output->writeln('Checked');

        return 1;
    }
}
