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

namespace Pluggable\Plugin;

abstract class Plugin implements PluginInterface
{
    protected $id;

    public function getId()
    {
        if (!$this->id) {
            return str_replace("\\",".",strtolower(get_called_class()));
        } else {
            return $this->id;
        }
    }
    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function activate()
    {
        return true;
    }
    
    public function deactivate()
    {
        return true;
    }
}