<?php

namespace NoccyLabs\Pluggable\Plugin;

abstract class Plugin implements PluginInterface
{
    protected $plugin_id = null;

    protected $is_activated = false;
    
    protected $root = null;

    public function getPluginId()
    {
        return $this->plugin_id;
    }

    public function setPluginId($plugin_id)
    {
        $this->plugin_id = $plugin_id;
        return $this;
    }
    
    public function setRoot($root)
    {
        $this->root = $root;
        return $this;
    }
    
    public function isActivated()
    {
        return $this->is_activated;
    }
    
    public function onActivate()
    {
        if (is_callable(array($this,"load"))) {
            call_user_func(array($this,"load"));
        }
        $this->is_activated = true;
    }

    public function onDeactivate()
    {
        $this->is_activated = false;
    }
}
