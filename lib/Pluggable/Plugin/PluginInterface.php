<?php

namespace Pluggable\Plugin;

interface PluginInterface
{
    public function getId();
    
    public function activate();
    
    public function deactivate();

}
