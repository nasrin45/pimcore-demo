<?php

namespace App;

use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;

class LoggerService
{
    /**
     * @var ApplicationLogger
     */
    private $logger;

    public function __construct(ApplicationLogger $logger)
    {
        $this->logger = $logger;
        $logger->debug('Hello from YourService');
    }

}
