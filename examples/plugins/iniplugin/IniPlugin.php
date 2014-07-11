<?php

namespace Plugins\IniPlugin;

class IniPlugin extends \NoccyLabs\Pluggable\Plugin\Plugin
{
    // load is called from Plugin#onActivate if not overridden.
    public function load()
    {
        echo "This is ".__CLASS__." loading from ".__FILE__."\n";
        echo "Root is {$this->root}\n";
        $tattoo = trim(file_get_contents($this->root."/tattoo.txt"));
        echo "My tattoo says {$tattoo}\n";
        echo "My greeting is {$this->meta['greeting']['string']}\n";
    }
}
