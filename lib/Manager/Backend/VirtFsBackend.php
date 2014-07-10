<?php

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
    
    protected function readPluginMeta($plugin_name, array $readers)
    {
        $vfs_proto = "plugins";
        $plugin_root = "{$vfs_proto}://{$plugin_name}";
        foreach($readers as $reader) {
            $ret = $reader->readPluginMeta($plugin_root);
            if ($ret) { return $ret; }
        }
    }
    
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