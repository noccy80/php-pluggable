<?php

namespace NoccyLabs\Pluggable\Manager\Backend;

use NoccyLabs\VirtFs\VirtFs;

/**
 * Static loading of plugins from an assoc array. Use this to load built-in
 * plugins.
 *
 *
 */
class StaticBackend implements BackendInterface
{
    protected $plugins = array();

    public function __construct(array $plugins = array())
    {
        foreach($plugins as $id=>$plugin) {
            $this->addStaticPlugin($id, $plugin);
        }
    }
    
    public function addStaticPlugin($id, $plugin_class)
    {
        if (!is_object($plugin_class)) {
            $plugin_class = new $plugin_class;
        }
        $plugin_class->setPluginId($id);
        $this->plugins[$id] = $plugin_class;
    }
    
    public function getPlugins(array $meta_readers = null)
    {
        return $this->plugins;
    }
}
