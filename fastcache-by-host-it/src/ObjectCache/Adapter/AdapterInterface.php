<?php

namespace FastCache\ObjectCache\Adapter;

interface AdapterInterface
{
    public function connect($settings);
    public function get($key, &$found);
    public function set($key, $data, $expire);
    public function delete($key);
    public function flush();
}
