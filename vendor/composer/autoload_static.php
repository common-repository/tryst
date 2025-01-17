<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit83de5df69b9b914e8fe2606345d25454
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Tryst\\Domain\\' => 13,
            'Tryst\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Tryst\\Domain\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/Tryst/Domain',
        ),
        'Tryst\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/Tryst',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit83de5df69b9b914e8fe2606345d25454::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit83de5df69b9b914e8fe2606345d25454::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
