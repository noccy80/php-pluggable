<?php

namespace Pluggable\Scanner;

use Pluggable\Manager\Manager;

class PluginScannerTest extends \PhpUnit_Framework_TestCase
{
    protected $scanner;
    
    protected $manager;

    public function setup()
    {
        $this->manager = new Manager();
        $this->scanner = new PluginScanner();
    }
    
    public function teardown()
    {}
    
    public function testScan()
    {
        $plugins = $this->scanner->scanDirectory($this->manager, __DIR__."/../../../data");
        $this->assertEquals(true, is_array($plugins));
        $this->assertEquals(1, count($plugins));
        $this->assertNotNull($plugins["plugin.proper"]);
    }
    
}
