<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class CustomService
{
 private LoggerInterface $customLogger;

 public function __construct(LoggerInterface $customLogger)
 {
 $this->customLogger = $customLogger;
 }

 public function logCustomMessage(): void
 {
 $this->customLogger->debug('This is custom log');
 }
}
