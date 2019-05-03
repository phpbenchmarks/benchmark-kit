<?php

namespace App\ComponentConfiguration;

interface ComponentConfigurationInterface
{
    public static function getComponentType();
    public static function getComponentName();
    public static function getComponentSlug();

    public static function isPhp56Enabled();
    public static function isPhp70Enabled();
    public static function isPhp71Enabled();
    public static function isPhp72Enabled();
    public static function isPhp73Enabled();

    public static function getBenchmarkUrl();

    public static function getCoreDependencyName();
    public static function getCoreDependencyMajorVersion();
    public static function getCoreDependencyMinorVersion();
    public static function getCoreDependencyPatchVersion();

    public static function getBenchmarkType();

    public static function getSourceCodeUrls();
}
