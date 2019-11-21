<?php

declare(strict_types=1);

namespace App\ComponentConfiguration;

interface ComponentConfigurationInterface
{
    public static function getComponentType(): int;

    public static function getComponentName(): string;

    public static function getComponentSlug(): string;

    public static function isCompatibleWithPhp(int $major, int $minor): bool;

    public static function getBenchmarkUrl(): string;

    public static function getCoreDependencyName(): string;

    public static function getCoreDependencyMajorVersion(): int;

    public static function getCoreDependencyMinorVersion(): int;

    public static function getCoreDependencyPatchVersion(): int;

    public static function getBenchmarkType(): int;

    public static function getSourceCodeUrls(): array;
}
