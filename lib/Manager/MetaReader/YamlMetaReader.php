<?php

namespace NoccyLabs\Pluggable\Manager\MetaReader;

class YamlMetaReader implements MetaReaderInterface
{
    public function readPluginMeta($plugin_dir)
    {
        $file = "{$plugin_dir}/plugin.yml";
        if (($yaml = @file_get_contents($file))) {
            $info = $this->yaml_decode($yaml);
            foreach(array("id", "name", "ns", "class") as $req) {
                if (!array_key_exists($req, $info)) {
                    error_log("Manifest {$file} missing required key {$req}");
                    return false;
                }
            }
            return $info;
        }
    }
    
    protected function yaml_decode($yaml)
    {
        if (is_callable("yaml_decode")) {
            return \yaml_decode($yaml);
        }
        if (class_exists('Symfony\Component\Yaml\Yaml')) {
            return \Symfony\Component\Yaml\Yaml::parse($yaml);
        }
        throw new \RuntimeException("Unable to decode yaml; install a compatible parser");
    }
}
