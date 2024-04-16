<?php

// src/Command/FetchTeamsRankingsCommand.php

namespace App\Command\RallyPaper;

use App\Service\SuyoolServices;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

class FetchTeamsRankingsCommand extends Command
{
    private $suyoolServices;
    private $memcachedCache;
    public function __construct(SuyoolServices $suyoolServices,AdapterInterface  $memcachedCache)
    {
        parent::__construct();
        $this->suyoolServices = $suyoolServices;
        $this->memcachedCache = $memcachedCache;

    }
    protected function configure()
    {
        //php bin/console
        $this
            ->setName('app:fetch-teams-rankings');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Fetching ranking..'
        ]);
        $response = $this->suyoolServices->getTeamsRankings();

        $cacheKey = 'teamRanking';
        $cacheItem = $this->memcachedCache->getItem($cacheKey);
        $cacheItem->set($response);
        $this->memcachedCache->save($cacheItem);

        return Command::SUCCESS;
    }
}
