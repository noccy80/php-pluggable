<?php

namespace NoccyLabs\Pluggable\Plugin;

use Symfony\Component\DependencyInjection\ContainerAwareInterface,
    Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwarePlugin extends Plugin implements ContainerAwareInterface
{
    protected $container;

    public function setContainer( ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}
