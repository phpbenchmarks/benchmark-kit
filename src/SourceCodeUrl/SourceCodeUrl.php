<?php

declare(strict_types=1);

namespace App\SourceCodeUrl;

class SourceCodeUrl
{
    public const URL_ENTRY_POINT = 'entryPoint';
    public const URL_TEMPLATE = 'template';
    public const URL_ROUTE = 'route';
    public const URL_CONTROLLER = 'controller';
    public const URL_RANDOMIZE_LANGUAGE_DISPATCH_EVENT = 'randomizeLanguageDispatchEvent';
    public const URL_RANDOMIZE_LANGUAGE_EVENT_LISTENER = 'randomizeLanguageEventListener';
    public const URL_TRANSLATIONS = 'translations';
    public const URL_TRANSLATE = 'translate';
    public const URL_SERIALIZE = 'serialize';
    public const URL_JSON_SERIALIZATION = 'jsonSerialization';
    public const URL_INTEGER_SERIALIZATION = 'integerSerialization';
    public const URL_FLOAT_SERIALIZATION = 'floatSerialization';
    public const URL_STRING_SERIALIZATION = 'stringSerialization';
    public const URL_BOOLEAN_SERIALIZATION = 'booleanSerialization';
    public const URL_NULL_SERIALIZATION = 'nullSerialization';
    public const URL_ARRAY_SERIALIZATION = 'arraySerialization';
    public const URL_OBJECT_SERIALIZATION = 'objectSerialization';
    public const URL_CUSTOM_SERIALIZATION = 'customSerializers';

    public const QUESTIONS = [
        self::URL_ENTRY_POINT => 'URL to entry point code?',
        self::URL_TEMPLATE => 'URL to template code?',
        self::URL_ROUTE => 'URL to benchmark route code?',
        self::URL_CONTROLLER => 'URL to Controller code?',
        self::URL_RANDOMIZE_LANGUAGE_DISPATCH_EVENT => 'URL to code who dispatch event to randomize language?',
        self::URL_RANDOMIZE_LANGUAGE_EVENT_LISTENER => 'URL to code who listen event to randomize language?',
        self::URL_TRANSLATIONS => 'URL to en_GB translations code?',
        self::URL_TRANSLATE => 'URL to code who translate translated.1000 key?',
        self::URL_SERIALIZE => 'URL to code who serialize User?',
        self::URL_JSON_SERIALIZATION => 'URL to code who serialize data into JSON?',
        self::URL_INTEGER_SERIALIZATION => 'URL to code who serialize integer?',
        self::URL_FLOAT_SERIALIZATION => 'URL to code who serialize float?',
        self::URL_STRING_SERIALIZATION => 'URL to code who serialize string?',
        self::URL_BOOLEAN_SERIALIZATION => 'URL to code who serialize boolean?',
        self::URL_NULL_SERIALIZATION => 'URL to code who serialize null?',
        self::URL_ARRAY_SERIALIZATION => 'URL to code who serialize array?',
        self::URL_OBJECT_SERIALIZATION => 'URL to code who serialize integer?',
        self::URL_CUSTOM_SERIALIZATION => 'URL to custom serializers?'
    ];
}
