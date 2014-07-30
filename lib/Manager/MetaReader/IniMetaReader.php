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

namespace NoccyLabs\Pluggable\Manager\MetaReader;

use NoccyLabs\Pluggable\Manager\Exception\BadManifestException;

/**
 * Read plugin manifests stored in .json files
 *
 */
class IniMetaReader implements MetaReaderInterface
{
    public function readPluginMeta($plugin_dir)
    {
        $file = "{$plugin_dir}/plugin.ini";
        if (($ini = @file_get_contents($file))) {
            $infos = parse_ini_string($ini, true, INI_SCANNER_RAW);
            $info = $infos['plugin'];
            $info = array_merge($infos, $info);
            foreach(array("id", "name", "ns", "class") as $req) {
                if (!array_key_exists($req, $info)) {
                    throw new BadManifestException("Manifest {$file} missing required key {$req}");
                }
            }
            return $info;
        } else {
            return false;
        }
    
    }
}
