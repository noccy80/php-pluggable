<?php

namespace Plugins\XyzzyPlugin;

class XyzzyPlugin extends \NoccyLabs\Pluggable\Plugin\Plugin
    implements \Symfony\Component\DependencyInjection\ContainerAwareInterface
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        echo "Container set in ".__CLASS__."!\n";
    }

    // load is called from Plugin#onActivate if not overridden.
    public function load()
    {
        echo "This is ".__CLASS__." loading from ".__FILE__."\n";
        echo "Root is {$this->root}\n";
        $tattoo = trim(file_get_contents($this->root."/tattoo.txt"));
        echo "My tattoo says {$tattoo}\n";
    }
}
