<?php

declare(strict_types=1);

namespace App\ComponentConfiguration;

use App\PhpVersion\PhpVersion;

interface ComponentConfigurationInterface
{
    public static function getComponentType(): int;

    public static function getComponentName(): string;

    public static function getComponentSlug(): string;

    public static function isCompatibleWithPhp(PhpVersion $phpVersion): bool;

    public static function getEntryPointFileName(): string;

    public static function getBenchmarkUrl(): string;

    public static function getCoreDependencyName(): string;

    public static function getCoreDependencyMajorVersion(): int;

    public static function getCoreDependencyMinorVersion(): int;

    public static function getCoreDependencyPatchVersion(): int;

    public static function getBenchmarkType(): int;

    public static function getSourceCodeUrls(): array;
}
