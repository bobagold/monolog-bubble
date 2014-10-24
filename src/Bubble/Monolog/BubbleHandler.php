<?php


namespace Bubble\Monolog;

use Monolog\Handler\AbstractHandler;
use Monolog\Handler\HandlerInterface;

class BubbleHandler extends AbstractHandler
{
    private $handler;
    private $catchBubble;
    public function __construct(AbstractHandler $handler, \Bubble\CatchBubble $catchBubble)
    {
        parent::__construct($handler->getLevel(), $handler->getBubble());
        $this->handler = $handler;
        $this->catchBubble = $catchBubble;
    }

    public function handle(array $record)
    {
        if ($record['level'] < $this->level || $record['context']['just_log']) {
            return false;
        }
        $bubble = \Bubble\Monolog\MonologBubble::monolog2bubble($record);
        $stored = $this->catchBubble->getBubble($bubble['record'], $bubble['details']);
        if ($stored) {
            $record['extra']['repetitions'] = $stored['repetitions'];
            $this->handler->handle($record);
            return false === $this->bubble;
        }
        return false;
    }
}