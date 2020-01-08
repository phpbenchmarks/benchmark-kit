<?php

declare(strict_types=1);

namespace App\Benchmark;

use App\{
    Component\ComponentType,
    SourceCodeUrl\SourceCodeUrl
};

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
            'camelCaseName' => 'helloWorld',
            'slug' => 'hello-world',
            'defaultBenchmarkRelativeUrl' => '/benchmark/helloworld',
            'responseBodyFiles' => ['responseBody.txt'],
            'responseBodyFileMinSize' => 13,
            'sourceCodeUrlIds' => [
                ComponentType::PHP => [SourceCodeUrl::URL_ENTRY_POINT],
                ComponentType::FRAMEWORK => [
                    SourceCodeUrl::URL_ROUTE,
                    SourceCodeUrl::URL_CONTROLLER
                ],
                ComponentType::TEMPLATE_ENGINE => [
                    SourceCodeUrl::URL_ENTRY_POINT,
                    SourceCodeUrl::URL_TEMPLATE
                ]
            ]
        ],
        self::REST_API => [
            'name' => 'REST API',
            'camelCaseName' => 'restApi',
            'slug' => 'rest-api',
            'defaultBenchmarkRelativeUrl' => '/benchmark/rest',
            'responseBodyFiles' => ['responseBody.en_GB.json', 'responseBody.fr_FR.json', 'responseBody.en.json'],
            'responseBodyFileMinSize' => 7541,
            'sourceCodeUrlIds' => [
                ComponentType::PHP => [
                    SourceCodeUrl::URL_ENTRY_POINT,
                    SourceCodeUrl::URL_RANDOMIZE_LANGUAGE_DISPATCH_EVENT,
                    SourceCodeUrl::URL_RANDOMIZE_LANGUAGE_EVENT_LISTENER,
                    SourceCodeUrl::URL_TRANSLATIONS,
                    SourceCodeUrl::URL_TRANSLATE,
                    SourceCodeUrl::URL_SERIALIZE
                ],
                ComponentType::FRAMEWORK => [
                    SourceCodeUrl::URL_ROUTE,
                    SourceCodeUrl::URL_CONTROLLER,
                    SourceCodeUrl::URL_RANDOMIZE_LANGUAGE_DISPATCH_EVENT,
                    SourceCodeUrl::URL_RANDOMIZE_LANGUAGE_EVENT_LISTENER,
                    SourceCodeUrl::URL_TRANSLATIONS,
                    SourceCodeUrl::URL_TRANSLATE,
                    SourceCodeUrl::URL_SERIALIZE
                ]
            ]
        ],
        self::TEMPLATING_SMALL_OVERLOAD => [
            'name' => 'Template engine small overload',
            'camelCaseName' => 'templateEngineSmallOverload',
            'slug' => 'templating-small-overload',
            'defaultBenchmarkRelativeUrl' => '/',
            'responseBodyFiles' => ['responseBody.html'],
            'responseBodyFileMinSize' => 520000,
            'sourceCodeUrlIds' => [
                ComponentType::PHP => [SourceCodeUrl::URL_ENTRY_POINT]
            ]
        ],
        self::TEMPLATING_BIG_OVERLOAD => [
            'name' => 'Template engine big overload',
            'camelCaseName' => 'templateEngineBigOverload',
            'slug' => 'templating-big-overload',
            'defaultBenchmarkRelativeUrl' => '/',
            'responseBodyFiles' => ['responseBody.html'],
            'responseBodyFileMinSize' => 5200000,
            'sourceCodeUrlIds' => [
                ComponentType::PHP => [SourceCodeUrl::URL_ENTRY_POINT]
            ]
        ],
        self::JSON_SERIALIZATION_HELLO_WORLD => [
            'name' => 'Serialization of Hello world',
            'camelCaseName' => 'jsonSerializationHelloWorld',
            'slug' => 'json-serialization-hello-world',
            'defaultBenchmarkRelativeUrl' => '/',
            'responseBodyFiles' => ['responseBody.json'],
            'responseBodyFileMinSize' => 17,
            'sourceCodeUrlIds' => [
                ComponentType::PHP => [SourceCodeUrl::URL_JSON_SERIALIZATION],
                ComponentType::JSON_SERIALIZER => [SourceCodeUrl::URL_JSON_SERIALIZATION]
            ]
        ],
        self::JSON_SERIALIZATION_SMALL_OVERLOAD => [
            'name' => 'Small JSON serialization',
            'camelCaseName' => 'jsonSerializationSmallOverload',
            'slug' => 'json-serialization-small-overload',
            'defaultBenchmarkRelativeUrl' => '/',
            'responseBodyFiles' => ['responseBody.json'],
            'responseBodyFileMinSize' => 512000,
            'sourceCodeUrlIds' => [
                ComponentType::PHP => [
                    SourceCodeUrl::URL_JSON_SERIALIZATION,
                    SourceCodeUrl::URL_INTEGER_SERIALIZATION,
                    SourceCodeUrl::URL_FLOAT_SERIALIZATION,
                    SourceCodeUrl::URL_STRING_SERIALIZATION,
                    SourceCodeUrl::URL_BOOLEAN_SERIALIZATION,
                    SourceCodeUrl::URL_NULL_SERIALIZATION,
                    SourceCodeUrl::URL_ARRAY_SERIALIZATION,
                    SourceCodeUrl::URL_OBJECT_SERIALIZATION
                ],
                ComponentType::JSON_SERIALIZER => [
                    SourceCodeUrl::URL_JSON_SERIALIZATION,
                    SourceCodeUrl::URL_INTEGER_SERIALIZATION,
                    SourceCodeUrl::URL_FLOAT_SERIALIZATION,
                    SourceCodeUrl::URL_STRING_SERIALIZATION,
                    SourceCodeUrl::URL_BOOLEAN_SERIALIZATION,
                    SourceCodeUrl::URL_NULL_SERIALIZATION,
                    SourceCodeUrl::URL_ARRAY_SERIALIZATION,
                    SourceCodeUrl::URL_OBJECT_SERIALIZATION,
                    SourceCodeUrl::URL_CUSTOM_SERIALIZATION
                ]
            ]
        ],
        self::JSON_SERIALIZATION_BIG_OVERLOAD => [
            'name' => 'Big JSON serialization',
            'camelCaseName' => 'jsonSerializationBigOverload',
            'slug' => 'json-serialization-big-overload',
            'defaultBenchmarkRelativeUrl' => '/',
            'responseBodyFiles' => ['responseBody.json'],
            'responseBodyFileMinSize' => 5241001,
            'sourceCodeUrlIds' => [
                ComponentType::PHP => [
                    SourceCodeUrl::URL_JSON_SERIALIZATION,
                    SourceCodeUrl::URL_INTEGER_SERIALIZATION,
                    SourceCodeUrl::URL_FLOAT_SERIALIZATION,
                    SourceCodeUrl::URL_STRING_SERIALIZATION,
                    SourceCodeUrl::URL_BOOLEAN_SERIALIZATION,
                    SourceCodeUrl::URL_NULL_SERIALIZATION,
                    SourceCodeUrl::URL_ARRAY_SERIALIZATION,
                    SourceCodeUrl::URL_OBJECT_SERIALIZATION
                ],
                ComponentType::JSON_SERIALIZER => [
                    SourceCodeUrl::URL_JSON_SERIALIZATION,
                    SourceCodeUrl::URL_INTEGER_SERIALIZATION,
                    SourceCodeUrl::URL_FLOAT_SERIALIZATION,
                    SourceCodeUrl::URL_STRING_SERIALIZATION,
                    SourceCodeUrl::URL_BOOLEAN_SERIALIZATION,
                    SourceCodeUrl::URL_NULL_SERIALIZATION,
                    SourceCodeUrl::URL_ARRAY_SERIALIZATION,
                    SourceCodeUrl::URL_OBJECT_SERIALIZATION,
                    SourceCodeUrl::URL_CUSTOM_SERIALIZATION
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

    public static function getName(int $type = null): string
    {
        return static::getConfiguration($type, 'name');
    }

    public static function getSlug(int $type = null): string
    {
        return static::getConfiguration($type, 'slug');
    }

    public static function getCamelCaseName(int $type = null): string
    {
        return static::getConfiguration($type, 'camelCaseName');
    }

    public static function getDefaultBenchmarkRelativeUrl(int $type = null): string
    {
        return static::getConfiguration($type, 'defaultBenchmarkRelativeUrl');
    }

    public static function getResponseBodyFiles(int $type = null): array
    {
        return static::getConfiguration($type, 'responseBodyFiles');
    }

    public static function getResponseBodyFileMinSize(int $type = null): int
    {
        return static::getConfiguration($type, 'responseBodyFileMinSize');
    }

    public static function getSourceCodeUrlIds(int $type = null, int $componentType = null): array
    {
        $ids = static::getConfiguration($type, 'sourceCodeUrlIds');

        return $ids[$componentType ?? Benchmark::getComponentType()];
    }

    /** @return mixed */
    protected static function getConfiguration(?int $type, string $name)
    {
        $type = $type ?? Benchmark::getBenchmarkType();

        if (array_key_exists($type, static::CONFIGURATIONS) === false) {
            throw new \Exception('Unknown benchmark type ' . $type . '.');
        } elseif (array_key_exists($name, static::CONFIGURATIONS[$type]) === false) {
            throw new \Exception('Unknown configuration ' . $name . '.');
        }

        return static::CONFIGURATIONS[$type][$name];
    }
}
