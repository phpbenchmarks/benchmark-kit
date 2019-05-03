<?php

declare(strict_types=1);

namespace App\Component;

use Symfony\Component\Console\Input\StringInput;

class ComponentType
{
    public const PHP = 1;
    public const FRAMEWORK = 2;
    public const TEMPLATE_ENGINE = 3;
    public const JSON_SERIALIZER = 4;

    protected const TYPES = [
        self::PHP => [
            'name' => 'PHP',
            'upperCamelCaseName' => 'Php',
            'showResultsQueryParameter' => 'phpBenchmarksShowResults=1'
        ],
        self::FRAMEWORK => [
            'name' => 'Framework',
            'upperCamelCaseName' => 'Framework',
            'showResultsQueryParameter' => null
        ],
        self::TEMPLATE_ENGINE => [
            'name' => 'Template engine',
            'upperCamelCaseName' => 'TemplateEngine',
            'showResultsQueryParameter' => null
        ],
        self::JSON_SERIALIZER => [
            'name' => 'JSON serializer',
            'upperCamelCaseName' => 'JsonSerializer',
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

    public static function getName(int $type): string
    {
        return static::getConfiguration($type)['name'];
    }

    public static function getUpperCamelCaseName(int $type): string
    {
        return static::getConfiguration($type)['upperCamelCaseName'];
    }

    public static function getShowResultsQueryParameter(int $type): ?string
    {
        return static::getConfiguration($type)['showResultsQueryParameter'];
    }

    protected static function getConfiguration(int $type): array
    {
        if (array_key_exists($type, static::TYPES) === false) {
            throw new \Exception('Unknown component type "' . $type . '".');
        }

        return static::TYPES[$type];
    }
}
