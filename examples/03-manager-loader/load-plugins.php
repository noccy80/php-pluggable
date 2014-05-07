<?php

require_once __DIR__."/../../vendor/autoload.php";

use Pluggable\Manager\Manager;
use Pluggable\Manager\Loader\ContainerAwareLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;

$container = new ContainerBuilder();
$container->set("event_dispatcher", new EventDispatcher());

$manager = new Manager();
$manager
    ->addPath(__DIR__."/..")
    ->setLoader(new ContainerAwareLoader($container))
    ->scan()
    ;

$plugins = $manager->getAvailablePlugins();
foreach($plugins as $plugin) {
    echo "Loading {$plugin->getName()} ({$plugin->getId()})\n";
    $plugin->activate();
}

// you could also do $manager->activatePlugin("plugin.id.here")

$container->get("event_dispatcher")->dispatch("some.event");
