<?php

namespace NoccyLabs\Pluggable\Manager\Backend;

use NoccyLabs\Pluggable\Manager\PluginManager;
use NoccyLabs\Pluggable\Plugin\Plugin;

class StaticBackendTest extends \PhpUnit_Framework_Testcase
{
    public function setup()
    {
        $this->manager = new PluginManager();
    }
    
    public function teardown()
    {}
    
    public function testCreateStaticBackend()
    {
        $backend = new StaticBackend();
        $this->manager->addBackend($backend);
    }
    
    public function testAddStaticPlugins()
    {
        $backend = new StaticBackend();
        $test_plugin = new TestPlugin();
        $backend->addStaticPlugin("test.plugin", $test_plugin);
        $backend->addStaticPlugin("test.plugin2", 'NoccyLabs\Pluggable\Manager\Backend\TestPlugin');
        $plugins = $backend->getPlugins();
        
        $this->assertEquals(2, count($plugins));
        $this->assertContains($test_plugin, $plugins);
    }
    
    public function testAddStaticPluginsFromConstructor()
    {
        $test_plugin = new TestPlugin();

        $backend = new StaticBackend(array(
            "test.plugin" => $test_plugin,
            "test.plugin2" => 'NoccyLabs\Pluggable\Manager\Backend\TestPlugin'
        ));
        $plugins = $backend->getPlugins();
        
        $this->assertEquals(2, count($plugins));
        $this->assertContains($test_plugin, $plugins);
    }
    
}

class TestPlugin extends Plugin
{
}
