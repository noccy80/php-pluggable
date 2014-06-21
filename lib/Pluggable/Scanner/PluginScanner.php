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

namespace Pluggable\Scanner;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class PluginScanner implements ScannerInterface
{
    public function scanDirectory($path)
    {
        $finder = new Finder();
        $finder->files()->name("plugin.yml")->in($path);

        $plugins = array();
        
        foreach($finder as $found) {
            $plugin_conf = $found->getPathName();
            $conf_data = file_get_contents($plugin_conf);
            try {
                $conf = Yaml::parse($conf_data);
                if (empty($conf) || empty($conf['plugin'])) {
                    throw new \Exception("Invalid manifest (missing plugin section)");
                }
                $plugins[$plugin_conf] = $conf;
            } catch (\Exception $e) {
                throw new \Exception("Parse error in manifest {$plugin_conf}", $e);
            }

        }
        
        return $plugins;
    }
}