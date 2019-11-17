<?php

declare(strict_types=1);

namespace PhpBenchmarks\BenchmarkConfiguration;

class Configuration
{
    public static function getComponentType(): int
    {
        return 2;
    }

    public static function getComponentName(): string
    {
        return '____PHPBENCHMARKS_COMPONENT_NAME____';
    }

    public static function getComponentSlug(): string
    {
        return '____PHPBENCHMARKS_COMPONENT_SLUG____';
    }

    public static function isPhp56Compatible(): bool
    {
        return ____PHPBENCHMARKS_PHP56_COMPATIBLE____;
    }

    public static function isPhp70Compatible(): bool
    {
        return ____PHPBENCHMARKS_PHP70_COMPATIBLE____;
    }

    public static function isPhp71Compatible(): bool
    {
        return ____PHPBENCHMARKS_PHP71_COMPATIBLE____;
    }

    public static function isPhp72Compatible(): bool
    {
        return ____PHPBENCHMARKS_PHP72_COMPATIBLE____;
    }

    public static function isPhp73Compatible(): bool
    {
        return ____PHPBENCHMARKS_PHP73_COMPATIBLE____;
    }

    public static function getBenchmarkUrl(): string
    {
        return '____PHPBENCHMARKS_BENCHMARK_URL____';
    }

    public static function getCoreDependencyName(): string
    {
        return '____PHPBENCHMARKS_CORE_DEPENDENCY_NAME____';
    }

    public static function getCoreDependencyMajorVersion(): int
    {
        return ____PHPBENCHMARKS_CORE_DEPENDENCY_MAJOR_VERSION____;
    }

    public static function getCoreDependencyMinorVersion(): int
    {
        return ____PHPBENCHMARKS_CORE_DEPENDENCY_MINOR_VERSION____;
    }

    public static function getCoreDependencyPatchVersion(): int
    {
        return ____PHPBENCHMARKS_CORE_DEPENDENCY_PATCH_VERSION____;
    }

    public static function getBenchmarkType(): int
    {
        return 3;
    }

    public static function getSourceCodeUrls(): array
    {
        return [
            'route' => '____PHPBENCHMARKS_ROUTE_SOURCE_CODE_URL____',
            'controller' => '____PHPBENCHMARKS_CONTROLLER_SOURCE_CODE_URL____',
            'randomLangue' => '____PHPBENCHMARKS_RANDOM_LANGUE_SOURCE_CODE_URL____',
            'randomizeLangueEvent' => '____PHPBENCHMARKS_RANDOMIZE_LANGUE_EVENT_SOURCE_CODE_URL____',
            'translations' => '____PHPBENCHMARKS_TRANSLATIONS_SOURCE_CODE_URL____',
            'translate' => '____PHPBENCHMARKS_TRANSLATE_SOURCE_CODE_URL____',
            'serialize' => '____PHPBENCHMARKS_SERIALIZE_SOURCE_CODE_URL____'
        ];
    }
}
