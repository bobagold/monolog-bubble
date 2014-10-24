<?php


namespace Bubble;
use ArrayAccess;
use Memcache;

class MemcacheArray implements ArrayAccess
{
    private $memcache;
    private $prefix;

    public function __construct()
    {
        $this->prefix = substr(md5(__FILE__), -6);
        $this->memcache = new Memcache;
        if (!$this->memcache->connect('127.0.0.1')) {
            unset($this->memcache);
        }
    }

    public function offsetExists($offset)
    {
        $value = $this->offsetGet($offset);
        return isset($value);
    }

    public function offsetGet($offset)
    {
        if ($this->memcache) {
            $value = $this->memcache->get($this->prefix . $offset);
            return $value === false ? null : $value;
        }
        return null;
    }

    public function offsetSet($offset, $value)
    {
        if ($this->memcache) {
            $this->memcache->set($this->prefix . $offset, $value, 0, 24 * 3600);
        }
    }

    public function offsetUnset($offset)
    {
        if ($this->memcache) {
            $this->memcache->delete($this->prefix . $offset);
        }
    }
}