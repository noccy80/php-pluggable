<?php

/*
 * Copyright (C) 2014, NoccyLabs
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

namespace NoccyLabs\Pluggable\Manager;

use NoccyLabs\Pluggable\Manager\Backend\BackendInterface;
use NoccyLabs\Pluggable\Plugin\PluginInterface;
use NoccyLabs\Pluggable\Manager\MetaReader\JsonMetaReader;
use NoccyLabs\Pluggable\Manager\MetaReader\YamlMetaReader;
use NoccyLabs\Pluggable\Manager\MetaReader\IniMetaReader;
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
        $this->meta_readers[] = new IniMetaReader();
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
            if (($plugin instanceof $interface_name) 
                || fnmatch($interface_name, get_class($plugin))) {
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
    public function findPlugins($load=false, array $meta_readers=null)
    {
        $found_plugins = array();
        
        if (!$meta_readers) {
            $meta_readers = $this->meta_readers;
        }
    
        // Assemble a list of plugins, if a plugin exists in more than one
        // location, the one that was found last will take precedence.
        foreach($this->backends as $backend)
        {
            $plugins = $backend->getPlugins($meta_readers);
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
    
    public function getPlugin($id)
    {
        foreach($this->plugins as $plugin) {
            if ($plugin->getPluginId() == $id) {
                return $plugin;
            }
        }
        return false;
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

    public function getLoadedPlugins()
    {
        $loaded = array();
        foreach($this->plugins as $plugin) {
            if ($plugin->isActivated()) {
                $loaded[] = $plugin;
            }
        }
        return $loaded;
    }

    public function getAllPlugins()
    {
        return $this->plugins;
    }

}
