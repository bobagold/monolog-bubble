<?php

namespace Bubble;

class MemcachedArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testReadWrite()
    {
        if (!class_exists('Memcached')) {
            $this->markTestSkipped('Memcached is not installed');
        }
        $memcachedArray = new MemcachedArray();
        unset($memcachedArray['test']);
        $this->assertFalse(isset($memcachedArray['test']));
        $test = $memcachedArray['test'];
        $this->assertNull($test);
        $memcachedArray['test'] = 'ok';
        $this->assertTrue(isset($memcachedArray['test']));
        $test = $memcachedArray['test'];
        $this->assertEquals('ok', $test);
        unset($memcachedArray['test']);
    }
}
