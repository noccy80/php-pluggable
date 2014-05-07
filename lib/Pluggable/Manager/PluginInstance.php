<?php

namespace Pluggable\Manager;

use Pluggable\Plugin\PluginInterface;

class PluginInstance
{
    protected $name;
    
    protected $version;
    
    protected $plugin_instance;

    protected $active = false;
    
    protected $manager;
    
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
    
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }
    
    public function getVersion()
    {
        return $this->version;
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
