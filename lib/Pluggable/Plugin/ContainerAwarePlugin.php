<?php

namespace Pluggable\Plugin;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ContainerAwarePlugin extends Plugin implements ContainerAwareInterface
{
    protected $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function getContainer()
    {
        return $this->container;
        
    }
    
}
