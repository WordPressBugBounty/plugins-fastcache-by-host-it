<?php

namespace FastCache\ObjectCache\Adapter;

class MemcachedAdapter implements AdapterInterface
{
    private $handler = null;
    private $is_memcached = false;
    private $prefix = '';

    public function connect($settings)
    {
        $this->prefix = isset($settings['object_cache_key_prefix']) ? $settings['object_cache_key_prefix'] : 'fc_';
        $host = isset($settings['object_cache_memcached_host']) ? $settings['object_cache_memcached_host'] : '127.0.0.1';
        $port = isset($settings['object_cache_memcached_port']) ? (int) $settings['object_cache_memcached_port'] : 11211;

        if (class_exists('Memcached')) {
            $this->handler = new \Memcached();
            $this->handler->addServer($host, $port);
            $this->is_memcached = true;
        } elseif (class_exists('Memcache')) {
            $this->handler = new \Memcache();
            $this->handler->addServer($host, $port);
            $this->is_memcached = false;
        }
    }

    public function get($key, &$found)
    {
        if (!$this->handler) {
            $found = false;
            return null;
        }

        $value = $this->handler->get($this->prefix . $key);

        if ($this->is_memcached) {
            if ($this->handler->getResultCode() === \Memcached::RES_NOTFOUND) {
                $found = false;
                return null;
            }
        } else {
            if ($value === false) {
                $found = false;
                return null;
            }
        }

        $found = true;
        return $value;
    }

    public function set($key, $data, $expire)
    {
        if (!$this->handler) {
            return false;
        }

        if ($this->is_memcached) {
            return $this->handler->set($this->prefix . $key, $data, $expire);
        } else {
            // Memcache::set(key, var, flags, expire)
            return $this->handler->set($this->prefix . $key, $data, 0, $expire);
        }
    }

    public function delete($key)
    {
        if (!$this->handler) {
            return false;
        }
        return $this->handler->delete($this->prefix . $key);
    }

    public function flush()
    {
        if (!$this->handler) {
            return false;
        }
        return $this->handler->flush();
    }
}
