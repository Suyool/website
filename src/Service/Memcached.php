<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

class Memcached
{
    // protected $cache;
    // private $mediumLifetime;

    // public function __construct($mediumLifetime)
    // {
    //     $this->mediumLifetime = $mediumLifetime;

    //     if (MemcachedAdapter::isSupported()) {
    //         try {
    //             $client = MemcachedAdapter::createConnection('memcached://' . "89.108.165.201");
    //             // dd($client);
    //             $this->cache = new MemcachedAdapter($client, 'suyool');
    //         } catch (\ErrorException $e) {;
    //             dd($e);
    //         }
    //     }
    // }
    public function __construct()
    {
    }

    // /**
    //  * @throws \Psr\Cache\InvalidArgumentException
    //  */
    // public function testmem($lotoServices)
    // {
    //     $filter = $lotoServices->VoucherFilter("ALFA");
    //     $cacheKey = 'cache_key_for_your_data'; // Choose a unique key for your cache item

    //     if (isset($this->cache)) {
    //         $cachedData = $this->cache->getItem($cacheKey);

    //         if (!$cachedData->isHit()) {
    //             $data = $filter;
    //             $cachedData->set($data)->expiresAfter(3600);
    //             $this->cache->save($cachedData);
    //             echo "data from memcached";
    //         } else {
    //             echo "data came without memcached without hit";
    //             $data = $filter;
    //         }
    //     } else {
    //         echo "data came without memcached";
    //         $data = $filter;
    //     }

    //     return $data;
    // }

    // /**
    //  * @throws \Psr\Cache\InvalidArgumentException
    //  */
    // public function testmem($lotoServices)
    // {
    //     $filter = $lotoServices->VoucherFilter("ALFA");
    //     $cacheKey = 'cache_key_for_your_data';

    //     $cachePool = new FilesystemAdapter();

    //     $cachedItem = $cachePool->getItem($cacheKey);
    //     // dd($cachedItem);
    //     if (!$cachedItem->isHit()) {
    //         $data = $filter;

    //         $cachedItem->set($data);
    //         $cachePool->save($cachedItem);
    //     } else {
    //         dd("retrive data");
    //         $data = $cachedItem->get();
    //     }

    //     return $data;
    // }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testmem($lotoServices)
    {
        $file = "../var/cache/dev/newfile.txt";
        $clearingTime = time() - (60);

        $filter = null;

        if (file_exists($file) && (filemtime($file) > $clearingTime) && (filesize($file) > 0)) {
            $operationsjson = file_get_contents($file);
            return json_decode($operationsjson, true);
        } else {
            $filter = $lotoServices->VoucherFilter("ALFA");
            $jsonData = json_encode($filter);

            $myfile = fopen($file, "w") or die("Unable to open file!");
            fwrite($myfile, $jsonData);
            fclose($myfile);
        }

        return $filter;
    }
}
