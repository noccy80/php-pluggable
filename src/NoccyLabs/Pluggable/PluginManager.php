<?php

namespace NoccyLabs\Pluggable;

use Symfony\Component\Yaml\Yaml;

class PluginManager
{

    protected $plugins;
    
    protected $container;

    public function scanDirectories(array $directories)
    {
        foreach($directories as $directory) {
            $this->scanDirectory($directory);
        }
    }
    
    public function scanDirectory($directory)
    {
        if (file_exists($directory) && is_dir($directory)) {
            $this->addAutoload($directory);
            $dir = new \DirectoryIterator($directory);
            foreach($dir as $file) {
                if ($file->isDir() && !$file->isDot()) {
                    $this->scanPlugin($file->getPathname());
                }
            }
        } 
    }
    
    public function scanPlugin($dir)
    {
        if (file_exists($dir."/plugin.conf")) {
            $conf = Yaml::parse(file_get_contents($dir."/plugin.conf"));
            $id = $conf["plugin"]["id"];
            $class = (empty($conf['plugin']['class'])?null:$conf['plugin']['class']);
            $this->plugins[$id] = (object)array(
                "id" => $id,
                "config" => $conf,
                "loaded" => false,
                "class" => $class,
                "instance" => null
            );
        }    
    }
    
    private function addAutoload($path)
    {
        $root_ns = "MspSim\\Plugin\\";
        spl_autoload_register(function ($class) use($path, $root_ns) {
            if (strncmp($class,$root_ns,strlen($root_ns)) === 0) {
                $fpath = $path."/".str_replace("\\","/",substr($class,strlen($root_ns))).".php";
                if (file_exists($fpath)) {
                    require_once $fpath;
                    return true;
                }
            }
        });
    }
    
    public function getPluginNames()
    {
        return array_keys($this->plugins);
    }
    
    public function getCommands()
    {
        $commands = array();
        foreach($this->plugins as $plugin) {
            if ($plugin->loaded) {
                if (!empty($plugin->config['plugin']['commands'])) {
                    $cmdlist = $plugin->config['plugin']['commands'];
                    foreach($cmdlist as $cmdclass) {
                        $cmdinst = new $cmdclass;
                        if (is_callable(array($cmdinst,"setContainer"))) {
                            $cmdinst->setContainer($this->container);
                        }
                        $commands[] = $cmdinst;
                    }
                }
            }
        }
        return $commands;
    }
    
    public function getPlugins()
    {
        return $this->plugins;
    }
    
    public function getPluginLoaded($name)
    {
        if (array_key_exists($name, $this->plugins)) {
            return $this->plugins[$name]->loaded;
        }
    }
    
    public function loadPlugin($name)
    {
        if (!array_key_exists($name, $this->plugins)) {
            throw new \RuntimeException("No such plugin {$name}");
        }
    
        $p_class = $this->plugins[$name]->class;
        if ($p_class) {
            if (!class_exists($p_class)) {
                throw new \RuntimeException("Unable to load plugin {$name}: Plugin class {$p_class} could not be found");
            }
            try {
                $p_inst = new $p_class;
                $p_inst->setContainer($this->container);
                $p_inst->load();
            } catch (\Exception $e) {
                throw new \RuntimeException("Unable to load plugin {$name}: {$e}");
            }
            $this->plugins[$name]->instance = $p_inst;
        } else {
            $p_inst = null;
        }
        $this->plugins[$name]->loaded = true;
        return $p_inst;
    }
    
    public function loadEnabledPlugins()
    {
        $config = $this->container->get("config_repository")->getRegistry("plugins");
        if (empty($config->getValue("plugins.enabled")) && (empty($config->getValue("plugins.available")))) {
            $enabled_plugins = $this->getPluginNames();
            $config->setValue("plugins.enabled", $enabled_plugins);
        } else {
            $enabled_plugins = $config->getValue("plugins.enabled");
        }
        foreach((array)$enabled_plugins as $plugin) {
            try {
                $this->loadPlugin($plugin);
            } catch (\Exception $e) {
                error_log("\033[37;41;1mWarning: Plugin {$plugin} could not be loaded.\033[0m");
                $this->disablePlugin($plugin);
            }
        }
        $config->setValue("plugins.available", $this->getPluginNames());
    }
    
    public function enablePlugin($name)
    {
        $config = $this->container->get("config_repository")->getRegistry("plugins");
        if (empty($config->getValue("plugins.enabled"))) {
            $enabled_plugins = array( $name );
            $config->setValue("plugins.enabled", $enabled_plugins);
            return true;
        } else {
            $enabled_plugins = $config->getValue("plugins.enabled");
            if (!in_array($name,$enabled_plugins)) {
                $enabled_plugins[] = $name;
                $config->setValue("plugins.enabled", $enabled_plugins);
                return true;
            }
        }
        return false;
    }
    
    public function disablePlugin($name)
    {
        $config = $this->container->get("config_repository")->getRegistry("plugins");
        if (empty($config->getValue("plugins.enabled"))) {
            return;
        } 
        $enabled_plugins_in = $config->getValue("plugins.enabled");
        $enabled_plugins = array();
        foreach($enabled_plugins_in as $pluginname) {
            if ($name != $pluginname) {
                $enabled_plugins[] = $pluginname;
            }
        }
        if (count($enabled_plugins) != count($enabled_plugins_in)) {
            $config->setValue("plugins.enabled", $enabled_plugins);
            return true;
        }
        return false;
    }
    
    public function enableAllPlugins()
    {
        $config = $this->container->get("config_repository")->getRegistry("plugins");
        $enabled_plugins = $this->getPluginNames();
        $config->setValue("plugins.enabled", $enabled_plugins);
        return true;
    }
    
    public function disableAllPlugins()
    {
        $config = $this->container->get("config_repository")->getRegistry("plugins");
        $config->setValue("plugins.enabled", array());
    }
    
    public function setContainer($container)
    {
        $this->container = $container;
    }

}
