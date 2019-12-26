<?php

declare(strict_types=1);

namespace App\Benchmark;

use App\{
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksConfigCommand,
    Component\Component,
    PhpVersion\PhpVersion,
    PhpVersion\PhpVersionArray,
    Utils\Path
};
use steevanb\PhpTypedArray\ScalarArray\StringArray;
use Symfony\Component\Yaml\Yaml;

class Benchmark
{
    protected static bool $loaded = false;

    protected static ?int $componentId;

    protected static ?string $componentName;

    protected static ?string $componentSlug;

    protected static ?int $componentType;

    protected static ?int $benchmarkType;

    protected static ?string $sourceCodeEntryPoint;

    protected static ?string $benchmarkUrl;

    protected static ?StringArray $sourceCodeUrls;

    protected static ?string $coreDependencyName;

    protected static ?string $coreDependencyVersion;

    protected static ?int $coreDependencyMajorVersion;

    protected static ?int $coreDependencyMinorVersion;

    protected static ?int $coreDependencyPatchVersion;

    public static function getComponentId(): int
    {
        static::load();

        return static::$componentId;
    }

    public static function getComponentName(): string
    {
        static::load();

        return static::$componentName;
    }

    public static function getComponentSlug(): string
    {
        static::load();

        return static::$componentSlug;
    }

    public static function getComponentType(): int
    {
        static::load();

        return static::$componentType;
    }

    public static function getBenchmarkType(): int
    {
        static::load();

        return static::$benchmarkType;
    }

    public static function getBenchmarkUrl(): string
    {
        static::load();

        return static::$benchmarkUrl;
    }

    public static function getSourceCodeEntryPoint(): string
    {
        static::load();

        return static::$sourceCodeEntryPoint;
    }

    public static function getSourceCodeUrls(): StringArray
    {
        static::load();

        return static::$sourceCodeUrls;
    }

    public static function getCoreDependencyName(): string
    {
        static::load();

        return static::$coreDependencyName;
    }

    public static function getCoreDependencyVersion(): string
    {
        static::load();

        return static::$coreDependencyVersion;
    }

    public static function getCoreDependencyMajorVersion(): int
    {
        static::load();

        return static::$coreDependencyMajorVersion;
    }

    public static function getCoreDependencyMinorVersion(): int
    {
        static::load();

        return static::$coreDependencyMinorVersion;
    }

    public static function getCoreDependencyPatchVersion(): int
    {
        static::load();

        return static::$coreDependencyPatchVersion;
    }

    public static function getCompatiblesPhpVersions(): PhpVersionArray
    {
        $return = new PhpVersionArray();
        foreach (PhpVersion::getAll() as $phpVersion) {
            if (static::isCompatibleWithPhp($phpVersion)) {
                $return[] = new PhpVersion($phpVersion->getMajor(), $phpVersion->getMinor());
            }
        }

        return $return;
    }

    public static function getIncompatiblesPhpVersions(): PhpVersionArray
    {
        $return = new PhpVersionArray();
        foreach (PhpVersion::getAll() as $phpVersion) {
            if (static::isCompatibleWithPhp($phpVersion) === false) {
                $return[] = new PhpVersion($phpVersion->getMajor(), $phpVersion->getMinor());
            }
        }

        return $return;
    }

    public static function getResponseBodySize(PhpVersion $phpVersion): int
    {
        return filesize(
            Path::getResponseBodyPath($phpVersion)
                . '/'
                . BenchmarkType::getResponseBodyFiles(static::getBenchmarkType())[0]
        );
    }

    protected static function isCompatibleWithPhp(PhpVersion $phpVersion): bool
    {
        return is_dir(Path::getPhpConfigurationPath($phpVersion));
    }

    protected static function load(): void
    {
        if (static::$loaded) {
            return;
        }

        static::$sourceCodeUrls = new StringArray();
        try {
            $config = Yaml::parseFile(Path::getConfigFilePath());

            static::$componentId = $config['component']['id'];
            static::$componentName = Component::getName(static::$componentId);
            static::$componentSlug = Component::getSlug(static::$componentId);
            static::$componentType = Component::getType(static::$componentId);

            static::$benchmarkType = $config['benchmark']['type'];
            static::$benchmarkUrl = $config['benchmark']['url'];

            static::$sourceCodeEntryPoint = $config['sourceCode']['entryPoint'];
            static::$sourceCodeUrls = new StringArray($config['sourceCode']['urls'] ?? []);

            static::$coreDependencyName = $config['coreDependency']['name'];
            static::$coreDependencyVersion = $config['coreDependency']['version'];
            $semanticVersionParts = explode('.', $config['coreDependency']['version']);
            if (count($semanticVersionParts) !== 3) {
                throw new \Exception(
                    Path::rmPrefix(Path::getConfigFilePath()) . '::coreDependency.version is not a semantic version.'
                );
            }
            static::$coreDependencyMajorVersion = (int) $semanticVersionParts[0];
            static::$coreDependencyMinorVersion = (int) $semanticVersionParts[1];
            static::$coreDependencyPatchVersion = (int) $semanticVersionParts[2];
        } catch (\Throwable $exception) {
            throw new \Exception(
                'Unable to parse ' . Path::rmPrefix(Path::getConfigFilePath())
                    . '. Use "phpbenchkit '
                    . ConfigurePhpBenchmarksConfigCommand::getDefaultName()
                    . '" to create it.'
            );
        }

        static::$loaded = true;
    }
}
