<?php

namespace NoccyLabs\Pluggable\Manager\MetaReader;

class IniMetaReaderTest extends \PhpUnit_Framework_TestCase
{
    private $reader;

    public function setup()
    {
        $this->reader = new IniMetaReader();
    }
    
    public function teardown()
    {}
    
    public function testReadData()
    {
        $read = $this->reader->readPluginMeta( __DIR__ . "/../../../data/plugin.ini" );

        foreach(array("id", "name", "class", "ns", "author", "extra_user_data") as $key) {
            $this->assertArrayHasKey($key, $read);
        }
    }
    
    public function testInvalidRead()
    {
        $read = $this->reader->readPluginMeta( __DIR__ . "/../../../data/plugin.ini.nonexisting" );
        $this->assertFalse($read);
    }

    /**
     * @expectedException NoccyLabs\Pluggable\Manager\Exception\BadManifestException
     */
    public function testInvalidData()
    {
        $read = $this->reader->readPluginMeta( __DIR__ . "/../../../data/plugin.ini.invalid" );
    }

}

