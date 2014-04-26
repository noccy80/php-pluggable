<?php

require_once __DIR__."/../vendor/autoload.php";

use NoccyLabs\Pluggable\Manager as PluginManager;

$manager = new PluginManager();

// Set the search paths for plugins
$manager->addPluginSearchPath(getcwd()."/plugins");

// Set the plugin data path
$manager->setPluginDataPath("/tmp/pluggable");

// Set the root namespace all plugins are expected to share
$manager->setRootNamespace('Pluggable\Plugin');

// Scan for available plugins
$manager->scan();

// Activate the testplugin
$manager->activatePlugins(
    array('com.noccy.testplugin')
);
