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
 * Static loading of plugins from an assoc array. Use this to load built-in
 * plugins.
 *
 *
 */
class StaticBackend implements BackendInterface
{
    protected $plugins = array();

    public function __construct(array $plugins = array())
    {
        foreach($plugins as $id=>$plugin) {
            $this->addStaticPlugin($id, $plugin);
        }
    }
    
    /**
     * Add a plugin class to the static plugin list.
     *
     * @param string The plugin id to enforce
     * @param string The plugin instance or class name for the id
     */
    public function addStaticPlugin($id, $plugin_class)
    {
        if (!is_object($plugin_class)) {
            $plugin_class = new $plugin_class;
        }
        if (is_callable(array($plugin_class, "setPluginId"))) {
            $plugin_class->setPluginId($id);
        }
        $this->plugins[$id] = $plugin_class;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getPlugins(array $meta_readers = null)
    {
        return $this->plugins;
    }
}
