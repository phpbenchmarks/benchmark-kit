<?php

declare(strict_types=1);

namespace App\Component;

class ComponentType
{
    public const PHP = 1;
    public const FRAMEWORK = 2;
    public const TEMPLATE_ENGINE = 3;

    public static function getAll(): array
    {
        return [
            static::PHP => 'PHP',
            static::FRAMEWORK => 'Framework',
            static::TEMPLATE_ENGINE => 'Template engine',
        ];
    }

    public static function getCamelCaseName(int $type): string
    {
        $names = [
            static::PHP => 'Php',
            static::FRAMEWORK => 'Framework',
            static::TEMPLATE_ENGINE => 'TemplateEngine'
        ];
        if (array_key_exists($type, $names) === false) {
            throw new \Exception('Unknown component type ' . $type . '.');
        }

        return $names[$type];
    }
}
