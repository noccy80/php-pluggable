<?php

namespace Pluggable\Manager\Loader;

use Pluggable\Plugin\PluginInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class ContainerAwareLoader extends PluginLoader
{
    protected $container;
    
    public function __construct(ContainerInterface $container = null)
    {
        $this->setContainer($container);
    }

    public function loadPlugin(PluginInterface $plugin)
    {
        if ($plugin instanceof ContainerAwareInterface) {
            $plugin->setContainer($this->container);
        }
        parent::loadPlugin($plugin);
    }
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function getContainer()
    {
        return $this->container;
    }
}
