<?php

declare(strict_types=1);

namespace App\Command\Validate\Configuration;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Benchmark\Benchmark,
    Command\Configure\ConfigurePhpBenchmarksCommand,
    Utils\Path
};

final class ValidateConfigurationPhpBenchmarksCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:configuration:phpbenchmarks';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . Path::rmPrefix(Path::getConfigFilePath()));
    }

    protected function doExecute(): int
    {
        $this
            ->outputTitle(
                'Validation of ' . Path::rmPrefix(Path::getConfigFilePath())
            )
            ->assertCallMethod('getComponentName', 'component.id', false)
            ->assertCallMethod('getComponentSlug', 'component.id')
            ->assertBenchmarkType()
            ->assertCallMethod('getBenchmarkRelativeUrl', 'benchmark.relativeUrl')
            ->assertCallMethod('getSourceCodeEntryPoint', 'sourceCode.entryPoint')
            ->assertCallMethod('getCoreDependencyName', 'coreDependency.name')
            ->assertCallMethod('getCoreDependencyVersion', 'coreDependency.version')
            ->assertCallMethod('getCoreDependencyMajorVersion', 'coreDependency.version')
            ->assertCallMethod('getCoreDependencyMinorVersion', 'coreDependency.version')
            ->assertCallMethod('getCoreDependencyPatchVersion', 'coreDependency.version');

        return 0;
    }

    protected function onError(): parent
    {
        return $this->outputWarning(
            'You can call "phpbenchkit '
                . ConfigurePhpBenchmarksCommand::getDefaultName()
                . '" to configure ' . Path::rmPrefix(Path::getConfigFilePath()) . '.'
        );
    }

    private function assertBenchmarkType(): self
    {
        $this->assertCallMethod('getBenchmarkType', 'benchmark.type', false);
        if (
            array_key_exists(
                Benchmark::getBenchmarkType(),
                BenchmarkType::getByComponentType(Benchmark::getComponentType())
            ) === false
        ) {
            throw new \Exception('Unknown benchmark type ' . Benchmark::getBenchmarkType() . '.');
        }

        $this->outputSuccess('benchmark.type (' . Benchmark::getBenchmarkType() . ') is valid.');

        return $this;
    }

    private function assertCallMethod(string $method, string $configuration, bool $outputSuccess = true): self
    {
        try {
            $value = Benchmark::$method();
        } catch (\Throwable $exception) {
            throw new \Exception("Invalid configuration: $configuration.", 0, $exception);
        }

        if ($outputSuccess === true) {
            $this->outputSuccess("$configuration ($value) is valid.");
        }

        return $this;
    }
}
