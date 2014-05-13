<?php

namespace Pluggable\Persister;

interface PersisterInterface
{
    public function setActivePlugins(array $plugin_ids);
    public function getActivePlugins();
}
