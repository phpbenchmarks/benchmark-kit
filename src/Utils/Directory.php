<?php

declare(strict_types=1);

namespace App\Utils;

use App\PhpVersion\PhpVersion;

class Directory
{
    public static function getBenchmarkPath(): string
    {
        return '/var/www/benchmark';
    }

    public static function removeBenchmarkPathPrefix(string $path): string
    {
        return substr($path, strlen(static::getBenchmarkPath()) + 1);
    }

    public static function getBenchmarkConfigurationPath(): string
    {
        return static::getBenchmarkPath() . '/.phpbenchmarks';
    }

    public static function getPhpConfigurationPath(PhpVersion $phpVersion): string
    {
        return static::getBenchmarkConfigurationPath() . '/php/' . $phpVersion->toString();
    }

    public static function getComposerLockFilePath(PhpVersion $phpVersion): string
    {
        return static::getPhpConfigurationPath($phpVersion) . '/composer.lock';
    }
}
