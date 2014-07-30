<?php

namespace NoccyLabs\Pluggable\Plugin;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ContainerAwarePluginTest extends \PhpUnit_Framework_TestCase
{
    public function setup()
    {}
    
    public function teardown()
    {}
    
    public function testPluginContainerInterface()
    {
        $container = new ContainerBuilder();
    
        $plugin = new ContainerAwareTestPlugin();
        $plugin->setContainer( $container );
        $this->assertEquals($container, $plugin->getContainer());
        $plugin->setContainer( null );
        $this->assertEquals(null, $plugin->getContainer());
    }
}

class ContainerAwareTestPlugin extends ContainerAwarePlugin
{
    public function getContainer()
    {
        return $this->container;
    }
}
