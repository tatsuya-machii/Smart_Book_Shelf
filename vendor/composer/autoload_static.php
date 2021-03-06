<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit24a2b10637e5136477107c75fd9f2270
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Abraham\\TwitterOAuth\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Abraham\\TwitterOAuth\\' => 
        array (
            0 => __DIR__ . '/..' . '/abraham/twitteroauth/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit24a2b10637e5136477107c75fd9f2270::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit24a2b10637e5136477107c75fd9f2270::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
