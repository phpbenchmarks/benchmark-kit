<?php

declare(strict_types=1);

namespace ____NAMESPACE____;

abstract class AbstractComponentConfiguration
{
    public static function getComponentType(): int
    {
        return 4;
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
        return 7;
    }

    public static function getSourceCodeUrls(): array
    {
        return [
            'jsonSerialization' => '____PHPBENCHMARKS_JSON_SERIALIZATION_CODE_URL____',
            'integerSerialization' => '____PHPBENCHMARKS_INTEGER_SERIALIZATION_CODE_URL____',
            'floatSerialization' => '____PHPBENCHMARKS_FLOAT_SERIALIZATION_CODE_URL____',
            'stringSerialization' => '____PHPBENCHMARKS_STRING_SERIALIZATION_CODE_URL____',
            'booleanSerialization' => '____PHPBENCHMARKS_BOOLEAN_SERIALIZATION_CODE_URL____',
            'nullSerialization' => '____PHPBENCHMARKS_NULL_SERIALIZATION_CODE_URL____',
            'arraySerialization' => '____PHPBENCHMARKS_ARRAY_SERIALIZATION_CODE_URL____',
            'objectSerialization' => '____PHPBENCHMARKS_OBJECT_SERIALIZATION_CODE_URL____',
            'customSerializers' => '____PHPBENCHMARKS_CUSTOM_SERIALIZERS_CODE_URL____'
        ];
    }
}
