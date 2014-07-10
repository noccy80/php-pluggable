<?php

namespace NoccyLabs\Pluggable\Manager;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class PluginManagerTest extends \PhpUnit_Framework_TestCase
{
    protected $plugin;

    public function setup()
    {
        $this->manager = new PluginManager();
    }
    
    public function teardown()
    {
        $this->manager = null;
    }
    
    public function testEmptyManager()
    {
        $this->manager->findPlugins();
        $expect = array();
        
        $this->assertEquals($expect, $this->manager->getLoadedPluginIds());
    }
}
