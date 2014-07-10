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

namespace Pluggable\Manager;

use Pluggable\Loader\LoaderInterface;
use Pluggable\Loader\PluginLoader;
use Pluggable\Scanner\ScannerInterface;
use Pluggable\Scanner\PluginScanner;
use Pluggable\Plugin\PluginInterface;
use Pluggable\Persister\PersisterInterface;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class PluginManager
{
    /**
     * @var \Pluggable\Loader\PluginLoaderInterface
     */
    protected $loader;
    
    /**
     * @var \Pluggable\Scanner\PluginScannerInterface
     */
    protected $scanner;
    
    /**
     * @var array Available plugins 
     */
    protected $plugins;

    /**
     * @var \Pluggable\Persister\PersisterInterface
     */
    protected $persister;
    
    /**
     * @var array<String> Plugin paths to scan
     */
    protected $plugin_paths;
        
    protected $active;

    public function __construct()
    {
        $this->loader = new PluginLoader();
        $this->scanner = new PluginScanner();
        $this->plugin_paths = array();
        $this->active = array();
    }

    /**
     * Assign the loader to use when loading plugins.
     *
     * @param Pluggable\Loader\LoaderInterface $loader The loader instance
     * @return Pluggable\Manager\PluginManager
     */
    public function setLoader(LoaderInterface $loader=null)
    {
        $this->loader = $loader?:(new PluginLoader);
        return $this;
    }
    
    /**
     *
     * @return Pluggable\Loader\LoaderInterface The loader instance
     */
    public function getLoader()
    {
        return $this->loader;
    }
    
    /**
     * Assign the scanner to use to scan plugin manifests.
     *
     * @param Pluggable\Scanner\ScannerInterface The scanner instance
     * @return Pluggable\Manager\PluginManager
     */
    public function setScanner(ScannerInterface $scanner=null)
    {
        $this->scanner = $scanner?:(new PluginScanner);
        return $this;
    }
    
    /**
     * Get the scanner used to scan plugin manifests.
     
     * @return Pluggable\Scanner\ScannerInterface The scanner instance
     */
    public function getScanner()
    {
        return $this->scanner;
    }

    /**
     *
     * @param \Pluggable\Persister\PersisterInterface The persister instance
     * @return \Pluggable\Manager\PluginManager
     */
    public function setPersister(PersisterInterface $persister=null)
    {
        $this->persister = $persister;
        return $this;
    }
    
    /**
     *
     * @return Pluggable\Persister\PersisterInterface The persister instance
     */
    public function getPersister()
    {
        return $this->persister;
    }

    
    public function addPath($path, $prepend=false)
    {
        if (!is_dir($path)) { return $this; }
        $path = realpath($path);
        if ($prepend) {
            array_unshift($path, $this->plugin_paths);
        } else {
            $this->plugin_paths[] = $path;
        }
        return $this;
    }
    
    /**
     * Scan for plugins in the configured paths.
     * 
     * 
     */
    public function scan()
    {
        // Scan for plugins
        $found_plugins = array();
        foreach($this->plugin_paths as $path) {
            // Scan each of the directories
            $plugins = $this->scanner->scanDirectory($path);
            $found_plugins = array_merge($found_plugins,$plugins);
        }
        
        // Set the plugins
        $this->plugins = $found_plugins;
        
        foreach($this->plugins as $plugin) {
            $plugin->setManager($this);
        }
        
        // If a persister is assigned, ask it for the list of active plugins.
        if ($this->persister) {
            // Get the list
            $active = $this->persister->getActivePlugins();
            // Activate each of the plugins
            foreach($active as $plugin) {
                $this->activatePlugin($plugin);
            }
        }
    }
    
    /**
     * Save the active plugins to the configured persister.
     * 
     * @throws \BadMethodCallException
     */
    public function save()
    {
        if (!$this->persister) {
            throw new \BadMethodCallException("Assign a persister before calling Manager#save");
        }
        $active = array();
        foreach($this->plugins as $plugin) {
            if ($plugin->isActive()) {
                $active[] = $plugin->getId();
            }
        }
        $this->persister->setActivePlugins($active);
    }
    
    public function getPlugin($plugin_id)
    {
        if (array_key_exists($plugin_id, $this->plugins)) {
            return $this->plugins[$plugin_id];
        }
        return null;
    }
    
    public function getAvailablePlugins()
    {
        return $this->plugins;
    }
    
    public function activatePlugin($plugin_id)
    {
        if (array_key_exists($plugin_id, $this->plugins)) {
            $this->plugins[$plugin_id]->activate();
        }
    }

    public function deactivatePlugin($plugin_id)
    {
        if (array_key_exists($plugin_id, $this->plugins)) {
            $this->plugins[$plugin_id]->deactivate();
        }
    }

}
