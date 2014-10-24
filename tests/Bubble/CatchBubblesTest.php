<?php
namespace Bubble;

class CatchBubblesTest extends \PHPUnit_Framework_TestCase
{
    public function testFirstBubblePasses()
    {
        $catchBubble = new \Bubble\CatchBubble();
        $this->assertTrue($catchBubble->bubble($this->exception(), array('time' => new \DateTime())));
    }

    public function testSameBubbleCaught()
    {
        $catchBubble = new \Bubble\CatchBubble();
        $catchBubble->bubble($this->exception(), $this->details());
        $this->assertFalse($catchBubble->bubble($this->exception(), $this->details()));
    }

    public function testAnotherBubblePasses()
    {
        $catchBubble = new \Bubble\CatchBubble();
        $catchBubble->bubble($this->exception(), array('time' => new \DateTime()));
        $this->assertTrue($catchBubble->bubble(array('file' => 'b.txt'), array('time' => new \DateTime())));
    }

    public function testBubblesAfterTimeout()
    {
        $catchBubble = new \Bubble\CatchBubble('PT1H');
        $now = new \DateTime();
        $catchBubble->bubble($this->exception(), array('time' => $now));
        $now = clone $now;
        $this->assertTrue($catchBubble->bubble($this->exception(), array('time' => $now->add(new \DateInterval('P1D')))));
    }

    public function testNotBubblesAfterSmallTimeout()
    {
        $catchBubble = new \Bubble\CatchBubble('PT1H');
        $now = new \DateTime();
        $catchBubble->bubble($this->exception(), array('time' => $now));
        $now = clone $now;
        $this->assertFalse($catchBubble->bubble($this->exception(), array('time' => $now->add(new \DateInterval('PT1M')))));
    }

    public function testStorage()
    {
        $storage = new \ArrayObject();
        $catchBubble = new \Bubble\CatchBubble('PT1H', $storage);
        $catchBubble->bubble($this->exception(), $this->details());
        $keys = array();
        foreach ($storage as $k => $v) $keys[] = $k;
        foreach ($keys as $k) unset($storage[$k]);
        $this->assertTrue($catchBubble->bubble($this->exception(), $this->details()));
    }

    private function exception()
    {
        return array(
            'file' => 'a.txt',
            'line' => 11,
            'type' => 'RuntimeException',
            'message' => 'invalid arguments'
    );
}

    private function details()
    {
        return array('additional' => 'info', 'time' => new \DateTime());
    }
} 