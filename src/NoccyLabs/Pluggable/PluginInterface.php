<?php

namespace NoccyLabs\Pluggable;

interface PluginInterface
{
    public function setContainer($container);
    public function load();
}
