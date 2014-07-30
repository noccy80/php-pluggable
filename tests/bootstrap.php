<?php

if (!extension_loaded("xdebug")) {
    @dl("xdebug.so");
}

require_once __DIR__."/../vendor/autoload.php";

// register autoloader for test implementations
spl_autoload_register(function($c) {
    $fn = __DIR__."/src/".strtr($c,"\\","/").".php";
    if (file_exists($fn)) {
        require_once $fn;
        return true;
    }
});
