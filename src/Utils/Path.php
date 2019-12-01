<?php

declare(strict_types=1);

namespace App\Utils;

use App\PhpVersion\PhpVersion;

class Path
{
    public static function getBenchmarkKitPath(): string
    {
        return __DIR__ . '/../..';
    }

    public static function getBenchmarkPath(): string
    {
        return '/var/www/benchmark';
    }

    public static function rmPrefix(string $path): string
    {
        return substr($path, strlen(static::getBenchmarkPath()) + 1);
    }

    public static function getBenchmarkConfigurationPath(): string
    {
        return static::getBenchmarkPath() . '/.phpbenchmarks';
    }

    public static function getBenchmarkConfigurationClassPath(): string
    {
        return static::getBenchmarkConfigurationPath() . '/Configuration.php';
    }

    public static function getPhpConfigurationPath(PhpVersion $phpVersion): string
    {
        return static::getBenchmarkConfigurationPath() . '/php/' . $phpVersion->toString();
    }

    public static function getComposerLockPath(PhpVersion $phpVersion): string
    {
        return static::getPhpConfigurationPath($phpVersion) . '/composer.lock';
    }

    public static function getVhostPath(): string
    {
        return static::getBenchmarkConfigurationPath() . '/nginx/vhost.conf';
    }

    public static function getInitBenchmarkPath(PhpVersion $phpVersion): string
    {
        return static::getPhpConfigurationPath($phpVersion) . '/initBenchmark.sh';
    }

    public static function getResponseBodyPath(PhpVersion $phpVersion): string
    {
        return static::getPhpConfigurationPath($phpVersion) . '/responseBody';
    }

    public static function getCircleCiPath(): string
    {
        return static::getBenchmarkPath() . '/.circleci';
    }
}
