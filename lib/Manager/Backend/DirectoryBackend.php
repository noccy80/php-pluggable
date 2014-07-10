<?php

namespace NoccyLabs\Pluggable\Manager\Backend;

use NoccyLabs\VirtFs\VirtFs;

class DirectoryBackend implements BackendInterface
{
    protected $paths = array();

    public function __construct($path=null)
    {
        if ($path) {
            $this->addPath($path);
        }
    }
    
    public function addPath($path)
    {
        $this->paths[] = $path;
    }
}
