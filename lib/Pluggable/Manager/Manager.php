<?php

namespace Pluggable\Manager;

use Pluggable\Manager\Loader\PluginLoaderInterface;
use Pluggable\Manager\Loader\PluginLoader;
use Pluggable\Manager\Scanner\PluginScannerInterface;
use Pluggable\Manager\Scanner\PluginScanner;
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

    public function __construct()
    {
        $this->loader = new PluginLoader();
        $this->scanner = new PluginScanner();
        $this->plugin_paths = array();
    }

    public function setLoader(PluginLoaderInterface $loader)
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
        if (!is_dir($path)) { return false; }
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
        $plugin_inst = new $plugin_class();
        
        $plugin = new PluginInstance();
        $plugin
            ->setName($plugin_conf['name'])
            ->setAuthor($plugin_conf['author'])
            ->setVersion($plugin_conf['version'])
            ->setPluginInstance($plugin_inst)
            ->setManager($this);
        
        return $plugin;
    
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
