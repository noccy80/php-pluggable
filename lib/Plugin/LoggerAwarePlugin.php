<?php

namespace NoccyLabs\Pluggable\Plugin;

use Psr\Log\LoggerAwareInterface,
    Psr\Log\LoggerInterface;

class LoggerAwarePlugin extends Plugin implements LoggerAwareInterface
{
    protected $logger;

    public function setLogger( LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

}
