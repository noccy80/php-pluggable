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
 * Interface for plugins 
 *
 *
 */
interface PluginInterface
{
    public function setMetaData(array $meta);

    /**
     * Set the plugin id
     *
     * @param string The plugin id as specified in the manifest
     */
    public function setPluginId($id);

    /**
     * Get the plugin id
     *
     * @return string The plugin id
     */
    public function getPluginId();
    
    public function setRoot($root);

    /**
     * Called when the plugin is activated.
     *
     */    
    public function onActivate();
 
    /**
     * Called when the plugin is deactivated.
     *
     */   
    public function onDeactivate();
 
    /**
     * Should return true if this plugin has been successfully activated,
     * false otherwise.
     */   
    public function isActivated();
}
