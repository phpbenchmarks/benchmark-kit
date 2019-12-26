<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksConfigCommand,
    Benchmark\Benchmark,
    Utils\Path
};

final class ValidatePhpBenchmarksConfigCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:phpbenchmarks:config';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . Path::rmPrefix(Path::getConfigFilePath()));
    }

    protected function doExecute(): parent
    {
        return $this
            ->outputTitle(
                'Validation of ' . Path::rmPrefix(Path::getConfigFilePath())
            )
            ->assertCallMethod('getComponentName', 'component.id', false)
            ->assertCallMethod('getComponentSlug', 'component.id')
            ->assertBenchmarkType()
            ->assertCallMethod('getBenchmarkUrl', 'benchmark.url')
            ->assertCallMethod('getSourceCodeEntryPoint', 'sourceCode.entryPoint')
            ->assertCallMethod('getCoreDependencyName', 'coreDependency.name')
            ->assertCallMethod('getCoreDependencyVersion', 'coreDependency.version')
            ->assertCallMethod('getCoreDependencyMajorVersion', 'coreDependency.version')
            ->assertCallMethod('getCoreDependencyMinorVersion', 'coreDependency.version')
            ->assertCallMethod('getCoreDependencyPatchVersion', 'coreDependency.version');
    }

    protected function onError(): parent
    {
        return $this->outputWarning(
            'You can call "phpbenchkit '
                . ConfigurePhpBenchmarksConfigCommand::getDefaultName()
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
