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
    public const URL_TEMPLATING_LAYOUT = 'templatingLayout';
    public const URL_TEMPLATING_BLOCKS = 'templatingBlocks';
    public const URL_TEMPLATING_FUNCTIONS = 'templatingFunctions';
    public const URL_TEMPLATING_MACROS = 'templatingMacros';
    public const URL_TEMPLATING_ESCAPE_STRING_HTML = 'templatingEscapeStringHtml';
    public const URL_TEMPLATING_ESCAPE_STRING_JS = 'templatingEscapeStringJs';
    public const URL_TEMPLATING_VARIABLES = 'templatingVariables';
    public const URL_TEMPLATING_OUTPUT_RAW = 'templatingOutputRaw';
    public const URL_TEMPLATING_OUTPUT_UNKNOWN_VARIABLES = 'templatingOutputUnknownVariables';
    public const URL_TEMPLATING_OUTPUT_METHOD_CALLS = 'templatingOutputMethodCalls';
    public const URL_TEMPLATING_INCLUDE_TEMPLATES = 'templatingIncludeTemplates';

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
        self::URL_CUSTOM_SERIALIZATION => 'URL to custom serializers?',
        self::URL_TEMPLATING_LAYOUT => 'URL to code who use the layout?',
        self::URL_TEMPLATING_BLOCKS => 'URL to code who call blocks?',
        self::URL_TEMPLATING_FUNCTIONS => 'URL to code who call functions?',
        self::URL_TEMPLATING_MACROS => 'URL to code who call macros?',
        self::URL_TEMPLATING_ESCAPE_STRING_HTML => 'URL to code who escape strings for HTML?',
        self::URL_TEMPLATING_ESCAPE_STRING_JS => 'URL to code who escape strings for JS?',
        self::URL_TEMPLATING_VARIABLES => 'URL to code who assign variables?',
        self::URL_TEMPLATING_OUTPUT_RAW => 'URL to code who output raw string?',
        self::URL_TEMPLATING_OUTPUT_UNKNOWN_VARIABLES => 'URL to code who output unknown variables?',
        self::URL_TEMPLATING_OUTPUT_METHOD_CALLS => 'URL to code who output method calls?',
        self::URL_TEMPLATING_INCLUDE_TEMPLATES => 'URL to code who include templates?'
    ];
}
