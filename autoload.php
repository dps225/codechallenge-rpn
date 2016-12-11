<?php

spl_autoload_register(function($class) {

    $classfilename = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.class.php';

    if (file_exists($classfilename)) {
        require_once $classfilename;
    }

    $otherfilename = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    if (file_exists($otherfilename)) {
        require_once $otherfilename;
    }
});