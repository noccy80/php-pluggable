<?php

namespace NoccyLabs\Pluggable\Manager\Backend;

use NoccyLabs\Pluggable\Manager\PluginManager;
use NoccyLabs\Pluggable\Plugin\Plugin;

class DirectoryBackendTest extends \PhpUnit_Framework_Testcase
{
    public function setup()
    {
        $this->manager = new PluginManager();
    }
    
    public function teardown()
    {}
    
    public function testCreateDirectoryBackend()
    {
        $backend = new DirectoryBackend();
        $this->manager->addBackend($backend);
    }
    
    public function testCreateDirectoryBackendWithConstructor()
    {
        $backend = new DirectoryBackend( __DIR__."/../../../data");
        $this->manager->addBackend($backend);
        
        $this->manager->findPlugins(true);
        
        
    }

    public function testFindPluginsFromBackend()    
    {
        
    }
    
    public function testThatErrorMessagesAreLogged()
    {
        $backend = new DirectoryBackend( __DIR__."/../../../data");
        $this->manager->addBackend($backend);
        
        $this->manager->findPlugins(true);
        $this->assertEquals(4, count((array)$backend->getErrors()));
    }
    
}

