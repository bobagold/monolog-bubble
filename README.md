monolog-bubble
==============

Monolog handler for limiting logging of similar records

    use Bubble\CatchBubble;
    use Bubble\MemcacheArray;
    use Bubble\Monolog\BubbleHandler;

    $log = new \Monolog\Logger(/*...*/);
    $log->pushHandler(/*...*/); // this handler will log everything

    $mailHandler = new \Monolog\Handler\NativeMailerHandler('support@example.com', 'Error report', 'noreply@example.com');
    $mailHandler->setFormatter(/*...*/);

    //$mailHandler will not pollute support mailbox with similar records more than once an hour
    $log->pushHandler(new BubbleHandler($mailHandler, new CatchBubble('PT1H', new MemcacheArray())));
    $log->pushProcessor(/*...*/);

    \Monolog\ErrorHandler::register($log);
