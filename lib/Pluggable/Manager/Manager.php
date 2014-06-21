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

class Manager
{
    /**
     * @var Pluggable\Manager\Loader\PluginLoaderInterface
     */
    protected $loader;
    
    /**
     * @var Pluggable\Manager\Scanner\PluginScannerInterface
     */
    protected $scanner;
    
    /**
     * @var array Available plugins 
     */
    protected $plugins;
    
    /**
     * @var array<String> Plugin 
     */
    protected $plugin_paths;
    
    protected $persister;
    
    protected $active;

    public function __construct()
    {
        $this->loader = new PluginLoader();
        $this->scanner = new PluginScanner();
        $this->plugin_paths = array();
        $this->active = array();
    }

    public function setLoader(LoaderInterface $loader)
    {
        $this->loader = $loader;
        return $this;
    }
    
    public function getLoader()
    {
        return $this->loader;
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
    
    public function scan()
    {
        $plugins = array();
        foreach($this->plugin_paths as $path) {
            $plugins = array_merge($plugins,$this->scanner->scanDirectory($path));
        }
        
        $this->scanPlugins($plugins);
        if ($this->persister) {
            $active = $this->persister->getActivePlugins();
            foreach($active as $plugin) {
                $this->activatePlugin($plugin);
            }
        }
    }
    
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
    
    protected function scanPlugins(array $plugins)
    {
        $this->plugins = array();
        foreach($plugins as $config=>$plugin) {
            $plugin_root = dirname($config);
            $plugin_obj = $this->readPlugin($plugin_root, $plugin);
            if ($plugin_obj) {
                $this->plugins[$plugin_obj->getId()] = $plugin_obj;
            }
        }
    }
    
    protected function readPlugin($root,array $config) {

        $plugin_conf = $config['plugin'];

        foreach(array('id','name','version','author','class','autoload') as $key) {
            if (!array_key_exists($key,$plugin_conf)) { 
                return false;
            }
        }
        $autoload_ns = rtrim($plugin_conf['autoload'],"\\")."\\";
        $plugin_class = $plugin_conf['class'];
        spl_autoload_register(function($class) use($root,$autoload_ns) {
            if (strncmp($autoload_ns,$class,strlen($autoload_ns)) === 0) {
                $filename = $root."/".str_replace("\\","/",substr($class,strlen($autoload_ns))).".php";
                if (file_exists($filename)) {
                    require_once $filename;
                    return true;
                }
            }
        });
        if (!class_exists($plugin_class)) {
            $plugin_name = $plugin_conf['name'];
            error_log("Error: Unable to find the class {$plugin_class} needed for {$plugin_name}");
            return false;
        }
        $plugin_inst = new $plugin_class();
        if (!empty($plugin_conf['depends'])) {
            $dependencies = $plugin_conf['depends'];
        } else {
            $dependencies = null;
        }
        $plugin = new PluginInstance();
        $plugin
            ->setName($plugin_conf['name'])
            ->setAuthor($plugin_conf['author'])
            ->setVersion($plugin_conf['version'])
            ->setPluginInstance($plugin_inst)
            ->setDependencies($dependencies)
            ->setPluginPath($root)
            ->setManager($this);
        if (!empty($plugin_conf['description'])) {
            $descr = $plugin_conf['description'];
            $descr_para = explode("\n\n",$descr);
            $descr_clean = array();
            foreach($descr_para as $para) {
                $lines = array_map("trim",explode("\n",$para));
                $descr_clean[] = join(" ",$lines);
                
            }
            $plugin->setDescription(join("\n",$descr_clean));
        }
        return $plugin;
    
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

    public function setPersister(PersisterInterface $persister=null)
    {
        $this->persister = $persister;
        return($this);
    }
    
    public function getPersister()
    {
        return $this->persister;
    }

}
