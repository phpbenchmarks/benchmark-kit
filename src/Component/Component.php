<?php

declare(strict_types=1);

namespace App\Component;

class Component
{
    protected const COMPONENTS = [
        'php' => [
            'name' => 'PHP',
            'slug' => 'php',
            'type' => ComponentType::PHP
        ],
        'symfony' => [
            'name' => 'Symfony',
            'slug' => 'symfony',
            'type' => ComponentType::FRAMEWORK
        ],
        'laravel' => [
            'name' => 'Laravel',
            'slug' => 'laravel',
            'type' => ComponentType::FRAMEWORK
        ],
        'zend-framework' => [
            'name' => 'Zend Framework',
            'slug' => 'zend-framework',
            'type' => ComponentType::FRAMEWORK
        ],
        'cake-php' => [
            'name' => 'CakePHP',
            'slug' => 'cake-php',
            'type' => ComponentType::FRAMEWORK
        ],
        'twig' => [
            'name' => 'Twig',
            'slug' => 'twig',
            'type' => ComponentType::TEMPLATE_ENGINE
        ],
        'plates' => [
            'name' => 'Plates',
            'slug' => 'plates',
            'type' => ComponentType::TEMPLATE_ENGINE
        ],
        'smarty' => [
            'name' => 'Smarty',
            'slug' => 'smarty',
            'type' => ComponentType::TEMPLATE_ENGINE
        ],
        'latte' => [
            'name' => 'Latte',
            'slug' => 'latte',
            'type' => ComponentType::TEMPLATE_ENGINE
        ],
        'symlex' => [
            'name' => 'Symlex',
            'slug' => 'symlex',
            'type' => ComponentType::FRAMEWORK
        ],
        'code-igniter' => [
            'name' => 'CodeIgniter',
            'slug' => 'code-igniter',
            'type' => ComponentType::FRAMEWORK
        ],
        'ubiquity' => [
            'name' => 'Ubiquity',
            'slug' => 'Ubiquity',
            'type' => ComponentType::FRAMEWORK
        ],
        'phpixie' => [
            'name' => 'PHPixie',
            'slug' => 'phpixie',
            'type' => ComponentType::FRAMEWORK
        ],
        'yii' => [
            'name' => 'Yii',
            'slug' => 'Yii',
            'type' => ComponentType::FRAMEWORK
        ],
        'symfony-json-serializer' => [
            'name' => 'Symfony JSON serializer',
            'slug' => 'symfony-json-serializer',
            'type' => ComponentType::JSON_SERIALIZER
        ]
    ];

    public static function assertExists(string $slug): void
    {
        if (array_key_exists($slug, static::COMPONENTS) === false) {
            throw new \Exception('Unknown component slug "' . $slug . '".');
        }
    }

    public static function getByType(int $type): array
    {
        $return = [];
        foreach (static::COMPONENTS as $slug => $configuration) {
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

    public static function getSlug(string $slug): string
    {
        return static::getConfiguration($slug)['slug'];
    }

    public static function getType(string $slug): int
    {
        return static::getConfiguration($slug)['type'];
    }

    protected static function getConfiguration(string $slug): array
    {
        static::assertExists($slug);

        return static::COMPONENTS[$slug];
    }
}
