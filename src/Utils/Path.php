<?php

namespace App\Utils;

use App\PhpVersion\PhpVersion;

/**
 * This class will be used with all PHP versions, so it has to compatible with at least PHP 5.6
 */
class Path
{
    /** @var ?string */
    protected static $sourceCodePath;

    /** @return string */
    public static function getBenchmarkKitPath()
    {
        return __DIR__ . '/../..';
    }

    /**
     * @param string $sourceCodePath
     * @return void
     */
    public static function setSourceCodePath($sourceCodePath)
    {
        static::$sourceCodePath = $sourceCodePath;
    }

    /**
     * @param bool $exceptionOnNotFound
     * @return string|null
     */
    public static function getSourceCodePath($exceptionOnNotFound = true)
    {
        $sourceCodePath = static::$sourceCodePath ?? $_ENV['SOURCE_CODE_PATH'] ?? null;
        if (is_string($sourceCodePath) === false) {
            if ($exceptionOnNotFound === true) {
                throw new \Exception('Unable to find source code path.');
            }
            $return = null;
        } else {
            $return = realpath($sourceCodePath);
        }

        return is_string($return) ? $return : $sourceCodePath;
    }

    /**
     * @param string $path
     * @return string
     */
    public static function rmPrefix($path)
    {
        $prefix = static::getSourceCodePath(false);
        if (is_string($prefix) && substr($path, 0, strlen($prefix)) === $prefix) {
            return substr($path, strlen($prefix) + 1);
        }

        return $path;
    }

    /** @return string */
    public static function getBenchmarkConfigurationPath()
    {
        return static::getSourceCodePath() . '/.phpbenchmarks';
    }

    /** @return string */
    public static function getConfigFilePath()
    {
        return static::getBenchmarkConfigurationPath() . '/config.yml';
    }

    /** @return string */
    public static function getPhpConfigurationPath(PhpVersion $phpVersion)
    {
        return static::getBenchmarkConfigurationPath() . '/php/' . $phpVersion->toString();
    }

    /** @return string */
    public static function getComposerLockPath(PhpVersion $phpVersion)
    {
        return static::getPhpConfigurationPath($phpVersion) . '/composer.lock';
    }

    /** @return string */
    public static function getVhostPath()
    {
        return static::getBenchmarkConfigurationPath() . '/nginx/vhost.conf';
    }

    /** @return string */
    public static function getInitBenchmarkPath(PhpVersion $phpVersion)
    {
        return static::getPhpConfigurationPath($phpVersion) . '/initBenchmark.sh';
    }

    /** @return string */
    public static function getResponseBodyPath(PhpVersion $phpVersion)
    {
        return static::getPhpConfigurationPath($phpVersion) . '/responseBody';
    }

    /** @return string */
    public static function getCircleCiPath()
    {
        return static::getSourceCodePath() . '/.circleci';
    }

    /** @return string */
    public static function getCircleCiConfigPath()
    {
        return static::getCircleCiPath() . '/config.yml';
    }

    /** @return string */
    public static function getPreloadPath(PhpVersion $phpVersion)
    {
        return static::getPhpConfigurationPath($phpVersion) . '/preload.php';
    }

    /** @return string */
    public static function getPhpIniPath(PhpVersion $phpVersion)
    {
        return static::getPhpConfigurationPath($phpVersion) . '/php.ini';
    }

    /** @return string */
    public static function getStatisticsPath()
    {
        return __DIR__ . '/../../var/statistics.json';
    }

    /** @return string */
    public static function getNginxVhostPath()
    {
        return '/etc/nginx/sites-enabled';
    }
}
