<?php

declare(strict_types=1);

namespace App\Benchmark;

use App\{
    Component\ComponentType,
    SourceCodeUrl\SourceCodeUrl
};

class BenchmarkType
{
    public const HELLO_WORLD = 'hello-world';
    public const REST_API = 'rest-api';
    public const TEMPLATING_SMALL_OVERLOAD = 'templating-small-overload';
    public const TEMPLATING_BIG_OVERLOAD = 'templating-big-overload';
    public const JSON_SERIALIZATION_HELLO_WORLD = 'json-serialization-hello-world';
    public const JSON_SERIALIZATION_SMALL_OVERLOAD = 'json-serialization-small-overload';
    public const JSON_SERIALIZATION_BIG_OVERLOAD = 'json-serialization-big-overload';

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
            ],
            'resultHidden' => false
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
            ],
            'resultHidden' => false
        ],
        self::TEMPLATING_SMALL_OVERLOAD => [
            'name' => 'Template engine small overload',
            'camelCaseName' => 'templateEngineSmallOverload',
            'slug' => 'templating-small-overload',
            'defaultBenchmarkRelativeUrl' => '/',
            'responseBodyFiles' => ['responseBody.html'],
            'responseBodyFileMinSize' => 520000,
            'sourceCodeUrlIds' => [
                ComponentType::PHP => [
                    SourceCodeUrl::URL_TEMPLATE,
                    SourceCodeUrl::URL_TEMPLATING_LAYOUT,
                    SourceCodeUrl::URL_TEMPLATING_BLOCKS,
                    SourceCodeUrl::URL_TEMPLATING_FUNCTIONS,
                    SourceCodeUrl::URL_TEMPLATING_MACROS,
                    SourceCodeUrl::URL_TEMPLATING_ESCAPE_STRING_HTML,
                    SourceCodeUrl::URL_TEMPLATING_ESCAPE_STRING_JS,
                    SourceCodeUrl::URL_TEMPLATING_VARIABLES,
                    SourceCodeUrl::URL_TEMPLATING_OUTPUT_RAW,
                    SourceCodeUrl::URL_TEMPLATING_OUTPUT_UNKNOWN_VARIABLES,
                    SourceCodeUrl::URL_TEMPLATING_OUTPUT_METHOD_CALLS,
                    SourceCodeUrl::URL_TEMPLATING_INCLUDE_TEMPLATES,
                ],
                ComponentType::TEMPLATE_ENGINE => [
                    SourceCodeUrl::URL_TEMPLATE,
                    SourceCodeUrl::URL_TEMPLATING_LAYOUT,
                    SourceCodeUrl::URL_TEMPLATING_BLOCKS,
                    SourceCodeUrl::URL_TEMPLATING_FUNCTIONS,
                    SourceCodeUrl::URL_TEMPLATING_MACROS,
                    SourceCodeUrl::URL_TEMPLATING_ESCAPE_STRING_HTML,
                    SourceCodeUrl::URL_TEMPLATING_ESCAPE_STRING_JS,
                    SourceCodeUrl::URL_TEMPLATING_VARIABLES,
                    SourceCodeUrl::URL_TEMPLATING_OUTPUT_RAW,
                    SourceCodeUrl::URL_TEMPLATING_OUTPUT_UNKNOWN_VARIABLES,
                    SourceCodeUrl::URL_TEMPLATING_OUTPUT_METHOD_CALLS,
                    SourceCodeUrl::URL_TEMPLATING_INCLUDE_TEMPLATES,
                ]
            ],
            'resultHidden' => false
        ],
        self::TEMPLATING_BIG_OVERLOAD => [
            'name' => 'Template engine big overload',
            'camelCaseName' => 'templateEngineBigOverload',
            'slug' => 'templating-big-overload',
            'defaultBenchmarkRelativeUrl' => '/',
            'responseBodyFiles' => ['responseBody.html'],
            'responseBodyFileMinSize' => 5200000,
            'sourceCodeUrlIds' => [
                ComponentType::PHP => [
                    SourceCodeUrl::URL_TEMPLATE,
                    SourceCodeUrl::URL_TEMPLATING_LAYOUT,
                    SourceCodeUrl::URL_TEMPLATING_BLOCKS,
                    SourceCodeUrl::URL_TEMPLATING_FUNCTIONS,
                    SourceCodeUrl::URL_TEMPLATING_MACROS,
                    SourceCodeUrl::URL_TEMPLATING_ESCAPE_STRING_HTML,
                    SourceCodeUrl::URL_TEMPLATING_ESCAPE_STRING_JS,
                    SourceCodeUrl::URL_TEMPLATING_VARIABLES,
                    SourceCodeUrl::URL_TEMPLATING_OUTPUT_RAW,
                    SourceCodeUrl::URL_TEMPLATING_OUTPUT_UNKNOWN_VARIABLES,
                    SourceCodeUrl::URL_TEMPLATING_OUTPUT_METHOD_CALLS,
                    SourceCodeUrl::URL_TEMPLATING_INCLUDE_TEMPLATES,
                ],
                ComponentType::TEMPLATE_ENGINE => [
                    SourceCodeUrl::URL_TEMPLATE,
                    SourceCodeUrl::URL_TEMPLATING_LAYOUT,
                    SourceCodeUrl::URL_TEMPLATING_BLOCKS,
                    SourceCodeUrl::URL_TEMPLATING_FUNCTIONS,
                    SourceCodeUrl::URL_TEMPLATING_MACROS,
                    SourceCodeUrl::URL_TEMPLATING_ESCAPE_STRING_HTML,
                    SourceCodeUrl::URL_TEMPLATING_ESCAPE_STRING_JS,
                    SourceCodeUrl::URL_TEMPLATING_VARIABLES,
                    SourceCodeUrl::URL_TEMPLATING_OUTPUT_RAW,
                    SourceCodeUrl::URL_TEMPLATING_OUTPUT_UNKNOWN_VARIABLES,
                    SourceCodeUrl::URL_TEMPLATING_OUTPUT_METHOD_CALLS,
                    SourceCodeUrl::URL_TEMPLATING_INCLUDE_TEMPLATES,
                ]
            ],
            'resultHidden' => false
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
            ],
            'resultHidden' => true
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
            ],
            'resultHidden' => true
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
            ],
            'resultHidden' => true
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
        $types = static::getAll();

        return [
            ComponentType::PHP => [
                static::HELLO_WORLD => $types[static::HELLO_WORLD],
                static::REST_API => $types[static::REST_API],
                static::TEMPLATING_SMALL_OVERLOAD => $types[static::TEMPLATING_SMALL_OVERLOAD],
                static::TEMPLATING_BIG_OVERLOAD => $types[static::TEMPLATING_BIG_OVERLOAD],
                static::JSON_SERIALIZATION_HELLO_WORLD => $types[static::JSON_SERIALIZATION_HELLO_WORLD],
                static::JSON_SERIALIZATION_SMALL_OVERLOAD => $types[static::JSON_SERIALIZATION_SMALL_OVERLOAD],
                static::JSON_SERIALIZATION_BIG_OVERLOAD => $types[static::JSON_SERIALIZATION_BIG_OVERLOAD]
            ],
            ComponentType::FRAMEWORK => [
                static::HELLO_WORLD => $types[static::HELLO_WORLD],
                static::REST_API => $types[static::REST_API],
            ],
            ComponentType::TEMPLATE_ENGINE => [
                static::HELLO_WORLD => $types[static::HELLO_WORLD]
            ],
            ComponentType::JSON_SERIALIZER => [
                static::JSON_SERIALIZATION_HELLO_WORLD => $types[static::JSON_SERIALIZATION_HELLO_WORLD],
                static::JSON_SERIALIZATION_SMALL_OVERLOAD => $types[static::JSON_SERIALIZATION_SMALL_OVERLOAD],
                static::JSON_SERIALIZATION_BIG_OVERLOAD => $types[static::JSON_SERIALIZATION_BIG_OVERLOAD]
            ]
        ];
    }

    public static function getByComponentType(string $componentType): array
    {
        return static::getAllByComponentType()[$componentType];
    }

    public static function getName(string $type = null): string
    {
        return static::getConfiguration($type, 'name');
    }

    public static function getSlug(string $type = null): string
    {
        return static::getConfiguration($type, 'slug');
    }

    public static function getCamelCaseName(string $type = null): string
    {
        return static::getConfiguration($type, 'camelCaseName');
    }

    public static function getDefaultBenchmarkRelativeUrl(string $type = null): string
    {
        return static::getConfiguration($type, 'defaultBenchmarkRelativeUrl');
    }

    public static function getResponseBodyFiles(string $type = null): array
    {
        return static::getConfiguration($type, 'responseBodyFiles');
    }

    public static function getResponseBodyFileMinSize(string $type = null): int
    {
        return static::getConfiguration($type, 'responseBodyFileMinSize');
    }

    public static function getSourceCodeUrlIds(string $type = null, string $componentType = null): array
    {
        $ids = static::getConfiguration($type, 'sourceCodeUrlIds');

        return $ids[$componentType ?? Benchmark::getComponentType()];
    }

    public static function isResultHidden(string $type = null): bool
    {
        return static::getConfiguration($type, 'resultHidden');
    }

    /** @return mixed */
    protected static function getConfiguration(?string $type, string $name)
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
