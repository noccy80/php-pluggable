<?php

namespace NoccyLabs\Pluggable\Manager;

use NoccyLabs\Pluggable\Manager\Backend\BackendInterface;
use NoccyLabs\Pluggable\Plugin\PluginInterface;
use NoccyLabs\Pluggable\Manager\MetaReader\JsonMetaReader;
use NoccyLabs\Pluggable\Manager\MetaReader\YamlMetaReader;
use NoccyLabs\Pluggable\Manager\MetaReader\MetaReaderInterface;

class PluginManager
{
    /** @var array Callbacks to load various interfaces; name as key */
    protected $interface_loaders = array();

    /** @var array<BackendInterface> The backends to use when finding/loading plugins */
    protected $backends = array();

    /** @var array<Plugin> The plugins found/available */
    protected $plugins = array();
    
    protected $meta_readers = array();

    public function __construct()
    {
        $this->meta_readers[] = new JsonMetaReader();
        $this->meta_readers[] = new YamlMetaReader();
    }

    /**
     * Add a new backend to scan and load plugins.
     *
     */
    public function addBackend(BackendInterface $backend)
    {
        // Throw exception if already added
        if (in_array($backend, $this->backends)) {
            throw new \RuntimeException("Backend already registered with plugin manager");
        }
        // Add to list of backends
        $this->backends[] = $backend;
        
        return $this;
    }

    /**
     * Add a loader to activate for a specific interface.
     *
     *
     */
    public function addInterfaceLoader($iface_name, callable $loader_callback)
    {
        if (!array_key_exists($iface_name, $this->interface_loaders)) {
            $this->interface_loaders[$iface_name] = array();
        }
        $this->interface_loaders[$iface_name][] = $loader_callback;

        return $this;
    }
    
    /**
     * Call the applicable loader callbacks with the plugin and the manager
     * as its parameters.
     *
     *
     */
    protected function runInterfaceLoaders(PluginInterface $plugin)
    {
        foreach($this->interface_loaders as $interface_name => $callbacks) {
            if ($plugin instanceof $interface_name) {
                foreach($callbacks as $callback) {
                    call_user_func($callback, $plugin, $this);
                }
            }
        }
    }
    
    /**
     *
     * If $load is false, no plugins are loaded. If $load is true, all plugins
     * are loaded. This is the same as passing a callable that returns true or
     * false after receiving the plugin.
     *
     * NOTE: Calling this method will reset the state of any loaded or found plugins!
     *
     * @param bool|callable Boolean or callback to return true if the plugin should be loaded
     */
    public function findPlugins($load=false)
    {
        $found_plugins = array();
    
        // Assemble a list of plugins, if a plugin exists in more than one
        // location, the one that was found last will take precedence.
        foreach($this->backends as $backend)
        {
            $plugins = $backend->getPlugins($this->meta_readers);
            foreach((array)$plugins as $plugin) {
                if (!($plugin instanceof PluginInterface)) {
                    throw new \Exception("BackendInterface#getPlugins() should only return PluginInterface derivatives");
                }
                $id = $plugin->getPluginId();
                $found_plugins[$id] = $plugin;
            }
        }
        
        $this->plugins = $found_plugins;
        
        // Go over the final list of plugins, and prepare them for operation.
        foreach($found_plugins as $id=>$plugin) {
            if (!$id) {
                throw new \RuntimeException("Plugin id can not be null");
            }
            $plugin->setPluginId($id);
            $this->setupPlugin($plugin);

            // If load is a callback, call it to determine if the plugin should
            // be loaded
            if (is_callable($load)) {
                $should_load = (bool)call_user_func($load, $id, $plugin);
            } else {
                $should_load = (bool)$load;
            }

            // And load it if it should be
            if ($should_load) {
                $this->loadPlugin($plugin);
            }
        }
    
         return $this;
    }
    
    protected function setupPlugin(PluginInterface $plugin)
    {
        $this->runInterfaceLoaders($plugin);
    }
    
    public function loadPlugin(PluginInterface $plugin)
    {
        $plugin->onActivate();
    }
    
    public function getLoadedPluginIds()
    {
        $loaded = array();
        foreach($this->plugins as $plugin) {
            if ($plugin->isActivated()) {
                $loaded[] = $plugin->getPluginId();
            }
        }
        return $loaded;
    }
}