<?php

namespace Pluggable\Plugin\TestPlugin;

use NoccyLabs\Pluggable\Plugin;
use Symfony\Component\EventDispatcher\Event;

class TestPlugin extends Plugin
{

    protected function getListeners()
    {
        // Register your listeners this way to make sure unloaded plugins
        // don't receive events.
        return array(
            "pluggable.example" => array($this, "onExampleEvent")
        );
    }

    public function onExampleEvent(Event $e)
    {
        // Do your magic here
        echo "Example event!\n";
    }

}
