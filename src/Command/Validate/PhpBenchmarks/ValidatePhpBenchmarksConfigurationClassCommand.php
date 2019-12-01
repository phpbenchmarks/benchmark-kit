<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksConfigurationClassCommand,
    Component\ComponentType,
    ComponentConfiguration\ComponentConfiguration,
    PhpVersion\PhpVersion,
    Utils\Path
};

final class ValidatePhpBenchmarksConfigurationClassCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:phpbenchmarks:configurationClass';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription(
            'Validate ' . Path::rmPrefix(Path::getBenchmarkConfigurationClassPath())
        );
    }

    protected function doExecute(): parent
    {
        return $this
            ->outputTitle(
                'Validation of ' . Path::rmPrefix(Path::getBenchmarkConfigurationClassPath())
            )
            ->assertInArray('getComponentType', ComponentType::getAll())
            ->assertCallMethod('getComponentName')
            ->assertCallMethod('getComponentSlug')
            ->assertPhpVersionsCompatibles()
            ->assertEntryPoint()
            ->assertCallMethod('getBenchmarkUrl')
            ->assertCallMethod('getCoreDependencyName')
            ->assertCallMethod('getCoreDependencyMajorVersion')
            ->assertCallMethod('getCoreDependencyMinorVersion')
            ->assertCallMethod('getCoreDependencyPatchVersion')
            ->assertInArray(
                'getBenchmarkType',
                BenchmarkType::getByComponentType(ComponentConfiguration::getComponentType())
            );
    }

    protected function onError(): parent
    {
        $this->outputWarning(
            'You can call "phpbenchkit '
                . ConfigurePhpBenchmarksConfigurationClassCommand::getDefaultName()
                . '" to create Configuration class.'
        );

        return $this;
    }

    private function assertInArray(string $method, array $allowedValues): self
    {
        $value = ComponentConfiguration::{$method}();
        if (array_key_exists($value, $allowedValues) === false) {
            $allowedValuesError = [];
            foreach ($allowedValues as $allowedValue => $allowedValueDescription) {
                $allowedValuesError[] = $allowedValue . ' (' . $allowedValueDescription . ')';
            }
            throw new \Exception($method . '() should return a value in ' . implode(', ', $allowedValuesError) . '.');
        }

        return $this->outputSuccess($method . '() return ' . $value . ' (' . $allowedValues[$value] . ').');
    }

    /** @param mixed $shouldNotReturn */
    private function assertCallMethod(string $method, $parameters = [], string $outputParameters = null): self
    {
        $value = ComponentConfiguration::{$method}(...$parameters);
        $valueStr = is_bool($value) ? ($value ? 'true' : 'false') : $value;
        $outputParameters = $outputParameters === null ? implode(', ', $parameters) : $outputParameters;

        return $this->outputSuccess("$method($outputParameters) return $valueStr.");
    }

    private function assertPhpVersionsCompatibles(): self
    {
        foreach (PhpVersion::getAll() as $phpVersion) {
            $this->assertCallMethod('isCompatibleWithPhp', [$phpVersion], $phpVersion->toString());
        }

        return $this;
    }

    private function assertEntryPoint(): self
    {
        $entryPointFileName = ComponentConfiguration::getEntryPointFileName();

        if (is_readable(Path::getBenchmarkPath() . '/' . $entryPointFileName) === false) {
            throw new \Exception("getEntryPoint() return $entryPointFileName who is not readable.");
        }

        return $this->outputSuccess("getEntryPoint() return $entryPointFileName who is readable.");
    }
}
