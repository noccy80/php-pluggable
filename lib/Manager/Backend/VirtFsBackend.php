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
 * Load plugins from a VirtFs virtual filesystem. The VirtFs instance should
 * be properly set up in advance, and directories and archives should be
 * mounted to the vfs prior to constructing the backend.
 *
 *
 */
class VirtFsBackend implements BackendInterface
{
    public function __construct(VirtFs $virt_fs, $root="/")
    {
        $this->virt_fs = $virt_fs;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPlugins(array $meta_readers = null)
    {
        $found = array();

        // activate plugins. note that glob doesn't glob yet, so leave out the *
        $plugins = $this->virt_fs->glob("/");
        foreach($plugins as $plugin_src) {
            if (!fnmatch("*.zip", $plugin_src)) {
                $plugin_name = basename($plugin_src);
                $plugin_meta = $this->readPluginMeta($plugin_name, $meta_readers);
                if ($plugin_meta) {
                    $plugin = $this->preparePlugin($plugin_meta, $plugin_name);
                    $id = $plugin_meta['id'];
                    $plugin->setPluginId($id);
                    $plugin->setRoot("plugins://{$plugin_name}");
                    $found[$id] = $plugin;
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
    protected function readPluginMeta($plugin_name, array $readers)
    {
        $vfs_proto = "plugins";
        $plugin_root = "{$vfs_proto}://{$plugin_name}";
        foreach($readers as $reader) {
            $ret = $reader->readPluginMeta($plugin_root);
            if ($ret) { return $ret; }
        }
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
    protected function preparePlugin($plugin_meta, $plugin_name)
    {

        // When creating the loader, we pass the VirtFs and the mountpoint to
        // operate upon, in this case the plugin name we created previous.
        $this->virt_fs->addAutoloader($plugin_meta['ns'], $plugin_name, true);

        // Now we can assemble the class name and create an instance of the actual
        // plugin.
        $plugin_class = $plugin_meta['ns'].$plugin_meta['name'];
        $plugin = new $plugin_class();
        // Return the plugin
        return $plugin;
    }
}
