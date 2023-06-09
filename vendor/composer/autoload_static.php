<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit67d647814ca84f56cd3910c4c7883dfc
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Codelife\\CodelifeModelGeneratorHelper\\' => 38,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Codelife\\CodelifeModelGeneratorHelper\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit67d647814ca84f56cd3910c4c7883dfc::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit67d647814ca84f56cd3910c4c7883dfc::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit67d647814ca84f56cd3910c4c7883dfc::$classMap;

        }, null, ClassLoader::class);
    }
}
