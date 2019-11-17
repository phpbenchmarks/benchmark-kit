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
    public const JSON_SERIALIZATION_HELLO_WORLD = 6;
    public const JSON_SERIALIZATION_SMALL_OVERLOAD = 7;
    public const JSON_SERIALIZATION_BIG_OVERLOAD = 8;

    protected const CONFIGURATIONS = [
        self::HELLO_WORLD => [
            'name' => 'Hello world',
            'upperCamelCaseName' => 'HelloWorld',
            'slug' => 'hello-world',
            'defaultBenchmarkUrl' => '/benchmark/helloworld',
            'responseBodyFiles' => ['responseBody.txt'],
            'responseBodyFileMinSize' => 13,
            'sourceCodeUrlIds' => [
                ComponentType::PHP => ['entryPoint'],
                ComponentType::FRAMEWORK => ['route', 'controller'],
                ComponentType::TEMPLATE_ENGINE => ['entryPoint', 'template']
            ]
        ],
        self::REST_API => [
            'name' => 'REST API',
            'upperCamelCaseName' => 'RestApi',
            'slug' => 'rest-api',
            'defaultBenchmarkUrl' => '/benchmark/rest',
            'responseBodyFiles' => ['responseBody.en_GB.json', 'responseBody.fr_FR.json', 'responseBody.en.json'],
            'responseBodyFileMinSize' => 7541,
            'sourceCodeUrlIds' => [
                ComponentType::PHP => [
                    'entryPoint',
                    'randomizeLanguageDispatchEvent',
                    'randomizeLanguageEventListener',
                    'translations',
                    'translate',
                    'serialize'
                ],
                ComponentType::FRAMEWORK => [
                    'route',
                    'controller',
                    'randomizeLanguageDispatchEvent',
                    'randomizeLanguageEventListener',
                    'translations',
                    'translate',
                    'serialize'
                ]
            ]
        ],
        self::TEMPLATING_SMALL_OVERLOAD => [
            'name' => 'Template engine small overload',
            'upperCamelCaseName' => 'TemplateEngineSmallOverload',
            'slug' => 'templating-small-overload',
            'defaultBenchmarkUrl' => '/index.php',
            'responseBodyFiles' => ['responseBody.html'],
            'sourceCodeUrlIds' => [
                ComponentType::PHP => ['entryPoint']
            ]
        ],
        self::TEMPLATING_BIG_OVERLOAD => [
            'name' => 'Template engine big overload',
            'upperCamelCaseName' => 'TemplateEngineBigOverload',
            'slug' => 'templating-big-overload',
            'defaultBenchmarkUrl' => '/index.php',
            'responseBodyFiles' => ['responseBody.html'],
            'sourceCodeUrlIds' => [
                ComponentType::PHP => ['entryPoint']
            ]
        ],
        self::JSON_SERIALIZATION_HELLO_WORLD => [
            'name' => 'Serialization of Hello world',
            'upperCamelCaseName' => 'JsonSerializationHelloWorld',
            'slug' => 'json-serialization-hello-world',
            'defaultBenchmarkUrl' => 'index.php',
            'responseBodyFiles' => ['responseBody.json'],
            'responseBodyFileMinSize' => 17,
            'sourceCodeUrlIds' => [
                ComponentType::PHP => ['jsonSerialization'],
                ComponentType::JSON_SERIALIZER => ['jsonSerialization']
            ]
        ],
        self::JSON_SERIALIZATION_SMALL_OVERLOAD => [
            'name' => 'Small deserialization',
            'upperCamelCaseName' => 'JsonSerializationSmallOverload',
            'slug' => 'json-serialization-small-overload',
            'defaultBenchmarkUrl' => 'index.php',
            'responseBodyFiles' => ['responseBody.json'],
            'responseBodyFileMinSize' => 512000,
            'sourceCodeUrlIds' => [
                ComponentType::PHP => [
                    'jsonSerialization',
                    'integerSerialization',
                    'floatSerialization',
                    'stringSerialization',
                    'booleanSerialization',
                    'nullSerialization',
                    'arraySerialization',
                    'objectSerialization'
                ],
                ComponentType::JSON_SERIALIZER => [
                    'jsonSerialization',
                    'integerSerialization',
                    'floatSerialization',
                    'stringSerialization',
                    'booleanSerialization',
                    'nullSerialization',
                    'arraySerialization',
                    'objectSerialization',
                    'customSerializers'
                ]
            ]
        ],
        self::JSON_SERIALIZATION_BIG_OVERLOAD => [
            'name' => 'Big deserialization',
            'upperCamelCaseName' => 'JsonSerializationBigOverload',
            'slug' => 'json-serialization-big-overload',
            'defaultBenchmarkUrl' => 'index.php',
            'responseBodyFiles' => ['responseBody.json'],
            'responseBodyFileMinSize' => 5241001,
            'sourceCodeUrlIds' => [
                ComponentType::PHP => [
                    'jsonSerialization',
                    'integerSerialization',
                    'floatSerialization',
                    'stringSerialization',
                    'booleanSerialization',
                    'nullSerialization',
                    'arraySerialization',
                    'objectSerialization'
                ],
                ComponentType::JSON_SERIALIZER => [
                    'jsonSerialization',
                    'integerSerialization',
                    'floatSerialization',
                    'stringSerialization',
                    'booleanSerialization',
                    'nullSerialization',
                    'arraySerialization',
                    'objectSerialization',
                    'customSerializers'
                ]
            ]
        ]
    ];

    public static function getAll(): array
    {
        return [
            static::HELLO_WORLD => static::getConfiguration(static::HELLO_WORLD, 'name'),
            static::REST_API => static::getConfiguration(static::REST_API, 'name'),
            static::TEMPLATING_SMALL_OVERLOAD => static::getConfiguration(static::TEMPLATING_SMALL_OVERLOAD, 'name'),
            static::TEMPLATING_BIG_OVERLOAD => static::getConfiguration(static::TEMPLATING_BIG_OVERLOAD, 'name'),
            static::JSON_SERIALIZATION_HELLO_WORLD => static::getConfiguration(
                static::JSON_SERIALIZATION_HELLO_WORLD,
                'name'
            ),
            static::JSON_SERIALIZATION_SMALL_OVERLOAD => static::getConfiguration(
                static::JSON_SERIALIZATION_SMALL_OVERLOAD,
                'name'
            ),
            static::JSON_SERIALIZATION_BIG_OVERLOAD => static::getConfiguration(
                static::JSON_SERIALIZATION_BIG_OVERLOAD,
                'name'
            )
        ];
    }

    public static function getAllByComponentType(): array
    {
        $benchmarkTypes = static::getAll();

        return [
            ComponentType::PHP => [
                static::HELLO_WORLD => $benchmarkTypes[static::HELLO_WORLD],
                static::REST_API => $benchmarkTypes[static::REST_API],
                static::TEMPLATING_SMALL_OVERLOAD => $benchmarkTypes[static::TEMPLATING_SMALL_OVERLOAD],
                static::TEMPLATING_BIG_OVERLOAD => $benchmarkTypes[static::TEMPLATING_BIG_OVERLOAD],
                static::JSON_SERIALIZATION_HELLO_WORLD => $benchmarkTypes[static::JSON_SERIALIZATION_HELLO_WORLD],
                static::JSON_SERIALIZATION_SMALL_OVERLOAD => $benchmarkTypes[static::JSON_SERIALIZATION_SMALL_OVERLOAD],
                static::JSON_SERIALIZATION_BIG_OVERLOAD => $benchmarkTypes[static::JSON_SERIALIZATION_BIG_OVERLOAD]
            ],
            ComponentType::FRAMEWORK => [
                static::HELLO_WORLD => $benchmarkTypes[static::HELLO_WORLD],
                static::REST_API => $benchmarkTypes[static::REST_API],
            ],
            ComponentType::TEMPLATE_ENGINE => [
                static::HELLO_WORLD => $benchmarkTypes[static::HELLO_WORLD]
            ],
            ComponentType::JSON_SERIALIZER => [
                static::JSON_SERIALIZATION_HELLO_WORLD => $benchmarkTypes[static::JSON_SERIALIZATION_HELLO_WORLD],
                static::JSON_SERIALIZATION_SMALL_OVERLOAD => $benchmarkTypes[static::JSON_SERIALIZATION_SMALL_OVERLOAD],
                static::JSON_SERIALIZATION_BIG_OVERLOAD => $benchmarkTypes[static::JSON_SERIALIZATION_BIG_OVERLOAD]
            ]
        ];
    }

    public static function getByComponentType(int $componentType): array
    {
        return static::getAllByComponentType()[$componentType];
    }

    public static function getName(int $type): string
    {
        return static::getConfiguration($type, 'name');
    }

    public static function getSlug(int $type): string
    {
        return static::getConfiguration($type, 'slug');
    }

    public static function getUpperCamelCaseName(int $type): string
    {
        return static::getConfiguration($type, 'upperCamelCaseName');
    }

    public static function getDefaultBenchmarkUrl(int $type): string
    {
        return static::getConfiguration($type, 'defaultBenchmarkUrl');
    }

    public static function getResponseBodyFiles(int $type): array
    {
        return static::getConfiguration($type, 'responseBodyFiles');
    }

    public static function getResponseBodyFileMinSize(int $type): int
    {
        return static::getConfiguration($type, 'responseBodyFileMinSize');
    }

    public static function getSourceCodeUrlIds(int $type, int $componentType): array
    {
        return static::getConfiguration($type, 'sourceCodeUrlIds')[$componentType];
    }

    /** @return mixed */
    protected static function getConfiguration(int $type, string $name)
    {
        if (array_key_exists($type, static::CONFIGURATIONS) === false) {
            throw new \Exception('Unknown benchmark type ' . $type . '.');
        } elseif (array_key_exists($name, static::CONFIGURATIONS[$type]) === false) {
            throw new \Exception('Unknown configuration ' . $name . '.');
        }

        return static::CONFIGURATIONS[$type][$name];
    }
}
