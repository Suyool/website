<?php

// src/Service/RateLimiter.php
namespace App\Service;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class RateLimiter
{
    private $cache;

    public function __construct()
    {
        //Symfony's cache
        $this->cache = new FilesystemAdapter();
    }

    public function limitRequests($ipAddress, $merchantId =null, $limit = 5, $period = 60)
    {
        // Create a cache item for the request count
        $cacheKey = 'request_count_' . $ipAddress . '-' . $merchantId;
        $cacheItem = $this->cache->getItem($cacheKey);

        // Get the current request count or initialize if not present
        $count = $cacheItem->get() ?? 0;

        // Check if the limit is exceeded
        if ($count >= $limit) {
            return false; // Rate limit exceeded
        }

        // Increment request count and store it in cache
        $count++;
        $cacheItem->set($count);
        $cacheItem->expiresAfter($period);
        $this->cache->save($cacheItem);

        return true; // Within rate limit
    }
}
