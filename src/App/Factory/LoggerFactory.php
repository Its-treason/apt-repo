<?php

namespace ItsTreason\AptRepo\App\Factory;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;

class LoggerFactory
{
    public function __invoke(): Logger
    {
        $handler = new StreamHandler(fopen('php://stdout', 'w'));
        $handler->setFormatter(new LineFormatter());

        return new Logger(
            'apt-repo',
            [$handler],
            [new IntrospectionProcessor(), new WebProcessor()],
        );
    }
}
