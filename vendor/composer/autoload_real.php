<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInita05be078ad1cb9a92dc6c724ecb5b7ba
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInita05be078ad1cb9a92dc6c724ecb5b7ba', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInita05be078ad1cb9a92dc6c724ecb5b7ba', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInita05be078ad1cb9a92dc6c724ecb5b7ba::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
