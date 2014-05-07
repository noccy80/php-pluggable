<?php

namespace Pluggable\Example2;

use Pluggable\Plugin\ContainerAwarePlugin;
use Symfony\Component\EventDispatcher\Event;

class ExamplePlugin extends ContainerAwarePlugin
{
    public function activate()
    {
        $dispatcher = $this->getContainer()->get("event_dispatcher");
        echo "Registering event listener...\n";
        $dispatcher->addListener("some.event", array( $this, "onEvent" ));
    }

    public function onEvent(Event $e)
    {
        echo "event some.event fired!\n";
    }
}
