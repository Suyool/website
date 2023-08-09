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
    public function getVouchers($lotoServices)
    {
        if ($_ENV['APP_ENV'] == 'prod') {
            $file = "../var/cache/prod/alfaVoucher.txt";
        } else {
            $file = "../var/cache/dev/alfaVoucher.txt";
        }

        // $file = "../var/cache/dev/alfaVoucher.txt";
        $clearingTime = time() - (60);

        $filter = null;

        if (file_exists($file) && (filemtime($file) > $clearingTime) && (filesize($file) > 0)) {
            $operationsjson = file_get_contents($file);
            return json_decode($operationsjson, true);
        } else {
            $filter = $lotoServices->VoucherFilter("ALFA");
            // dd($filter);

            foreach ($filter as &$item) {
                switch ($item['vouchertype']) {
                    case 1:
                        $item['desc1'] = "$1.22 Alfa recharge card";
                        $item['desc2'] = "$1.37 Credit Only without validity";
                        $item['desc3'] = "Credit Only";
                        break;
                    case 13:
                        $item['desc1'] = "$3.02 Alfa recharge card";
                        $item['desc2'] = "Credit and 13 Days Validity";
                        $item['desc3'] = "Credit and 13 Days Validity";
                        break;
                    case 4:
                        $item['desc1'] = "$4.50 Alfa recharge card";
                        $item['desc2'] = "Credit and up to 35 Days";
                        $item['desc3'] = "Credit and up to 35 Days";
                        break;
                    case 35:
                        $item['desc1'] = "$7.58 Alfa recharge card";
                        $item['desc2'] = "Credit and 35 Days Validity";
                        $item['desc3'] = "Credit and 35 Days Validity";
                        break;
                    case 65:
                        $item['desc1'] = "$15.15 Alfa recharge card";
                        $item['desc2'] = "Credit and 65 Days Validity";
                        $item['desc3'] = "Credit and 65 Days Validity";
                        break;
                    case 95:
                        $item['desc1'] = "$22.73 Alfa recharge card";
                        $item['desc2'] = "Credit and 95 Days Validity";
                        $item['desc3'] = "Credit and 95 Days Validity";
                        break;
                    case 32:
                        $item['desc1'] = "$7.50 Alfa recharge card";
                        $item['desc2'] = "Waffer Credit and 30 Days Validity";
                        $item['desc3'] = "Waffer Credit and 30 Days Validity";
                        break;
                    case 33:
                        $item['desc1'] = "Waffer Credit and 30 Days Validity";
                        $item['desc2'] = "Waffer Credit and 30 Days Validity";
                        $item['desc3'] = "Waffer Credit and 30 Days Validity";
                        break;
                    default:
                        $item['desc1'] = "default";
                        $item['desc2'] = "default";
                        $item['desc3'] = "default";
                        break;
                }
            }

            // dd($filter);
            $jsonData = json_encode($filter);

            $myfile = fopen($file, "w") or die("Unable to open file!");
            fwrite($myfile, $jsonData);
            fclose($myfile);
        }

        return $filter;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getVouchersTouch($lotoServices)
    {
        if ($_ENV['APP_ENV'] == 'prod') {
            $file = "../var/cache/prod/touchVoucher.txt";
        } else {
            $file = "../var/cache/dev/touchVoucher.txt";
        }

        // $file = "../var/cache/dev/touchVoucher.txt";
        $clearingTime = time() - (60);

        $filter = null;

        if (file_exists($file) && (filemtime($file) > $clearingTime) && (filesize($file) > 0)) {
            $operationsjson = file_get_contents($file);
            return json_decode($operationsjson, true);
        } else {
            $filter = $lotoServices->VoucherFilter("MTC");
            // dd($filter);

            foreach ($filter as &$item) {
                switch ($item['vouchertype']) {
                    case 1:
                        $item['desc1'] = "Credit Only";
                        $item['desc2'] = "$1.22 Touch recharge card";
                        $item['desc3'] = "$1.22 Touch recharge card";
                        break;
                    case 10:
                        $item['desc1'] = "10 Days Validity & 5 Days Grace";
                        $item['desc2'] = "Credit and 13 Days Validity";
                        $item['desc3'] = "$3.79 Touch recharge card";
                        break;
                    case 29:
                        $item['desc1'] = "30 Days Validity & 5 Days Grace";
                        $item['desc2'] = "Credit and up to 35 Days";
                        $item['desc3'] = "$4.50 Touch recharge card";
                        break;
                    case 30:
                        $item['desc1'] = "30 Days Validity & 5 Days Grace";
                        $item['desc2'] = "Credit and 35 Days Validity";
                        $item['desc3'] = "$7.58 Touch recharge card";
                        break;
                    case 60:
                        $item['desc1'] = "60 Days Validity & 5 Days Grace";
                        $item['desc2'] = "Credit and 65 Days Validity";
                        $item['desc3'] = "$15.15 Touch recharge card";
                        break;
                    case 90:
                        $item['desc1'] = "90 Days Validity & 5 Days Grace";
                        $item['desc2'] = "Credit and 95 Days Validity";
                        $item['desc3'] = "$22.73 Touch recharge card";
                        break;
                    case 31:
                        $item['desc1'] = "30 Days Validity";
                        $item['desc2'] = "Waffer Credit and 30 Days Validity";
                        $item['desc3'] = "$4.50 Touch recharge card";
                        break;
                    default:
                        $item['desc1'] = "default";
                        $item['desc2'] = "default";
                        $item['desc3'] = "default";
                        break;
                }
            }

            // dd($filter);
            $jsonData = json_encode($filter);

            $myfile = fopen($file, "w") or die("Unable to open file!");
            fwrite($myfile, $jsonData);
            fclose($myfile);
        }

        return $filter;
    }
}
