<?php

namespace Pluggable\Plugin;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class PluginTest extends \PhpUnit_Framework_TestCase
{
    protected $plugin;

    public function setup()
    {
        $this->plugin = new TestPlugin();
    }
    
    public function teardown()
    {}
    
    public function testGetId()
    {
        $id = $this->plugin->getId();
        $this->assertEquals($id,"pluggable.plugin.testplugin");
    }
    
    public function testActivate()
    {
        $act = $this->plugin->activate();
        $this->assertEquals($act,true);
    }

}
