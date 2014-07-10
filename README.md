noccylabs/pluggable
===================

*Note: This readme applies to the 0.2.x branch of Pluggable*

## Installing

    $ composer require noccylabs/pluggable:0.2.x-dev


## Using
    
Create an instance of `NoccyLabs\Pluggable\Manager\PluginManager`, and add the
backends from which you would like to load plugins:

        // Find and load all plugins from virtual filesystem $vfs
        $plug = new PluginManager();
        $plug
            ->addBackend(new VirtFsBackend($vfs, null))
            ->findPlugins(true)
            ;

### Backends

#### DirectoryBackend

Directorybackend loads plugins from a set of directories. This backend can only
load directly from source, and not via phar, zip or any other archive.

        new DirectoryBackend(array(
            "/foo/bar",
            "/foo/biz",
            "/var/bar"
        ));


#### VirtFsBackend

VirtFsBackend loads plugins from a VirtFs filesystem consisting of mapped
directories as well as zip-files.

        new VirtFsBackend($vfs, "/");


#### StaticBackend

The StaticBackend returns a list of static pre-initialized plugins. Use this
for embedded plugins f.ex. when making phar executables.

        new StaticBackend(array(
            "my.plugin.id.one" => 'My\Plugin\Class',
            "my.plugin.id.two" => 'My\Other\Plugin\Class'
        ));


### Finding plugins

Passing `true` to `PluginManager#findPlugins()` will load all plugins found by
the backend:

        $plug->findPlugins(true);

The above is also functionally identical to:
         
        $plug->findPlugins( function ($plugin) {
            return true;
        });

To select the plugins to load, do something like:

        // Read plugins loaded since last time
        $plugins_to_load = file("plugins.lst", FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
        
        // Use a custom callback to see if the plugin is in the list
        $plug->findPlugins( function ($plugin) use ($plugins_to_load) {
            $plugin_id = $plugin->getPluginId();
            return in_array($plugin_id, $plugins_to_load);
        });
        
        // Write the list back out
        $loaded_plugins = $plug->getLoadedPluginIds();
        file_put_contents("plugins.lst", join("\n", $loaded_plugins));



## Writing plugins

Plugins should implement `NoccyLabs\Pluggable\Plugin\PluginInterface` or
extend `NoccyLabs\Pluggable\Plugin\Plugin`. If you choose to use the interface,
it is your responsibility to respond to the `PluginInterface#onActivate()` as
well as `PluginInterface#isActivated()` to reflect the state. If you extend the
plugin you can instead override the `Plugin#load()` method and leave the gears
and wrenches to Pluggable.

Plugins need to have a manifest (unless loaded with the `StaticBackend`) in any
of the supported languages json, yaml or sdl. Note that yaml and sdl might require
additional libraries be installed for the parsing to work.

Language  | Filename                 | Requirements
==========|==========================|=======================
Json      | `plugin.json`            | 
Yaml      | `plugin.yml`             | php5-yaml or symfony/yaml


The file should define the following values:

 * **id** - the plugin id, f.ex. foovendor.myplugin
 * **ns** - the namespace of the plugins root directory (psr-4)
 * **class** - the class to load from the specified ns
 * **name** - the plugin name
 

### Interfaces

By calling `PluginManager#addInterfaceLoader()`, callbacks can be created for
plugins implementing specific interfaces or extending specific classes. Internally
it uses `instanceof` to compare the instance against the requested name.

        class MyPlugin extends Plugin implements ICanAddInterface
        { ... }

        $plug->addInterfaceLoader("ICanAddInterface", function ($plugin) {
            $sum = $plugin->addNumbers(5, 4);
        });

Or use it to set containers etc:

        $plug->addInterfaceLoader($container_interface, function ($plugin) use ($container) {
            $plugin->setContainer($container);
        });



