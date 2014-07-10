<?php

require_once __DIR__."/../vendor/autoload.php";

use NoccyLabs\Pluggable\Manager\PluginManager;
use NoccyLabs\Pluggable\Manager\Backend\StaticBackend;
use NoccyLabs\Pluggable\Plugin\Plugin;

class HelloWorldPlugin extends Plugin
{
    
    public function onActivate()
    {
        parent::onActivate();
        echo "Activating ".$this->getPluginId()."!\n";
    }
}

$plugin_classes = array(
    "plugin.static.helloworld" => "HelloWorldPlugin"
);

// this should be your symfony container
$container = null;

$plug = new PluginManager();
$plug
    // load static plugins
    ->addBackend( new StaticBackend($plugin_classes) )
    // add container to container aware plugins
    ->addInterfaceLoader(
       'Symfony\Component\DependencyInjection\ContainerAwareInterface', 
       function (Plugin $plugin) use ($container) {
           $plugin->setContainer( $container );
       }
    )
    ->findPlugins( function($id, $plugin) {
        echo "Load request: {$id}\n";
        return true;
    })
    ;

var_dump($plug->getLoadedPluginIds());
