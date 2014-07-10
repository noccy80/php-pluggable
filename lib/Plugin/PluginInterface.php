<?php

namespace NoccyLabs\Pluggable\Plugin;

interface PluginInterface
{
    public function setPluginId($id);
    public function getPluginId();
    public function setRoot($root);
    public function onActivate();
    public function onDeactivate();
    public function isActivated();
}
