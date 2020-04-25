<?php

declare(strict_types=1);

namespace App\Component;

use App\Benchmark\Benchmark;
use steevanb\PhpTypedArray\ScalarArray\StringArray;

class ComponentType
{
    public const PHP = 'php';
    public const FRAMEWORK = 'framework';
    public const TEMPLATE_ENGINE = 'template-engine';
    public const JSON_SERIALIZER = 'json-serializer';

    protected const TYPES = [
        self::PHP => [
            'name' => 'PHP',
            'camelCaseName' => 'php'
        ],
        self::FRAMEWORK => [
            'name' => 'Framework',
            'camelCaseName' => 'framework'
        ],
        self::TEMPLATE_ENGINE => [
            'name' => 'Template engine',
            'camelCaseName' => 'templateEngine'
        ],
        self::JSON_SERIALIZER => [
            'name' => 'JSON serializer',
            'camelCaseName' => 'jsonSerializer'
        ],
    ];

    public static function getAll(): StringArray
    {
        return new StringArray(
            [
                static::PHP => static::getName(static::PHP),
                static::FRAMEWORK => static::getName(static::FRAMEWORK),
                static::TEMPLATE_ENGINE => static::getName(static::TEMPLATE_ENGINE),
                static::JSON_SERIALIZER => static::getName(static::JSON_SERIALIZER)
            ]
        );
    }

    public static function getName(string $type = null): string
    {
        return static::getConfiguration($type)['name'];
    }

    public static function getCamelCaseName(string $type = null): string
    {
        return static::getConfiguration($type)['camelCaseName'];
    }

    /** @return string[] */
    protected static function getConfiguration(string $type = null): array
    {
        $type = $type ?? Benchmark::getComponentType();

        if (array_key_exists($type, static::TYPES) === false) {
            throw new \Exception('Unknown component type "' . $type . '".');
        }

        return static::TYPES[$type];
    }
}
