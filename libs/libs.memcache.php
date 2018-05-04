<?php

class Libs_Memcache {
    private $memcache;

    public function __construct()
    {
        $mmc = new Memcache();
        if ($mmc == false) {
            return false;
        }
        $this->memcache = $mmc;
        global $memcache_servers;
        foreach ($memcache_servers as $line) {
            $memcache_server = explode(':', $line);
            $host = $memcache_server['0'];
            $port = $memcache_server['1'];
            $this->memcache->addServer($host, $port);
        }
        $this->memcache->setCompressThreshold(200, 0.2);
    }

    public function setWithDep($key, $data, $dep_key)
    {
        $dep_value = $this->get($dep_key);
        if (!$dep_value) {
            $dep_value = getCurrentTime();
            $result = $this->set($dep_key, $dep_value);
            if (!$result) {
                return false;
            }
        }
        $key = $key . '_' . $dep_value;
        return $this->set($key, $data);
    }

    public function get($keys, $dep_key = null)
    {
        if (!empty($dep_key)) {
            $dep_value = $this->get($dep_key);
            if (!$dep_value) {
                return false;
            }
            $keys = $keys . '_' . $dep_value;
            $data = $this->get($keys);
        } else {
            $data = $this->memcache->get($keys);
        }
        return $data;
    }

    public function delete($key)
    {
        $result = $this->memcache->delete($key, 0);
        return $result;
    }

    public function set($key, $data, $life_time_limit = 2592000, $memcache_compressed = MEMCACHE_COMPRESSED)
    {
        $result = $this->memcache->set($key, $data, $memcache_compressed, $life_time_limit);
        if (!$result) {
            writeLog('time:' . date('Ymd-H:i:s') . '||set key ' . json_encode($key), 'memcache_error');
        }
        return $result;
    }

    public function replace($key, $data, $memcache_compressed = MEMCACHE_COMPRESSED, $life_time_limit = 2592000)
    {
        $result = $this->memcache->replace($key, $data, $memcache_compressed, $life_time_limit);
        return $result;
    }

    public function increment($key, $value = 1)
    {
        $result = $this->memcache->increment($key, $value);
        return $result;
    }

    public function decrement($key, $value = 1)
    {
        $result = $this->memcache->decrement($key, $value);
        return $result;
    }

    public function flush()
    {
        return $this->memcache->flush();
    }

}