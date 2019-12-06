<?php

declare(strict_types=1);

namespace App\Component;

class Component
{
    protected const COMPONENTS = [
        1 => [
            'name' => 'PHP',
            'slug' => 'php',
            'type' => ComponentType::PHP
        ],
        2 => [
            'name' => 'Symfony',
            'slug' => 'symfony',
            'type' => ComponentType::FRAMEWORK
        ],
        3 => [
            'name' => 'Laravel',
            'slug' => 'laravel',
            'type' => ComponentType::FRAMEWORK
        ],
        5 => [
            'name' => 'Zend Framework',
            'slug' => 'zend-framework',
            'type' => ComponentType::FRAMEWORK
        ],
        6 => [
            'name' => 'CakePHP',
            'slug' => 'cake-php',
            'type' => ComponentType::FRAMEWORK
        ],
        7 => [
            'name' => 'Twig',
            'slug' => 'twig',
            'type' => ComponentType::TEMPLATE_ENGINE
        ],
        8 => [
            'name' => 'Plates',
            'slug' => 'plates',
            'type' => ComponentType::TEMPLATE_ENGINE
        ],
        9 => [
            'name' => 'Smarty',
            'slug' => 'smarty',
            'type' => ComponentType::TEMPLATE_ENGINE
        ],
        10 => [
            'name' => 'Latte',
            'slug' => 'latte',
            'type' => ComponentType::TEMPLATE_ENGINE
        ],
        11 => [
            'name' => 'Symlex',
            'slug' => 'symlex',
            'type' => ComponentType::FRAMEWORK
        ],
        12 => [
            'name' => 'CodeIgniter',
            'slug' => 'code-igniter',
            'type' => ComponentType::FRAMEWORK
        ],
        13 => [
            'name' => 'Ubiquity',
            'slug' => 'Ubiquity',
            'type' => ComponentType::FRAMEWORK
        ],
        14 => [
            'name' => 'PHPixie',
            'slug' => 'phpixie',
            'type' => ComponentType::FRAMEWORK
        ],
        15 => [
            'name' => 'Yii',
            'slug' => 'Yii',
            'type' => ComponentType::FRAMEWORK
        ],
        16 => [
            'name' => 'Symfony JSON serializer',
            'slug' => 'symfony-json-serializer',
            'type' => ComponentType::JSON_SERIALIZER
        ]
    ];

    public static function getAll(): array
    {
        $return = [];
        foreach (static::COMPONENTS as $id => $configuration) {
            $return[$id] = static::getName($id);
        }

        return $return;
    }

    public static function getByType(int $type): array
    {
        $return = [];
        foreach (static::COMPONENTS as $id => $configuration) {
            if (static::getType($id) === $type) {
                $return[$id] = static::getName($id);
            }
        }
        asort($return);

        return $return;
    }

    public static function getName(int $id): string
    {
        return static::getConfiguration($id)['name'];
    }

    public static function getSlug(int $id): string
    {
        return static::getConfiguration($id)['slug'];
    }

    public static function getType(int $id): int
    {
        return static::getConfiguration($id)['type'];
    }

    protected static function getConfiguration(int $id): array
    {
        if (array_key_exists($id, static::COMPONENTS) === false) {
            throw new \Exception('Unknown component id "' . $id . '".');
        }

        return static::COMPONENTS[$id];
    }
}
