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
        if (static::isPhp56Compatible()) {
            $return[] = PhpVersion::PHP_56;
        }
        if (static::isPhp70Compatible()) {
            $return[] = PhpVersion::PHP_70;
        }
        if (static::isPhp71Compatible()) {
            $return[] = PhpVersion::PHP_71;
        }
        if (static::isPhp72Compatible()) {
            $return[] = PhpVersion::PHP_72;
        }
        if (static::isPhp73Compatible()) {
            $return[] = PhpVersion::PHP_73;
        }

        return $return;
    }

    public static function getDisabledPhpVersions(): array
    {
        $return = [];
        if (static::isPhp56Compatible() === false) {
            $return[] = PhpVersion::PHP_56;
        }
        if (static::isPhp70Compatible() === false) {
            $return[] = PhpVersion::PHP_70;
        }
        if (static::isPhp71Compatible() === false) {
            $return[] = PhpVersion::PHP_71;
        }
        if (static::isPhp72Compatible() === false) {
            $return[] = PhpVersion::PHP_72;
        }
        if (static::isPhp73Compatible() === false) {
            $return[] = PhpVersion::PHP_73;
        }

        return $return;
    }
}
