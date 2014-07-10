<?php

namespace Plugins\BazPlugin;

class BazPlugin extends \NoccyLabs\Pluggable\Plugin\Plugin
{
    public function onActivate()
    {
        parent::onActivate();
        echo "This is ".__CLASS__." loading from ".__FILE__."\n";
        echo "Root is {$this->root}\n";
        $tattoo = trim(file_get_contents($this->root."/tattoo.txt"));
        echo "My tattoo says {$tattoo}\n";
    }
}
