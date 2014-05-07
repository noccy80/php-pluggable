<?php

namespace Pluggable\Manager\Loader;

use Pluggable\Plugin\PluginInterface;

class PluginLoader implements PluginLoaderInterface
{
    public function loadPlugin(PluginInterface $plugin)
    {
        $plugin->activate();
        return $plugin;
    }
}
