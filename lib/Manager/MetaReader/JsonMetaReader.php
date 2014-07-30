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
class JsonMetaReader implements MetaReaderInterface
{
    public function readPluginMeta($plugin_dir)
    {
        $file = "{$plugin_dir}/plugin.json";
        if (file_exists($file) && (($json = @file_get_contents($file)))) {
            $info = json_decode($json, JSON_OBJECT_AS_ARRAY);
            if (($err = json_last_error())) {
                $errmsg = json_last_error_msg();
                throw new BadManifestException("Manifest {$file} contains invalid json: {$errmsg}");
            }
            foreach(array("id", "name", "ns", "class") as $req) {
                if (!array_key_exists($req, $info)) {
                    throw new BadManifestException("Manifest {$file} missing required key {$req}");
                }
            }
            return $info;
        }
        return false;
    }
}
