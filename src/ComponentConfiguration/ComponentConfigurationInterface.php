<?php

namespace App\ComponentConfiguration;

interface ComponentConfigurationInterface
{
    public static function getComponentType();

    public static function getComponentName();

    public static function getComponentSlug();

    public static function isPhp56Compatible();

    public static function isPhp70Compatible();

    public static function isPhp71Compatible();

    public static function isPhp72Compatible();

    public static function isPhp73Compatible();

    public static function getBenchmarkUrl();

    public static function getCoreDependencyName();

    public static function getCoreDependencyMajorVersion();

    public static function getCoreDependencyMinorVersion();

    public static function getCoreDependencyPatchVersion();

    public static function getBenchmarkType();

    public static function getSourceCodeUrls();
}
