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

    public const MAIN_REPOSITORY = "____PHPBENCHMARKS_MAIN_REPOSITORY____";
    public const COMMON_REPOSITORY = '____PHPBENCHMARKS_SLUG____-common';
    public const VERSION_MAJOR = ____PHPBENCHMARKS_MAJOR_VERSION____;
    public const VERSION_MINOR = ____PHPBENCHMARKS_MINOR_VERSION____;
    public const VERSION_BUGFIX = ____PHPBENCHMARKS_BUGFIX_VERSION____;

    public static function getVersion(): string
    {
        return static::VERSION_MAJOR . '.' . static::VERSION_MINOR . '.' . static::VERSION_BUGFIX;
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
