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

namespace NoccyLabs\Pluggable\Manager\Backend;

use NoccyLabs\VirtFs\VirtFs;

/**
 * Load plugins directly from a list of specified directories.
 *
 */
class DirectoryBackend implements BackendInterface
{
    protected $paths = array();

    public function __construct($path=null)
    {
        if ($path) {
            $this->addPath($path);
        }
    }
    
    /**
     * Add a filesystem path to the backend.
     *
     * @param string The path to add
     */
    public function addPath($path)
    {
        $this->paths[] = $path;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPlugins(array $meta_readers = null)
    {
        $found = array();

        $plugins = array();
        foreach($this->paths as $path) {
            $paths = glob($path.'/*');
            $plugins = array_merge($plugins, $paths);
        }
        foreach($plugins as $plugin_src) {
            if (!fnmatch("*.zip", $plugin_src)) {
                $plugin_meta = $this->readPluginMeta($plugin_src, $meta_readers);
                if ($plugin_meta) {
                    $plugin = $this->preparePlugin($plugin_meta, $plugin_src);
                    if ($plugin) {
                        $id = $plugin_meta['id'];
                        $plugin->setMetaData($plugin_meta);
                        $plugin->setPluginId($id);
                        $plugin->setRoot($plugin_src);
                        $found[$id] = $plugin;
                    }  else {
                        $msg = sprintf("The plugin in %s could not be loaded", $plugin_src);
                        trigger_error($msg);
                    }
                }
            }
        }
        
        return $found;
    }

    /**
     * Read metadata from all readers until we get a proper result.
     *
     * @internal
     * @param string The plugin name 
     * @param array The metadata readers readers to test
     * @return array|null Parsed metadata if any
     */
    protected function readPluginMeta($plugin_root, array $readers)
    {
        $vfs_proto = "plugins";
        foreach($readers as $reader) {
            try {
                $ret = $reader->readPluginMeta($plugin_root);
                if ($ret) { return $ret; }
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }
    
    /**
     * Prepare the plugin for using by registering autoloader and creating a
     * new instance.
     *
     * @internal
     * @param array The plugin metadata
     * @param string The plugin name 
     * @return NoccyLabs\Pluggable\Plugin\PluginInterface Loaded plugin
     */
    protected function preparePlugin($plugin_meta, $plugin_root)
    {
        \spl_autoload_register( function($class) use ($plugin_meta, $plugin_root) {
            $ns = $plugin_meta['ns'];
            if (strncmp($class, $ns, strlen($ns)) === 0) {
                $plugin_file = $plugin_root.strtr(str_replace(rtrim($ns,"\\"), "", $class),"\\","/").".php";
                if (file_exists($plugin_file)) {
                    require_once $plugin_file;
                    return true;
                } else {
                    trigger_error("Warning: Plugin class file {$plugin_file} not found");
                }
            }
        });
        
    
        // Now we can assemble the class name and create an instance of the actual
        // plugin, or throw an exception otherwise
        $plugin_class = $plugin_meta['class'];
        if (!class_exists($plugin_class)) {
            return false;
        }
        $plugin = new $plugin_class();
        // Return the plugin
        return $plugin;
    }

}
