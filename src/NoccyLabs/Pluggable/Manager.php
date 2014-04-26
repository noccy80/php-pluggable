<?php

namespace NoccyLabs\Pluggable;

use Symfony\Component\Yaml\Yaml;

/**
 * Plugin manager
 *
 *
 */
class Manager implements \ArrayAccess
{

    /**
     * @var string[] The paths to scan for plugins in ascending order of importance.
     */
    protected $search_paths = array();
    
    /**
     * @var string The path to where plugins persistent data is written.
     */
    protected $data_path = null;

    /**
     * @var NoccyLabs\Pluggable\PluginInstance[] Found plugins
     */
    protected $plugins = array();
    
    /**
     * @var string The root namespace where all the plugins live
     */
    protected $plugin_ns = null;


    public function addPluginSearchPath($path)
    {
        $this->search_paths[] = $path;
    }
    
    /**
     * Set (replace) the plugin search paths that are scanned for plugins.
     *
     * @param string[] The paths to search
     */
    public function setPluginSearchPaths(array $paths)
    {
        $this->search_paths = array();
        foreach($paths as $path) {
            $this->addPluginSearchPath($path);
        }
    }
    
    /**
     * Set the path where the persistent data of the loaded plugins are stored
     * in the filesystem. If not set, no data will be saved.
     *
     */
    public function setPluginDataPath($path)
    {
        $this->data_path = $path;
    }

    /**
     * Scan the plugin paths, and repopulate the plugin list. To load plugins
     * after scanning, call activatePlugins(..) or $manager['pluginname']->activate();
     *
     */
    public function scan()
    {
        
    }
    
    /**
     * Activate one or more plugins. Shorthand for $manager->getPlugin($id)->activate()
     *
     * @param string[] $plugins The plugins to activate
     * @param bool $optimistic If true, no exception will be thrown if a 
     *       requested plugin is not found.
     */
    public function activatePlugins(array $plugins, $optimistic=false)
    {
    
    }
    
    /**
     * Disable (unload) one or more already loaded plugins. Shorthand for
     * $manager->getPlugin($id)->deactivate()
     *
     * @param string[] $plugins The plugins to activate
     */
    public function deactivatePlugins(array $plugins)
    {
    
    }
    
    public function hasPlugin($id)
    {
        return array_key_exists($id, $this->plugins);
    }
    
    public function getPlugin($id)
    {
        if (!array_key_exists($id, $this->plugins)) {
            return false;
        }
        return $this->plugins[$id];
    }
    
    public function setRootNamespace($root_ns)
    {
        $this->plugin_ns = $root_ns;
    }
    
    public function getRootNamespace()
    {
        return $this->plugin_ns;
    }
    
    
////// ArrayAccess interface implementation ///////////////////////////////////
 
    public function offsetExists($offset)
    {
        return $this->hasPlugin($offset);
    }
    
    public function offsetSet($offset, $value)
    {}
    
    public function offsetGet($offset)
    {
        return $this->getPlugin($offset);
    }

    public function offsetUnset($offset)
    {}   
    
}
