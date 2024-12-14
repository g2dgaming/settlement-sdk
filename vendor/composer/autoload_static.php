<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite0504ac4b84646ef9d8fab9e7ea2a7f0
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'ApnaPayment\\Settlements\\' => 24,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ApnaPayment\\Settlements\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite0504ac4b84646ef9d8fab9e7ea2a7f0::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite0504ac4b84646ef9d8fab9e7ea2a7f0::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite0504ac4b84646ef9d8fab9e7ea2a7f0::$classMap;

        }, null, ClassLoader::class);
    }
}
