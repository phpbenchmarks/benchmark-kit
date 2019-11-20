<?php

declare(strict_types=1);

namespace App\ComponentConfiguration;

use App\PhpVersion\PhpVersion;
use PhpBenchmarks\BenchmarkConfiguration\Configuration;

class ComponentConfiguration extends Configuration implements ComponentConfigurationInterface
{
    public static function getCoreDependencyVersion(): string
    {
        return
            static::getCoreDependencyMajorVersion()
                . '.'
                . static::getCoreDependencyMinorVersion()
                . '.'
                . static::getCoreDependencyPatchVersion();
    }

    public static function getEnabledPhpVersions(): array
    {
        $return = [];
        foreach (PhpVersion::getAll() as $phpVersion) {
            $parts = explode('.', $phpVersion);
            if (static::isPhpCompatible((int) $parts[0], (int) $parts[1])) {
                $return[] = $phpVersion;
            }
        }

        return $return;
    }

    public static function getDisabledPhpVersions(): array
    {
        $return = [];
        foreach (PhpVersion::getAll() as $phpVersion) {
            $parts = explode('.', $phpVersion);
            if (static::isPhpCompatible((int) $parts[0], (int) $parts[1]) === false) {
                $return[] = $phpVersion;
            }
        }

        return $return;
    }
}
