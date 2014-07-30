<?php

namespace NoccyLabs\Pluggable\Manager\Backend;

use NoccyLabs\Pluggable\Manager\PluginManager;
use NoccyLabs\Pluggable\Plugin\Plugin;
use NoccyLabs\VirtFs\VirtFs;

class VirtFsBackendTest extends \PhpUnit_Framework_Testcase
{
    public function setup()
    {
        $this->manager = new PluginManager();
    }
    
    public function teardown()
    {}
    
    public function testCreateVirtfsBackend()
    {
        $vfs = new VirtFs();
        $backend = new VirtFsBackend($vfs);
        $plugins = $backend->getPlugins();
        $this->assertNotNull($plugins);
        $this->assertEquals(0, count($plugins));
    }

    public function testCreateVirtfsBackendWithDirectory()
    {
        $vfs = new VirtFs("plugins");
        $vfs->addDirectory(__DIR__ . "/../../../data");
        $backend = new VirtFsBackend($vfs);
        $plugins = $backend->getPlugins($this->manager->getDefaultMetaReaders());
        $this->assertNotNull($plugins);
    }
    
    
}

