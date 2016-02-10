<?php

namespace Alpipego\AdaptiveImages\Cache;

class Cache
{
    private $cache;
    private $expire;

    public function __construct($server = '127.0.0.1', $port = 11211)
    {
        if (class_exists('Memcached')) {
            $this->cache = new \Memcached();
            $this->cache->addServer($server, $port);
            $this->expire = $this->setExpire();
        } else {
            echo '<code><pre>';
                var_dump('memcached is not installed');
            echo '</pre></code>';
        }
    }

    public function setExpire()
    {
        if ((defined('WP_STAGE') || defined('WP_ENV')) == 'local') {
            return 3600 + mt_rand(0, 1200);
        } else {
            return (3600 * 24) + mt_rand(0, 3600);
        }
    }

    public function get($request)
    {
        $key = $this->makeKey($request);

        $cacheResult = $this->cache->get($key);

        if ($cacheResult) {
            // $this->cache->delete($key);
            return $cacheResult;
        }

        return false;
    }

    public function set($request, $result)
    {
        $key = $this->makeKey($request);

        $this->cache->set($key, $result, $this->expire);
    }

    private function makeKey($request)
    {
        if (isset($request['action'])) {
            unset($request['action']);
        }
        asort($request);

        return md5(serialize($request));
    }

    public function getAll()
    {
        $result = [];
        $keys = $this->cache->getAllKeys();

        foreach ($keys as $key) {
            $result[$key] = $this->cache->get($key);
        }

        return $result;
    }

    public function purge()
    {
        $this->cache->deleteMulti($this->cache->getAllKeys());

        return $this->cache;
    }
}
