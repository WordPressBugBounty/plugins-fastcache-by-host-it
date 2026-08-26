<?php

namespace FastCache\ObjectCache\Adapter;

class RedisAdapter implements AdapterInterface
{
    private $redis = null;
    private $prefix = '';

    public function connect($settings)
    {
        $this->prefix = isset($settings['object_cache_key_prefix']) ? $settings['object_cache_key_prefix'] : 'fc_';
        $host = isset($settings['object_cache_redis_host']) ? $settings['object_cache_redis_host'] : '127.0.0.1';
        $port = isset($settings['object_cache_redis_port']) ? (int) $settings['object_cache_redis_port'] : 6379;
        $pass = isset($settings['object_cache_redis_password']) ? $settings['object_cache_redis_password'] : '';
        $db = isset($settings['object_cache_redis_database']) ? (int) $settings['object_cache_redis_database'] : 0;

        try {
            $this->redis = new \Redis();
            if ($this->redis->connect($host, $port)) {
                if (!empty($pass)) {
                    $this->redis->auth($pass);
                }
                $this->redis->select($db);
                $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
            }
        } catch (\Exception $e) {
            $this->redis = null;
        }
    }

    public function get($key, &$found)
    {
        if (!$this->redis) {
            $found = false;
            return null;
        }
        $rk = $this->prefix . $key;
        $value = $this->redis->get($rk);
        if ($value === false && !$this->redis->exists($rk)) {
            $found = false;
            return null;
        }
        $found = true;
        return $value;
    }

    public function set($key, $data, $expire)
    {
        if (!$this->redis) {
            return false;
        }
        $rk = $this->prefix . $key;
        if ($expire > 0) {
            return $this->redis->setex($rk, $expire, $data);
        }
        return $this->redis->set($rk, $data);
    }

    public function delete($key)
    {
        if (!$this->redis) {
            return false;
        }
        return $this->redis->del($this->prefix . $key) > 0;
    }

    public function flush()
    {
        if (!$this->redis) {
            return false;
        }
        return $this->redis->flushDb();
    }
}
