<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit864108c9ba216ac6908f0f533043cf74
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'X' => 
        array (
            'Xzag\\Telegram\\' => 14,
        ),
        'T' => 
        array (
            'Twig\\' => 5,
            'TelegramBot\\Api\\' => 16,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Ctype\\' => 23,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Xzag\\Telegram\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
        'Twig\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/twig/src',
        ),
        'TelegramBot\\Api\\' => 
        array (
            0 => __DIR__ . '/..' . '/telegram-bot/api/src',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Twig_' => 
            array (
                0 => __DIR__ . '/..' . '/twig/twig/lib',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit864108c9ba216ac6908f0f533043cf74::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit864108c9ba216ac6908f0f533043cf74::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit864108c9ba216ac6908f0f533043cf74::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
