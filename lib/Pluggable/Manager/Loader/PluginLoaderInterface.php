<?php

namespace Pluggable\Manager\Loader;

use Pluggable\Plugin\PluginInterface;

interface PluginLoaderInterface
{
    public function loadPlugin(PluginInterface $plugin);
}
