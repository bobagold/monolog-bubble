<?php

namespace Bubble;

class MemcacheArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testReadWrite()
    {
        $memcacheArray = new MemcacheArray();
        unset($memcacheArray['test']);
        $this->assertFalse(isset($memcacheArray['test']));
        $test = $memcacheArray['test'];
        $this->assertNull($test);
        $memcacheArray['test'] = 'ok';
        $this->assertTrue(isset($memcacheArray['test']));
        $test = $memcacheArray['test'];
        $this->assertEquals('ok', $test);
        unset($memcacheArray['test']);
    }
}