<?php

    namespace adaptiveImages;

    \spl_autoload_register(function($class) {
        $class = ltrim($class, '\\');

        if(strpos($class, __NAMESPACE__) !== 0) {
            return;
        }

        $class = str_replace(__NAMESPACE__, '', $class);
        $path = __DIR__ . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.class.php';

        require_once($path);
    });

    // remove image sizes
    new Admin();
    