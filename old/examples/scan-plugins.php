<?php

require_once __DIR__."/../vendor/autoload.php";

use Pluggable\Manager\Manager;

$manager = new Manager();
$manager->addPath(__DIR__."/plugins");
$manager->scan();

$plugins = $manager->getAvailablePlugins();
echo "Available plugins:\n";
foreach($plugins as $plugin) {
    echo " - {$plugin->getName()} ({$plugin->getId()})\n";
}
