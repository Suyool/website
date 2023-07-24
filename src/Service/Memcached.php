<?php
namespace App\Service;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

class Memcached
{
    // protected $cache;
    // public function __construct()
    // {
    //     if (MemcachedAdapter::isSupported()) {
    //         try {
    //             $client = MemcachedAdapter::createConnection('memcached://' . "89.108.165.201");
    //             dd($client);
    //             $this->cache = new MemcachedAdapter($client, 'suyool');
    //         } catch (\ErrorException $e) {;
    //             dd($e);
    //         }
    //     }
    // }
    // /**
    //  * @throws \Psr\Cache\InvalidArgumentException
    //  */
    // public function testmem()
    // {
    //     // dd($this->cache);
    //     if (isset($this->cache)) {
    //         $cachedPrograms = $this->cache->getItem('AlfaVouchers');
    //         $cachedPrograms->set("elie")->expiresAfter(3600);
    //         $this->cache->save($cachedPrograms);
    //         dd($cachedPrograms->get());
    //     } else {
    //         dd("ok");
    //     }
    // }
}