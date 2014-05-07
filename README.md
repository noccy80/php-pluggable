noccylabs/pluggable
===================

Pluggable provides plugin-support with a lot of nifty features such as:

 * Multiple plugin paths - allows you to include default plugins in your phar
   executable while still making it possible for the user to add custom plugins.
 * Plugins in directories or phar archives - develop plugins live in the filesystem,
   and distribute the sourcecode *or* a compressed ready-to-use plugin archive.
 * Symfony2 ready - Pluggable makes use of the Symfony2 event dispatcher to
   notify and interact with the plugins. A service container can be passed to
   the plugins if they implement the `ContainerAwareInterface` interface. The
   configuration parser is using Symfony2 yaml.

## What is a plugin?

A plugin consists of at least two files:

 * a `plugin.conf` file containing metadata and plugin information
 * a `plugin.php` file containing the plugin class

The configuration file is in yaml format:

        pluggable:
            # This is how a plugin is identified
            id:      com.noccy.test
            # And the host id
            host:    com.noccy.testhost
            # Descriptive name of the plugin
            name:    Testplugin
            # The version of the plugin
            version: 1.0
            # This is the main plugin class
            class:   MyApp\Plugin\TestPlugin\TestPlugin
            # And the plugin source file (or autoloader bootstrap)
            file:    plugin.php
            # Services (only if symfony service container is available)
            services:
                

The host id is enforced if `setHostId()` is called on the plugin manager. In
this case, plugins not having the host field set to a compatible value will not
be loaded.

And the plugin is just php:

        use NoccyLabs\Pluggable\Plugin\Plugin;
        
        class TestPlugin extends Plugin
        {
            // Events are proxied, so as long as the plugin is disabled it
            // will not receive any registered events. Load may be called
            // more than once, but create only once.
            public function create()
            {
                $this->on("test", [ $this,"onTest" ]);
            }
            
            // Called when the plugin is activated
            public function load()
            {}
            
            // Called when the plugin is deactivated
            public function unload()
            {}
            
            // Event handler
            protected function onTest()
            {
                echo "Hello world!\n";
            }
        }

## Loading plugins



        use NoccyLabs\Pluggable;
        
        // Create a new plugin manager
        $manager = new Pluggable\Manager();
        
        // Set up the paths where plugins can be located.
        $manager->setPluginPaths([
            __DIR__."/../plugins",
            getenv("HOME")."/.myapp/plugins"
        ]);

        // Create a persister, to track loaded plugins between sessions
        $persister = new Pluggable\Persister(__DIR__."/../plugins/state.conf");
        $manager->setPersister(persister);

        // Scan for plugins, and also load the ones that were previously
        // loaded according to the used persister.
        $manager->scan();

        // Load all plugins
        $manager->load("*");        
        
## Globals

Passing globals is done via the `globals` property:

        $manager = new Pluggable\Manager();
        $manager->globals->output = $output;

And in the plugin:

        protected function onTest()
        {
            $this->globals->output->write("Hello World!\n");
        }

