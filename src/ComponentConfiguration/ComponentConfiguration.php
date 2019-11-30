<?php

declare(strict_types=1);

namespace App\ComponentConfiguration;

use App\{
    PhpVersion\PhpVersion,
    PhpVersion\PhpVersionArray,
    Utils\Path
};
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

    public static function isCompatibleWithPhp(PhpVersion $phpVersion): bool
    {
        return is_dir(Path::getPhpConfigurationPath($phpVersion));
    }

    public static function getCompatiblesPhpVersions(): PhpVersionArray
    {
        $return = new PhpVersionArray();
        foreach (PhpVersion::getAll() as $phpVersion) {
            if (static::isCompatibleWithPhp($phpVersion)) {
                $return[] = new PhpVersion($phpVersion->getMajor(), $phpVersion->getMinor());
            }
        }

        return $return;
    }

    public static function getIncompatiblesPhpVersions(): PhpVersionArray
    {
        $return = new PhpVersionArray();
        foreach (PhpVersion::getAll() as $phpVersion) {
            if (static::isCompatibleWithPhp($phpVersion) === false) {
                $return[] = new PhpVersion($phpVersion->getMajor(), $phpVersion->getMinor());
            }
        }

        return $return;
    }
}
