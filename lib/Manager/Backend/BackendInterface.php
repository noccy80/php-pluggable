<?php

namespace NoccyLabs\Pluggable\Manager\Backend;

interface BackendInterface
{
    public function getPlugins(array $meta_readers = null);
}
