<?php

namespace Pluggable\Plugin;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ContainerAwarePluginTest extends \PhpUnit_Framework_TestCase
{
    protected $plugin;

    public function setup()
    {
        $this->plugin = new ContainerAwareTestPlugin();
    }
    
    public function teardown()
    {}

    public function testContainer()
    {
        $cb = new ContainerBuilder();
        $this->plugin->setContainer($cb);
        $cr = $this->plugin->getContainer();
        $this->assertEquals($cr,$cb);
    }

}
