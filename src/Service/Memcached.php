<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\MemcachedAdapter;

class Memcached
{
    protected $cache;
    private $mediumLifetime;

    public function __construct($mediumLifetime)
    {
        $this->mediumLifetime = $mediumLifetime;

        if (MemcachedAdapter::isSupported()) {
            try {
                $client = MemcachedAdapter::createConnection('memcached://' . "89.108.165.201");
                // dd($client);
                $this->cache = new MemcachedAdapter($client, 'suyool');
            } catch (\ErrorException $e) {;
                dd($e);
            }
        }
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testmem($lotoServices)
    {
        $filter = $lotoServices->VoucherFilter("ALFA");
        $cacheKey = 'cache_key_for_your_data'; // Choose a unique key for your cache item

        if (isset($this->cache)) {
            $cachedData = $this->cache->getItem($cacheKey);

            if (!$cachedData->isHit()) {
                $data = $filter;
                $cachedData->set($data)->expiresAfter($this->mediumLifetime);
                $this->cache->save($cachedData);
                echo "data from memcached";
            } else {
                echo "data came without memcached without hit";
                $data = $filter;
            }
        } else {
            echo "data came without memcached";
            $data = $filter;
        }

        return $data;
    }
}
