<?php

namespace Pluggable\Manager;

use Pluggable\Plugin\PluginInterface;

class PluginInstance
{
    protected $name;
    
    protected $version;
    
    protected $author;
    
    protected $plugin_instance;

    protected $active = false;
    
    protected $manager;
    
    protected $description;
    
    protected $plugin_path;
    
    protected $dependencies;
    
    public function getId()
    {
        if (empty($this->plugin_instance)) {
            return null;
        }
        return $this->plugin_instance->getId();
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }
    
    public function getAuthor()
    {
        return $this->author;
    }
    
    public function setPluginPath($path)
    {
        $this->plugin_path = $path;
        return $this;
    }
    
    public function getPluginPath()
    {
        return $this->plugin_path;
    }
    
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }
    
    public function getVersion()
    {
        return $this->version;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDependencies(array $dependencies=null)
    {
        $this->dependencies = (array)$dependencies;
        return $this;
    }
    
    public function getDependencies()
    {
        return $this->dependencies;
    }
    
    public function setPluginInstance(PluginInterface $plugin)
    {
        $this->plugin_instance = $plugin;
        return $this;
    }
    
    public function getPluginInstance()
    {
        return $this->plugin_instance;
    }
    
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
        return $this;
    }
    
    public function getManager()
    {
        return $this->manager;
    }
    
    public function activate()
    {
        if ($this->active) { return; }
        foreach($this->dependencies as $dependency) {
            $plugin = $this->manager->getPlugin($dependency);
            if (!$plugin) { 
                throw new \Exception("Unable to activate plugin {$this->getId()} as the dependency {$dependency} is missing!");
            }
            $plugin->activate(); 
        }
        $this->manager->getLoader()->loadPlugin($this->plugin_instance);
        $this->active = true;
    }
    
    public function deactivate()
    {
        if (!$this->active) { return; }
        $this->plugin_instance->deactivate();
        $this->active = false;
    }
    
    public function isActive()
    {
        return $this->active;
    }

}
