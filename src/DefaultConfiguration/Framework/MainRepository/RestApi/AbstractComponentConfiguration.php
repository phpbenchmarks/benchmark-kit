<?php

declare(strict_types=1);

namespace ____NAMESPACE____;

abstract class AbstractComponentConfiguration
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

    public static function isPhp56Enabled(): bool
    {
        return ____PHPBENCHMARKS_PHP56_ENABLED____;
    }

    public static function isPhp70Enabled(): bool
    {
        return ____PHPBENCHMARKS_PHP70_ENABLED____;
    }

    public static function isPhp71Enabled(): bool
    {
        return ____PHPBENCHMARKS_PHP71_ENABLED____;
    }

    public static function isPhp72Enabled(): bool
    {
        return ____PHPBENCHMARKS_PHP72_ENABLED____;
    }

    public static function isPhp73Enabled(): bool
    {
        return ____PHPBENCHMARKS_PHP73_ENABLED____;
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
