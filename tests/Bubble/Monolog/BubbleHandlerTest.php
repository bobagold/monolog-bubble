<?php


namespace Bubble\Monolog;

use Monolog\Handler\AbstractHandler;
use Monolog\Handler\TestHandler;
use Monolog\Logger;

class BubbleHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array Record
     */
    protected function getRecord($level = Logger::WARNING, $message = 'test', $context = array())
    {
        return array(
            'message' => $message,
            'context' => $context,
            'level' => $level,
            'level_name' => Logger::getLevelName($level),
            'channel' => 'test',
            'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true))),
            'extra' => array(),
        );
    }

    /**
     * @return array
     */
    protected function getMultipleRecords()
    {
        return array(
            $this->getRecord(Logger::DEBUG, 'debug message 1'),
            $this->getRecord(Logger::DEBUG, 'debug message 2'),
            $this->getRecord(Logger::INFO, 'information'),
            $this->getRecord(Logger::WARNING, 'warning'),
            $this->getRecord(Logger::ERROR, 'error')
        );
    }

    /**
     * @return \Monolog\Formatter\FormatterInterface
     */
    protected function getIdentityFormatter()
    {
        $formatter = $this->getMock('Monolog\\Formatter\\FormatterInterface');
        $formatter->expects($this->any())
            ->method('format')
            ->will($this->returnCallback(function($record) { return $record['message']; }));

        return $formatter;
    }

    public function testHandleBuffers()
    {
        $test = new TestHandler();
        $handler = new BubbleHandler($test, new \Bubble\CatchBubble(\Bubble\CatchBubble::TIMEOUT));
        $this->assertTrue($handler instanceof AbstractHandler);
        $debug = $this->getRecord(Logger::DEBUG, 'one message');
        $handler->handle($debug);
        $secondSame = $debug;
        $secondSame['datetime'] = clone $debug['datetime'];
        $secondSame['datetime']->add(new \DateInterval('PT1S'));
        $handler->handle($secondSame);
        $this->assertCount(1, $test->getRecords());
    }
    public function testHandleAnother()
    {
        $test = new TestHandler();
        $handler = new BubbleHandler($test, new \Bubble\CatchBubble(\Bubble\CatchBubble::TIMEOUT));
        $this->assertTrue($handler instanceof AbstractHandler);
        $debug = $this->getRecord(Logger::DEBUG, 'one message');
        $handler->handle($debug);
        $secondSame = $debug;
        $secondSame['message'] = 'another message';
        $secondSame['datetime'] = clone $debug['datetime'];
        $secondSame['datetime']->add(new \DateInterval('PT1S'));
        $handler->handle($secondSame);
        $this->assertCount(2, $test->getRecords());
    }
    public function testFingerPrint()
    {
        $this->assertEquals(array(
            'message' => 'one message',
            'context' => array(),
            'level' => 100,
            'level_name' => 'DEBUG',
        ), MonologBubble::fingerPrint($this->getRecord(Logger::DEBUG, 'one message')));
    }
}
