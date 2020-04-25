<?php

declare(strict_types=1);

namespace App\Component;

class Component
{
    public const PHP = 'php';
    public const SYMFONY = 'symfony';
    public const LARAVEL = 'laravel';
    public const ZEND_FRAMEWORK = 'zend-framework';
    public const CAKE_PHP = 'cake-php';
    public const TWIG = 'twig';
    public const PLATES = 'plates';
    public const SMARTY = 'smarty';
    public const LATTE = 'latte';
    public const SYMLEX = 'symlex';
    public const CODE_IGNITER = 'code-igniter';
    public const UBIQUITY = 'ubiquity';
    public const PHPIXIE = 'phpixie';
    public const YII = 'yii';
    public const SYMFONY_JSON_SERIALIZER = 'symfony-json-serializer';

    protected const COMPONENTS = [
        self::PHP => [
            'name' => 'PHP',
            'type' => ComponentType::PHP
        ],
        self::SYMFONY => [
            'name' => 'Symfony',
            'type' => ComponentType::FRAMEWORK
        ],
        self::LARAVEL => [
            'name' => 'Laravel',
            'type' => ComponentType::FRAMEWORK
        ],
        self::ZEND_FRAMEWORK => [
            'name' => 'Zend Framework',
            'type' => ComponentType::FRAMEWORK
        ],
        self::CAKE_PHP => [
            'name' => 'CakePHP',
            'type' => ComponentType::FRAMEWORK
        ],
        self::TWIG => [
            'name' => 'Twig',
            'type' => ComponentType::TEMPLATE_ENGINE
        ],
        self::PLATES => [
            'name' => 'Plates',
            'type' => ComponentType::TEMPLATE_ENGINE
        ],
        self::SMARTY => [
            'name' => 'Smarty',
            'type' => ComponentType::TEMPLATE_ENGINE
        ],
        self::LATTE => [
            'name' => 'Latte',
            'type' => ComponentType::TEMPLATE_ENGINE
        ],
        self::SYMLEX => [
            'name' => 'Symlex',
            'type' => ComponentType::FRAMEWORK
        ],
        self::CODE_IGNITER => [
            'name' => 'CodeIgniter',
            'type' => ComponentType::FRAMEWORK
        ],
        self::UBIQUITY => [
            'name' => 'Ubiquity',
            'type' => ComponentType::FRAMEWORK
        ],
        self::PHPIXIE => [
            'name' => 'PHPixie',
            'type' => ComponentType::FRAMEWORK
        ],
        self::YII => [
            'name' => 'Yii',
            'type' => ComponentType::FRAMEWORK
        ],
        self::SYMFONY_JSON_SERIALIZER => [
            'name' => 'Symfony JSON serializer',
            'type' => ComponentType::JSON_SERIALIZER
        ]
    ];

    public static function assertExists(string $slug): void
    {
        if (array_key_exists($slug, static::COMPONENTS) === false) {
            throw new \Exception('Unknown component slug "' . $slug . '".');
        }
    }

    /** @return string[] */
    public static function getByType(string $type): array
    {
        $return = [];
        foreach (array_keys(static::COMPONENTS) as $slug) {
            if (static::getType($slug) === $type) {
                $return[$slug] = static::getName($slug);
            }
        }
        asort($return);

        return $return;
    }

    public static function getName(string $slug): string
    {
        return static::getConfiguration($slug)['name'];
    }

    public static function getType(string $slug): string
    {
        return static::getConfiguration($slug)['type'];
    }

    /** @return string[] */
    protected static function getConfiguration(string $slug): array
    {
        static::assertExists($slug);

        return static::COMPONENTS[$slug];
    }
}
