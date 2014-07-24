<?php

require_once __DIR__."/../vendor/autoload.php";

use NoccyLabs\Pluggable\Manager\PluginManager;
use NoccyLabs\Pluggable\Manager\Backend\VirtFsBackend;
use NoccyLabs\Pluggable\Plugin\Plugin;

use NoccyLabs\VirtFs\VirtFs;
use NoccyLabs\VirtFs\VirtFsLoader;

$vfs_plugins = new VirtFs("plugins");

// Bind a plugin .zip file to the virtual filesystem. The path is determined
// from the filename
function bind_plugin_zip($plugin_zip, VirtFs $vfs)
{
    // This is really just a name for the mountoint of this plugin zip. We
    // use it with VirtFs#addArchive() to mount the plugin zip in its own
    // directory.
    $plugin_name = basename($plugin_zip,".zip");
    $vfs->addArchive($plugin_zip, $plugin_name);
    
}

// Add a directory to the plugin vfs
function add_plugin_src($plugin_dir, VirtFs $vfs)
{
    $vfs->addDirectory($plugin_dir);
}

function add_plugin_zips($path, VirtFs $vfs)
{
    foreach(glob($path."*.zip") as $plugin_zip) {
        bind_plugin_zip($plugin_zip, $vfs);
    }
}

// Grab all the .zip files and load them
add_plugin_zips(__DIR__."/plugins/", $vfs_plugins);
// Then add the plugins directory
add_plugin_src(__DIR__."/plugins/", $vfs_plugins);


$container = new \Symfony\Component\DependencyInjection\ContainerBuilder();

$plug = new PluginManager();
$plug
    //->addBackend( new VirtFsBackend($vfs, null) )
    // load static plugins
    ->addBackend( new VirtFsBackend($vfs_plugins, null) )
    ->addLoader( function($plugin, $manager) {
        echo "Now loading ".get_class($plugin)."\n";
    })
    ->addInterfaceLoader(
       'Symfony\Component\DependencyInjection\ContainerAwareInterface', 
       function (Plugin $plugin) use ($container) {
           $plugin->setContainer( $container );
       }
    )
    ->findPlugins( function($id, $plugin) {
        echo "Load request: {$id}\n";
        return true;
    })
    ;
    
echo "Loaded plugins:\n";
var_dump($plug->getLoadedPluginIds());
