<?php

namespace Bubble;
use ArrayAccess;

class MemcachedArray implements ArrayAccess
{
    private $memcached;
    private $prefix;

    public function __construct()
    {
        $this->prefix = substr(md5(__FILE__), -6);
        $this->memcached = new \Memcached();
        $this->connected = $this->memcached->addServer('127.0.0.1', '11211');
    }

    public function offsetExists($offset)
    {
        $value = $this->offsetGet($offset);
        return isset($value);
    }

    public function offsetGet($offset)
    {
        $value = $this->memcached->get($this->prefix . $offset);
        return $value === false ? null : $value;
    }

    public function offsetSet($offset, $value)
    {
        $this->memcached->set($this->prefix . $offset, $value, 24 * 3600);
    }

    public function offsetUnset($offset)
    {
        $this->memcached->delete($this->prefix . $offset);
    }
}
