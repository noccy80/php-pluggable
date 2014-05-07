<?php

namespace Pluggable\Plugin;

abstract class Plugin implements PluginInterface
{
    public function getId()
    {
        return str_replace("\\",".",strtolower(get_called_class()));
    }

    public function activate()
    {
        return true;
    }
    
    public function deactivate()
    {
        return true;
    }
}
