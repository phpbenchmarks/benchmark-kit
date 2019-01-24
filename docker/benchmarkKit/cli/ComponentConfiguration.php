<?php

declare(strict_types=1);

namespace App;

class ComponentConfiguration
{
    public const PHP_5_6_ENABLED = ____PHPBENCHMARKS_PHP_5_6_ENABLED____;
    public const PHP_7_0_ENABLED = ____PHPBENCHMARKS_PHP_7_0_ENABLED____;
    public const PHP_7_1_ENABLED = ____PHPBENCHMARKS_PHP_7_1_ENABLED____;
    public const PHP_7_2_ENABLED = ____PHPBENCHMARKS_PHP_7_2_ENABLED____;
    public const PHP_7_3_ENABLED = ____PHPBENCHMARKS_PHP_7_3_ENABLED____;

    public const URL = "____PHPBENCHMARKS_BENCHMARK_URL____";
    public const SLUG = "____PHPBENCHMARKS_SLUG____";

    public const COMMON_REPOSITORY = '____PHPBENCHMARKS_SLUG____-common';

    public const DEPENDENCY_NAME = "____PHPBENCHMARKS_DEPENDENCY_NAME____";
    public const DEPENDENCY_MAJOR_VERSION = ____PHPBENCHMARKS_DEPENDENCY_MAJOR_VERSION____;
    public const DEPENDENCY_MINOR_VERSION = ____PHPBENCHMARKS_DEPENDENCY_MINOR_VERSION____;
    public const DEPENDENCY_BUGFIX_VERSION = ____PHPBENCHMARKS_DEPENDENCY_BUGFIX_VERSION____;

    public static function getDependencyVersion(): string
    {
        return
            static::DEPENDENCY_MAJOR_VERSION
            . '.'
            . static::DEPENDENCY_MINOR_VERSION
            . '.'
            . static::DEPENDENCY_BUGFIX_VERSION;
    }

    public static function getEnabledPhpVersions(): array
    {
        $return = [];
        static::PHP_5_6_ENABLED && $return[] = '5.6';
        static::PHP_7_0_ENABLED && $return[] = '7.0';
        static::PHP_7_1_ENABLED && $return[] = '7.1';
        static::PHP_7_2_ENABLED && $return[] = '7.2';
        static::PHP_7_3_ENABLED && $return[] = '7.3';

        return $return;
    }

    public static function getDisabledPhpVersions(): array
    {
        $return = [];
        static::PHP_5_6_ENABLED === false && $return[] = '5.6';
        static::PHP_7_0_ENABLED === false && $return[] = '7.0';
        static::PHP_7_1_ENABLED === false && $return[] = '7.1';
        static::PHP_7_2_ENABLED === false && $return[] = '7.2';
        static::PHP_7_3_ENABLED === false && $return[] = '7.3';

        return $return;
    }
}
