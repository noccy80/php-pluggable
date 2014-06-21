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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class ContainerAwareLoader extends PluginLoader
{
    protected $container;
    
    public function __construct(ContainerInterface $container = null)
    {
        $this->setContainer($container);
    }

    public function loadPlugin(PluginInterface $plugin)
    {
        if ($plugin instanceof ContainerAwareInterface) {
            $plugin->setContainer($this->container);
        }
        parent::loadPlugin($plugin);
    }
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function getContainer()
    {
        return $this->container;
    }
}
