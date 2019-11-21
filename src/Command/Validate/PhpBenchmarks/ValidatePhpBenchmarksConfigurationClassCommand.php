<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Command\Configure\PhpBenchmarks\ConfigurePhpBenchmarksConfigurationClassCommand,
    Component\ComponentType,
    ComponentConfiguration\ComponentConfiguration,
    PhpVersion\PhpVersion
};

final class ValidatePhpBenchmarksConfigurationClassCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:phpbenchmarks:configurationClass';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate ' . $this->getConfigurationFilePath(true));
    }

    protected function doExecute(): parent
    {
        $this
            ->outputTitle('Validation of ' . $this->getConfigurationFilePath(true))
            ->assertInArray('getComponentType', ComponentType::getAll())
            ->assertCallMethod('getComponentName')
            ->assertCallMethod('getComponentSlug')
            ->assertPhpVersionsCompatibles()
            ->assertCallMethod('getBenchmarkUrl')
            ->assertCallMethod('getCoreDependencyName')
            ->assertCallMethod('getCoreDependencyMajorVersion')
            ->assertCallMethod('getCoreDependencyMinorVersion')
            ->assertCallMethod('getCoreDependencyPatchVersion')
            ->assertInArray(
                'getBenchmarkType',
                BenchmarkType::getByComponentType(ComponentConfiguration::getComponentType())
            );

        return $this;
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
            $this->throwError($method . '() should return a value in ' . implode(', ', $allowedValuesError) . '.');
        }
        $this->outputSuccess($method . '() return ' . $value . ' (' . $allowedValues[$value] . ').');

        return $this;
    }

    /** @param mixed $shouldNotReturn */
    private function assertCallMethod(string $method, $parameters = []): self
    {
        $value = ComponentConfiguration::{$method}(...$parameters);
        $valueStr = is_bool($value) ? ($value ? 'true' : 'false') : $value;

        return $this->outputSuccess($method . '(' . implode(', ', $parameters) . ') return ' . $valueStr . '.');
    }

    private function assertPhpVersionsCompatibles(): self
    {
        foreach (PhpVersion::getAll() as $phpVersion) {
            $parts = explode('.', $phpVersion);
            $this->assertCallMethod('isCompatibleWithPhp', [(int) $parts[0], (int) $parts[1]]);
        }

        return $this;
    }
}
