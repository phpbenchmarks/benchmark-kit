<?php

declare(strict_types=1);

namespace App\ComponentConfiguration;

use App\PhpVersion\PhpVersion;
use AbstractComponentConfiguration\AbstractComponentConfiguration;

class ComponentConfiguration extends AbstractComponentConfiguration implements ComponentConfigurationInterface
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
        static::isPhp56Enabled() && $return[] = PhpVersion::PHP_56;
        static::isPhp70Enabled() && $return[] = PhpVersion::PHP_70;
        static::isPhp71Enabled() && $return[] = PhpVersion::PHP_71;
        static::isPhp72Enabled() && $return[] = PhpVersion::PHP_72;
        static::isPhp73Enabled() && $return[] = PhpVersion::PHP_73;

        return $return;
    }

    public static function getDisabledPhpVersions(): array
    {
        $return = [];
        static::isPhp56Enabled() === false && $return[] = PhpVersion::PHP_56;
        static::isPhp70Enabled() === false && $return[] = PhpVersion::PHP_70;
        static::isPhp71Enabled() === false && $return[] = PhpVersion::PHP_71;
        static::isPhp72Enabled() === false && $return[] = PhpVersion::PHP_72;
        static::isPhp73Enabled() === false && $return[] = PhpVersion::PHP_73;

        return $return;
    }
}
