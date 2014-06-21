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

namespace Pluggable\Loader;

use Pluggable\Plugin\PluginInterface;

/**
 * The LoaderInterface is used to activate plugins after injecting any
 * dependencies needed or making other adjustments that need to be done
 * on all plugins before loading.
 * 
 */
interface LoaderInterface
{
    /**
     * Load a plugin
     * 
     * @param \Pluggable\Plugin\PluginInterface $plugin
     */
    public function loadPlugin(PluginInterface $plugin);
}
