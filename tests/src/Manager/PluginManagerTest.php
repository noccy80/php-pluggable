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
    
    /**
     * @expectedException NoccyLabs\Pluggable\Manager\Exception\PluggableException
     */
    public function testAddBackendTwice()
    {
        $backend = new Backend\StaticBackend(array(
            "test.plugin" => new TestPlugin()
        ));
        $this->manager->addBackend($backend);
        $this->manager->addBackend($backend);
    }
    
    /**
     * @expectedException NoccyLabs\Pluggable\Manager\Exception\PluggableException
     */
    public function testBackendReturningInvalidPlugin()
    {
        $backend = new Backend\StaticBackend(array(
            "bad" => new \SplQueue()
        ));
        $this->manager->addBackend($backend);
        $this->manager->findPlugins(true);
    }

    /**
     * @expectedException NoccyLabs\Pluggable\Manager\Exception\PluggableException
     */
    public function testBackendReturningInvalidPluginId()
    {
        $backend = new BadIdMockBackend();
        $this->manager->addBackend($backend);
        $this->manager->findPlugins(true);
    }
    
    public function testSingleBackendAlwaysLoad()
    {
        $backend = new Backend\StaticBackend(array(
            "test.plugin" => new TestPlugin()
        ));
        $this->manager->addBackend($backend);
        $this->manager->findPlugins(true);

        $plugin = $this->manager->getPlugin("test.plugin");
        $this->assertNotNull($plugin);
        $this->assertInstanceOf('NoccyLabs\Pluggable\Plugin\PluginInterface', $plugin);
        $this->assertEquals("test.plugin", $plugin->getPluginId());
        $this->assertFalse($this->manager->getPlugin("nonexisting.plugin.id"));
        $this->assertTrue($plugin->isActivated());
        
        $plugin_ids = $this->manager->getLoadedPluginIds();
        $this->assertContains("test.plugin", $plugin_ids);
        $loaded_plugins = $this->manager->getLoadedPlugins();
        $this->assertEquals(1, count($loaded_plugins));
        $this->assertContains($plugin, $loaded_plugins);
        $all_plugins = $this->manager->getAllPlugins();
        $this->assertEquals(1, count($all_plugins));

    }    

    public function testSingleBackendNeverLoad()
    {
        $backend = new Backend\StaticBackend(array(
            "test.plugin" => new TestPlugin()
        ));
        $this->manager->addBackend($backend);
        $this->manager->findPlugins(false);

        $plugin = $this->manager->getPlugin("test.plugin");
        $this->assertNotNull($plugin);
        $this->assertInstanceOf('NoccyLabs\Pluggable\Plugin\PluginInterface', $plugin);
        $this->assertEquals("test.plugin", $plugin->getPluginId());
        $this->assertFalse($this->manager->getPlugin("nonexisting.plugin.id"));
        $this->assertFalse($plugin->isActivated());
        
        $plugin_ids = $this->manager->getLoadedPluginIds();
        $this->assertEquals(0, count($plugin_ids));
        $loaded_plugins = $this->manager->getLoadedPlugins();
        $this->assertEquals(0, count($loaded_plugins));
        $all_plugins = $this->manager->getAllPlugins();
        $this->assertEquals(1, count($all_plugins));

    }    

    public function testSingleBackendWithLoadCallback()
    {
        $backend = new Backend\StaticBackend(array(
            "test.plugin" => new TestPlugin()
        ));
        $this->manager->addBackend($backend);
        $this->manager->findPlugins(array($this,"loader_func"));
        
        $plugin = $this->manager->getPlugin("test.plugin");
        $this->assertNotNull($plugin);
        $this->assertInstanceOf('NoccyLabs\Pluggable\Plugin\PluginInterface', $plugin);
        $this->assertEquals("test.plugin", $plugin->getPluginId());
        $this->assertFalse($this->manager->getPlugin("nonexisting.plugin.id"));
        $this->assertTrue($plugin->isActivated());
        
        $plugin_ids = $this->manager->getLoadedPluginIds();
        $this->assertContains("test.plugin", $plugin_ids);
        $loaded_plugins = $this->manager->getLoadedPlugins();
        $this->assertEquals(1, count($loaded_plugins));
        $this->assertContains($plugin, $loaded_plugins);
        $all_plugins = $this->manager->getAllPlugins();
        $this->assertEquals(1, count($all_plugins));
    }    
    
    public function testPluginGenericLoader()
    {
        $backend = new Backend\StaticBackend(array(
            "test.plugin" => new TestPlugin()
        ));
        $this->manager->addBackend($backend);
        $this->manager->addLoader(function($plugin,$manager) {
            $plugin->data = "bar";
        });
        $this->manager->findPlugins(true);
        
        $plugin = $this->manager->getPlugin("test.plugin");
        $this->assertEquals("bar", $plugin->data);
    }

    public function testPluginInterfaceLoader()
    {
        $backend = new Backend\StaticBackend(array(
            "fooplugin" => new TestPlugin(),
            "barplugin" => new TestPlugin2(),
        ));
        $this->manager->addBackend($backend);
        $this->manager->addInterfaceLoader(
            'NoccyLabs\Pluggable\Manager\BarInterface', 
            function($plugin,$manager) {
                $plugin->data = "bar";
            }
        );
        $this->manager->findPlugins(true);
        
        $plugin = $this->manager->getPlugin("fooplugin");
        $this->assertEquals(null, $plugin->data);
        $plugin = $this->manager->getPlugin("barplugin");
        $this->assertEquals("bar", $plugin->data);
    }
    
    public function loader_func($plugin)
    {
        return true;
    }
    
}

class TestPlugin extends \NoccyLabs\Pluggable\Plugin\Plugin
{
    public $data = null;
}

class TestPlugin2 extends TestPlugin implements BarInterface
{
    public $data = null;
}

interface BarInterface
{}

class BadIdMockBackend implements \NoccyLabs\Pluggable\Manager\Backend\BackendInterface
{
    public function getPlugins(array $meta_readers = null)
    {
        return array( "" => new TestPlugin() );
    }
}
