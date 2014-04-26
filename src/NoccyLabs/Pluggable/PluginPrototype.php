<?php

namespace NoccyLabs\Pluggable;

abstract class PluginPrototype implements PluginInterface
{
    protected $container;
    protected $events;
    protected $config;

    public function setContainer($container)
    {
        $this->container = $container;
        $this->events = $container->get("event_dispatcher");
        $config = $container->get("config_repository");
        $me = strtolower(basename(str_replace("\\","/",get_called_class())));
        $this->config = $config->getRegistry("plugin-data/".$me);
    }
    
    protected function onEvent($event, callable $callback)
    {
        $this->events->addListener($event, $callback);
    }
    
}
