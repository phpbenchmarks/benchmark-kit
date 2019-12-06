<?php

declare(strict_types=1);

namespace App\Component;

use App\Benchmark\Benchmark;

class ComponentType
{
    public const PHP = 1;
    public const FRAMEWORK = 2;
    public const TEMPLATE_ENGINE = 3;
    public const JSON_SERIALIZER = 4;

    protected const TYPES = [
        self::PHP => [
            'name' => 'PHP',
            'camelCaseName' => 'php',
            'showResultsQueryParameter' => 'phpBenchmarksShowResults=1'
        ],
        self::FRAMEWORK => [
            'name' => 'Framework',
            'camelCaseName' => 'framework',
            'showResultsQueryParameter' => null
        ],
        self::TEMPLATE_ENGINE => [
            'name' => 'Template engine',
            'camelCaseName' => 'templateEngine',
            'showResultsQueryParameter' => null
        ],
        self::JSON_SERIALIZER => [
            'name' => 'JSON serializer',
            'camelCaseName' => 'jsonSerializer',
            'showResultsQueryParameter' => 'phpBenchmarksShowResults=1'
        ],
    ];

    public static function getAll(): array
    {
        return [
            static::PHP => static::getName(static::PHP),
            static::FRAMEWORK => static::getName(static::FRAMEWORK),
            static::TEMPLATE_ENGINE => static::getName(static::TEMPLATE_ENGINE),
            static::JSON_SERIALIZER => static::getName(static::JSON_SERIALIZER)
        ];
    }

    public static function getName(int $type = null): string
    {
        return static::getConfiguration($type)['name'];
    }

    public static function getCamelCaseName(int $type = null): string
    {
        return static::getConfiguration($type)['camelCaseName'];
    }

    public static function getShowResultsQueryParameter(int $type = null): ?string
    {
        return static::getConfiguration($type)['showResultsQueryParameter'];
    }

    protected static function getConfiguration(int $type = null): array
    {
        $type = $type ?? Benchmark::getComponentType();

        if (array_key_exists($type, static::TYPES) === false) {
            throw new \Exception('Unknown component type "' . $type . '".');
        }

        return static::TYPES[$type];
    }
}
