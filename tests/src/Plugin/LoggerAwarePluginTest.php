<?php

namespace NoccyLabs\Pluggable\Plugin;

use Psr\Log\NullLogger;

class LoggerAwarePluginTest extends \PhpUnit_Framework_TestCase
{
    public function setup()
    {}
    
    public function teardown()
    {}
    
    public function testPluginLoggerInterface()
    {
        $logger = new NullLogger();
    
        $plugin = new LoggerAwareTestPlugin();
        $plugin->setLogger( $logger );
        $this->assertEquals($logger, $plugin->getLogger());
        $plugin->setLogger( null );
        $this->assertEquals(null, $plugin->getLogger());
    }
}

class LoggerAwareTestPlugin extends LoggerAwarePlugin
{
    public function getLogger()
    {
        return $this->logger;
    }
}
