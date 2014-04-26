<?php

namespace NoccyLabs\Pluggable;

class PluginInstance
{

    protected $loaded = false;
    
    protected $plugin_dir = null;
    
    protected $data_dir = null;

    public function __construct($plugin_dir, $data_dir=null)
    {
    
    }
    
    public function activate()
    {
        if ($this->loaded) {
            return;
        }
    }
    
    public function deactivate()
    {
        if (!$this->loaded) {
            return;
        }
    }
    
    public function getPluginName()
    {}
    
    public function getPluginVersion()
    {}
    
    public function getPluginId()
    {}

}
