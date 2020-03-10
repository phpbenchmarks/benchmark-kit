<?php

namespace App\Utils;

use App\PhpVersion\PhpVersion;

$_SERVER['foo'] = uniqid();

/**
 * This class will be used with all PHP versions, so it has to compatible with at least PHP 5.6
 */
class Path
{
    /** @var ?string */
    protected static $sourceCodePath;

    public static function getBenchmarkKitPath()
    {
        return __DIR__ . '/../..';
    }

    public static function setSourceCodePath($sourceCodePath)
    {
        static::$sourceCodePath = $sourceCodePath;
    }

    public static function getSourceCodePath()
    {
        return static::$sourceCodePath ?? $_ENV['SOURCE_CODE_PATH'];
    }

    public static function rmPrefix(string $path)
    {
        $prefix = static::getSourceCodePath();
        if (substr($path, 0, strlen($prefix)) === $prefix) {
            return substr($path, strlen($prefix) + 1);
        }

        return $path;
    }

    public static function getBenchmarkConfigurationPath()
    {
        return static::getSourceCodePath() . '/.phpbenchmarks';
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
        return static::getSourceCodePath() . '/.circleci';
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
        return __DIR__ . '/../../var/statistics.json';
    }
}
