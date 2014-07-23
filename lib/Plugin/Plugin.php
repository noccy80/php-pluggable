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

namespace NoccyLabs\Pluggable\Plugin;

/**
 * Plugin base class.
 *
 */
abstract class Plugin implements PluginInterface
{
    protected $plugin_id = null;

    protected $is_activated = false;
    
    protected $root = null;

    
    /**
     * {@inheritDoc}
     */
    public function setMetaData(array $meta)
    {
        $this->meta = $meta;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPluginId()
    {
        return $this->plugin_id;
    }

    /**
     * {@inheritDoc}
     */
    public function setPluginId($plugin_id)
    {
        $this->plugin_id = $plugin_id;
        return $this;
    }
    
    public function getPluginName()
    {
        if (!array_key_exists("name", $this->meta)) {
            return false;
        }
        return $this->meta["name"];
    }

    public function getPluginVersion()
    {
        if (!array_key_exists("version", $this->meta)) {
            return false;
        }
        return $this->meta["version"];
    }
    
    /**
     * {@inheritDoc}
     */
    public function setRoot($root)
    {
        $this->root = $root;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function isActivated()
    {
        return $this->is_activated;
    }
    
    /**
     * {@inheritDoc}
     */
    public function onActivate()
    {
        if (is_callable(array($this,"load"))) {
            call_user_func(array($this,"load"));
        }
        $this->is_activated = true;
    }

    /**
     * {@inheritDoc}
     */
    public function onDeactivate()
    {
        $this->is_activated = false;
    }
}
