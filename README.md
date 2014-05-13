noccylabs/pluggable
===================

Pluggable provides plugin-support with a lot of nifty features such as:

 * Multiple plugin paths - allows you to include default plugins in your phar
   executable while still making it possible for the user to add custom plugins.
   *NOTE: Plugin enumeration currently fails in phar executables*
 * Plugins in directories or phar archives - develop plugins live in the filesystem,
   and distribute the sourcecode *or* a compressed ready-to-use plugin archive.
 * Symfony2 ready - Pluggable makes use of the Symfony2 event dispatcher to
   notify and interact with the plugins. A service container can be passed to
   the plugins if they implement the `ContainerAwareInterface` interface. The
   configuration parser is using Symfony2 yaml.

## Terminology

 * **Persister** - A class implementing the `PersisterInterface` interface to read
   and write the list of loaded plugins between sessions.
 * **Scanner** - A class implementing the `ScannerInterface` interface to find
   plugins in the added search paths.
 * **Loader** - A class that loads up and prepares the plugin for use by f.ex. setting
   the container instance (the `ContainerAwareLoader` class is provided by Pluggable)
   or other properties or checks.

## What is a plugin?

A plugin consists of at least two files:

 * a `plugin.yml` file containing metadata and plugin information
 * a php-file containing the plugin class
 
This is the default behavior, and it can be modified by creating your own
custom Scanner. The main plugin class need to implement the `PluginInterface`
interface, but you can extend from the `Plugin` base class for convenience.

## Plugin manifest

The configuration file is in yaml format by default:

        pluggable:
            # This is how a plugin is identified
            id:       com.noccy.test
            # And the host id
            host:     com.noccy.testhost
            # Descriptive name of the plugin
            name:     Testplugin
            # The version of the plugin
            version:  1.0
            # Author
            author:   Noccy
            # This is the main plugin class
            class:    MyApp\Plugin\TestPlugin\TestPlugin
            # A psr-4 autoloader for the plugin directory will be created
            # for this namespace 
            autoload: MyApp\Plugin\TestPlugin

Note that it is entirely possible to read your plugin information from a .json
file or even straight from a class if you like; it is all up to the Scanner as
long as the data returned matches the yaml configuration.

*NOT IMPLEMENTED:* The host id is enforced if `setHostId()` is called on the plugin manager. In
this case, plugins not having the host field set to a compatible value will not
be loaded.

## Loading plugins

You can load plugins manually using `Manager#activatePlugin()` or
`PluginInstance#activate()`. Additionally, when using a persister, you can
save and restore the plugin state as appropriate. The persister just need to
implement `getActivePlugins()` to return an array of active plugins, and the
`setActivePlugins(array)` to set the active plugins. Upon being called, the
persister should write or read the data from wherever appropriate, so if you
like to keep your code compact your main application class could be your
persister.

        use NoccyLabs\Pluggable;
        
        // Create a new plugin manager
        $manager = new Pluggable\Manager();
        // Create a persister, to track loaded plugins between sessions
        $persister = new MyPersister(__DIR__."/../plugins/state.conf");
        
        // Set up the paths where plugins can be located.
        $manager
            ->addPath(__DIR__."/../plugins")
            ->addPath(getenv("HOME")."/.myapp/plugins")
            ->setPersister($persister)
            ->scan();
            ;
        
## Globals

*NOT IMPLEMENTED*

Passing globals is done via the `globals` property:

        $manager = new Pluggable\Manager();
        $manager->globals->output = $output;

And in the plugin:

        protected function onTest()
        {
            $this->globals->output->write("Hello World!\n");
        }

## Using service containers

To use a service container, replace the default loader with a container aware
loader, and pass the container as the first parameter to the constructor (or
call on the loaders `setContainer` method)

        $manager = new Pluggable\Manager();
        $loader = new Pluggable\Loader\ContainerAwareLoader($container);
        $manager->setLoader($loader);
        
