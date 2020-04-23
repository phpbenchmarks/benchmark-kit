<?php

declare(strict_types=1);

namespace App\Benchmark;

use App\{
    Command\Configure\ConfigurePhpBenchmarksCommand,
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

    protected static ?string $componentSlug;

    protected static ?string $componentName;

    protected static ?int $componentType;

    protected static ?int $benchmarkType;

    protected static ?string $sourceCodeEntryPoint;

    protected static ?string $benchmarkRelativeUrl;

    protected static ?StringArray $sourceCodeUrls;

    protected static ?string $coreDependencyName;

    protected static ?string $coreDependencyVersion;

    protected static ?int $coreDependencyMajorVersion;

    protected static ?int $coreDependencyMinorVersion;

    protected static ?int $coreDependencyPatchVersion;

    public static function reload(): void
    {
        static::$loaded = false;
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

    public static function getBenchmarkRelativeUrl(): string
    {
        static::load();

        return static::$benchmarkRelativeUrl;
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

    public static function getResponseBodySize(PhpVersion $phpVersion, bool $showResult): int
    {
        return
            $showResult === false && BenchmarkType::isResultHidden()
                ? 0
                :
                    filesize(
                        Path::getResponseBodyPath($phpVersion)
                            . '/'
                            . BenchmarkType::getResponseBodyFiles(static::getBenchmarkType())[0]
                    );
    }

    public static function getPublicPath(): string
    {
        if (strlen(static::getSourceCodeEntryPoint()) === 0) {
            throw new \Exception('Unable to find source code entry point path.');
        }

        return dirname(static::getSourceCodeEntryPoint());
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

            static::$componentSlug = $config['component']['slug'];
            static::$componentName = Component::getName(static::$componentSlug);
            static::$componentType = Component::getType(static::$componentSlug);

            static::$benchmarkType = $config['benchmark']['type'];
            static::$benchmarkRelativeUrl = $config['benchmark']['relativeUrl'];

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
                    . ConfigurePhpBenchmarksCommand::getDefaultName()
                    . '" to create it.'
            );
        }

        static::$loaded = true;
    }
}
