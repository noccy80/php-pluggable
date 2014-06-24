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

namespace Pluggable\Scanner;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Pluggable\Manager\PluginInstance;
use Pluggable\Manager\Manager;

/**
 * Scans for plugins.
 * 
 * 
 */
class PluginScanner implements ScannerInterface
{
    protected $manager;
    
    public function scanDirectory($path)
    {
        $finder = new Finder();
        $finder->files()->name("plugin.yml")->in($path);

        $plugins = array();
        
        foreach($finder as $found) {
            $plugin_conf = $found->getPathName();
            $conf_data = file_get_contents($plugin_conf);
            try {
                $conf = Yaml::parse($conf_data);
                if (empty($conf) || empty($conf['plugin'])) {
                    //throw new \Exception("Invalid manifest (missing plugin section)");
                } else {
                    $plugins[$plugin_conf] = $conf;
                }
            } catch (\Exception $e) {
                // throw new \Exception("Parse error in manifest {$plugin_conf}", 0, $e);
            }

        }
        
        $plugins = $this->scanPlugins($plugins);
        
        return $plugins;
    }

    /**
     * Scans the plugins 
     * 
     * @param array $plugins
     */
    protected function scanPlugins(array $plugins)
    {
        $found = array();
        foreach($plugins as $config=>$plugin) {
            $plugin_root = dirname($config);
            try {
                $plugin_obj = $this->readPlugin($plugin_root, $plugin);
                if ($plugin_obj) {
                    $plugin_id = $plugin_obj->getId();
                    $found[$plugin_id] = $plugin_obj;
                }
            } catch (\Exception $e) {
            
            }
        }
        return $found;
    }

    /**
     * 
     * 
     * @param type $root
     * @param array $config
     * @return \Pluggable\Manager\PluginInstance|boolean
     */
    protected function readPlugin($root,array $config) {

        $plugin_conf = $config['plugin'];

        foreach(array('id','name','version','author','class','autoload') as $key) {
            if (!array_key_exists($key,$plugin_conf)) { 
                error_log("Warning: {$root}: Plugin config is missing a required key: {$key}");
                return false;
            }
        }
        
        // Define autoloader
        $plugin_class = $plugin_conf['class'];
        if (!class_exists($plugin_class)) {
            $autoload_ns = rtrim($plugin_conf['autoload'],"\\")."\\";
            spl_autoload_register(function($class) use($root,$autoload_ns) {
                if (strncmp($autoload_ns,$class,strlen($autoload_ns)) === 0) {
                    $filename = $root."/".str_replace("\\","/",substr($class,strlen($autoload_ns))).".php";
                    if (file_exists($filename)) {
                        require_once $filename;
                        return true;
                    }
                }
            });
        }
        
        // Check if the plugin is loadable
        if (!class_exists($plugin_class)) {
            $plugin_name = $plugin_conf['name'];
            error_log("Error: Unable to find the class {$plugin_class} needed for {$plugin_name}");
            return false;
        }
        
        // Create a new instance of the plugin class
        $plugin_inst = new $plugin_class();
        if (!empty($plugin_conf['depends'])) {
            $dependencies = $plugin_conf['depends'];
        } else {
            $dependencies = null;
        }
        
        // Create a wrapper instance to add to the manager
        $plugin = new PluginInstance();
        $plugin
            ->setName($plugin_conf['name'])
            ->setId($plugin_conf['id'])
            ->setAuthor($plugin_conf['author'])
            ->setVersion($plugin_conf['version'])
            ->setPluginInstance($plugin_inst)
            ->setDependencies($dependencies)
            ->setPluginPath($root);
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
    
    
}
