<?php

namespace App\Utils;

use App\PhpVersion\PhpVersion;

/**
 * This class will be used with all PHP versions, so it has to compatible with at least PHP 5.6
 */
class Path
{
    public static function getBenchmarkKitPath()
    {
        return __DIR__ . '/../..';
    }

    public static function getBenchmarkPath()
    {
        return '/var/www/benchmark';
    }

    public static function rmPrefix(string $path)
    {
        return substr($path, strlen(static::getBenchmarkPath()) + 1);
    }

    public static function getBenchmarkConfigurationPath()
    {
        return static::getBenchmarkPath() . '/.phpbenchmarks';
    }

    public static function getConfigFilePath()
    {
        return static::getBenchmarkConfigurationPath() . '/config.yml';
    }

    public static function getPhpConfigurationPath(PhpVersion $phpVersion)
    {
        return static::getBenchmarkConfigurationPath() . '/php/' . $phpVersion->toString();
    }

    public static function getComposerLockPath(PhpVersion $phpVersion)
    {
        return static::getPhpConfigurationPath($phpVersion) . '/composer.lock';
    }

    public static function getVhostPath()
    {
        return static::getBenchmarkConfigurationPath() . '/nginx/vhost.conf';
    }

    public static function getInitBenchmarkPath(PhpVersion $phpVersion)
    {
        return static::getPhpConfigurationPath($phpVersion) . '/initBenchmark.sh';
    }

    public static function getResponseBodyPath(PhpVersion $phpVersion)
    {
        return static::getPhpConfigurationPath($phpVersion) . '/responseBody';
    }

    public static function getCircleCiPath()
    {
        return static::getBenchmarkPath() . '/.circleci';
    }

    public static function getPreloadPath(PhpVersion $phpVersion)
    {
        return static::getPhpConfigurationPath($phpVersion) . '/preload.php';
    }

    public static function getPhpIniPath(PhpVersion $phpVersion)
    {
        return static::getPhpConfigurationPath($phpVersion) . '/php.ini';
    }

    public static function getStatisticsPath()
    {
        return '/tmp/phpbenchmarks-statistics.json';
    }
}
