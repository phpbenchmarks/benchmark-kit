<?php

declare(strict_types=1);

namespace App\Benchmark;

use App\Component\ComponentType;

class BenchmarkType
{
    public const HELLO_WORLD = 1;
    public const REST_API = 3;
    public const TEMPLATING_SMALL_OVERLOAD = 4;
    public const TEMPLATING_BIG_OVERLOAD = 5;

    public static function getAll(): array
    {
        return [
            static::HELLO_WORLD => 'Hello world',
            static::REST_API => 'REST API',
            static::TEMPLATING_SMALL_OVERLOAD => 'Template engine small overload',
            static::TEMPLATING_BIG_OVERLOAD => 'Template engine big overload'
        ];
    }

    public static function getName(int $type): string
    {
        if (array_key_exists($type, static::getAll()) === false) {
            throw new \Exception('Unknown benchmark type ' . $type . '.');
        }

        return static::getAll()[$type];
    }

    public static function getAllByComponentType(): array
    {
        $benchmarkTypes = static::getAll();

        return [
            ComponentType::PHP => [
                static::HELLO_WORLD => $benchmarkTypes[static::HELLO_WORLD],
                static::TEMPLATING_SMALL_OVERLOAD => $benchmarkTypes[static::TEMPLATING_SMALL_OVERLOAD],
                static::TEMPLATING_BIG_OVERLOAD => $benchmarkTypes[static::TEMPLATING_BIG_OVERLOAD]
            ],
            ComponentType::FRAMEWORK => [
                static::HELLO_WORLD => $benchmarkTypes[static::HELLO_WORLD],
                static::REST_API => $benchmarkTypes[static::REST_API],
            ],
            ComponentType::TEMPLATE_ENGINE => [
                static::HELLO_WORLD => $benchmarkTypes[static::HELLO_WORLD]
            ]
        ];
    }

    public static function getByComponentType(int $componentType): array
    {
        return static::getAllByComponentType()[$componentType];
    }

    public static function getSlug(int $type): string
    {
        $slugs = [
            static::HELLO_WORLD => 'hello-world',
            static::REST_API => 'rest-api',
            static::TEMPLATING_SMALL_OVERLOAD => 'templating-small-overload',
            static::TEMPLATING_BIG_OVERLOAD => 'templating-big-overload',
        ];
        if (array_key_exists($type, $slugs) === false) {
            throw new \Exception('Unknown benchmark type ' . $type . '.');
        }

        return $slugs[$type];
    }

    public static function getCamelCaseName(int $type): string
    {
        $names = [
            static::HELLO_WORLD => 'HelloWorld',
            static::REST_API => 'RestApi',
            static::TEMPLATING_SMALL_OVERLOAD => 'TemplatingSmallOverload',
            static::TEMPLATING_BIG_OVERLOAD => 'TemplatingBigOverload',
        ];
        if (array_key_exists($type, $names) === false) {
            throw new \Exception('Unknown benchmark type ' . $type . '.');
        }

        return $names[$type];
    }
}
