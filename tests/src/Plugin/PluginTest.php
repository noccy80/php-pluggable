<?php

namespace NoccyLabs\Pluggable\Plugin;

class PluginTest extends \PhpUnit_Framework_TestCase
{
    public function setup()
    {
        $this->plugin = new TestPlugin();
        $this->plugin->setMetaData(
            array(
                "id"        => "test.id",
                "name"      => "test.name",
                "version"   => "test.version",
                "author"    => "test.author"
            )
        );
        $this->plugin_empty = new TestPlugin();
    }
    
    public function teardown()
    {}
    
    public function testPluginMetaDataFields()
    {
        $this->assertEquals("test.id", $this->plugin->getPluginId());
        $this->assertEquals("test.name", $this->plugin->getPluginName());
        $this->assertEquals("test.version", $this->plugin->getPluginVersion());
        $this->assertEquals("test.author", $this->plugin->getPluginAuthor());
        
        $this->assertEquals(null, $this->plugin_empty->getPluginId());
        $this->assertEquals(null, $this->plugin_empty->getPluginName());
        $this->assertEquals(null, $this->plugin_empty->getPluginVersion());
        $this->assertEquals(null, $this->plugin_empty->getPluginAuthor());
    }
    
    public function testPluginActivation()
    {
        $this->assertNotTrue($this->plugin->was_load_called);
        $this->assertNotTrue($this->plugin->isActivated());
        $this->plugin->onActivate();
        $this->assertTrue($this->plugin->was_load_called);
        $this->assertTrue($this->plugin->isActivated());
        $this->plugin->onDeactivate();
        $this->assertNotTrue($this->plugin->isActivated());
        $this->assertTrue($this->plugin->was_load_called);
    }
    
    public function testPluginSetRoot()
    {
        $this->assertNull($this->plugin->getRoot());
        $this->plugin->setRoot("/tmp");
        $this->assertEquals("/tmp", $this->plugin->getRoot());
    }
}

class TestPlugin extends Plugin
{
    public $was_load_called = false;
    
    public function load()
    {
        $this->was_load_called = true;
    }
    
    public function getRoot()
    {
        return $this->root;
    }
}
