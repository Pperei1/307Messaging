<?php

// autoload_real.php @generated by Composer

<<<<<<< HEAD
class ComposerAutoloaderInit4a89e3352ca4f79535a21b98e42237c6
=======
class ComposerAutoloaderInit808258b661f65402f8cdb16102cf42b3
>>>>>>> origin/master
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

<<<<<<< HEAD
        spl_autoload_register(array('ComposerAutoloaderInit4a89e3352ca4f79535a21b98e42237c6', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInit4a89e3352ca4f79535a21b98e42237c6', 'loadClassLoader'));
=======
        spl_autoload_register(array('ComposerAutoloaderInit808258b661f65402f8cdb16102cf42b3', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInit808258b661f65402f8cdb16102cf42b3', 'loadClassLoader'));
>>>>>>> origin/master

        $map = require __DIR__ . '/autoload_namespaces.php';
        foreach ($map as $namespace => $path) {
            $loader->set($namespace, $path);
        }

        $map = require __DIR__ . '/autoload_psr4.php';
        foreach ($map as $namespace => $path) {
            $loader->setPsr4($namespace, $path);
        }

        $classMap = require __DIR__ . '/autoload_classmap.php';
        if ($classMap) {
            $loader->addClassMap($classMap);
        }

        $loader->register(true);

        return $loader;
    }
}
<<<<<<< HEAD
=======

function composerRequire808258b661f65402f8cdb16102cf42b3($file)
{
    require $file;
}
>>>>>>> origin/master
