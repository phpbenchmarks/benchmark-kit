<?php

declare(strict_types=1);

namespace App\ComponentConfiguration;

interface ComponentConfigurationInterface
{
    public static function getComponentType(): int;
    public static function getComponentName(): string;
    public static function getComponentSlug(): string;

    public static function isPhp56Enabled(): bool;
    public static function isPhp70Enabled(): bool;
    public static function isPhp71Enabled(): bool;
    public static function isPhp72Enabled(): bool;
    public static function isPhp73Enabled(): bool;

    public static function getBenchmarkUrl(): string;

    public static function getCoreDependencyName(): string;
    public static function getCoreDependencyMajorVersion(): int;
    public static function getCoreDependencyMinorVersion(): int;
    public static function getCoreDependencyPatchVersion(): int;

    public static function getBenchmarkType(): int;

    public static function getSourceCodeUrls(): array;
}
