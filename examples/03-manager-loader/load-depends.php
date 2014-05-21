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

// This should load the dependencies of example4 (example2)
$plugin = $manager->getPlugin("pluggable.example4.exampleplugin");
$plugin->activate();

// you could also do $manager->activatePlugin("plugin.id.here")

$container->get("event_dispatcher")->dispatch("some.event");
