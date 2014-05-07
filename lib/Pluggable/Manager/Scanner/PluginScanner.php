<?php

namespace Pluggable\Manager\Scanner;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class PluginScanner implements PluginScannerInterface
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
