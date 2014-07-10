<?php

namespace NoccyLabs\Pluggable\Manager\MetaReader;

class JsonMetaReader implements MetaReaderInterface
{
    public function readPluginMeta($plugin_dir)
    {
        $file = "{$plugin_dir}/plugin.json";
        if (($json = @file_get_contents($file))) {
            $info = json_decode($json, JSON_OBJECT_AS_ARRAY);
            foreach(array("id", "name", "ns", "class") as $req) {
                if (!array_key_exists($req, $info)) {
                    error_log("Manifest {$file} missing required key {$req}");
                    return false;
                }
            }
            return $info;
        }
    
    }
}
