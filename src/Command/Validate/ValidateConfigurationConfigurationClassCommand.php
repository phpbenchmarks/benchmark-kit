<?php

declare(strict_types=1);

namespace App\Command\Validate;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Command\Configure\ConfigureConfigurationClassCommand,
    Component\ComponentType,
    ComponentConfiguration\ComponentConfiguration,
    PhpVersion\PhpVersion
};

final class ValidateConfigurationConfigurationClassCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:configuration:configuration-class';

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
                . ConfigureConfigurationClassCommand::getDefaultName()
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
            $this->throwError($method . '() should return a data among ' . implode(', ', $allowedValuesError) . '.');
        }
        $this->outputSuccess($method . '() return ' . $value . ' (' . $allowedValues[$value] . ').');

        return $this;
    }

    private function assertCallMethod(string $method): self
    {
        $value = ComponentConfiguration::{$method}();
        if (is_bool($value)) {
            $valueStr = $value ? 'true' : 'false';
        } else {
            $valueStr = $value;
        }
        $this->outputSuccess($method . '() return ' . $valueStr . '.');

        return $this;
    }

    private function assertPhpVersionsCompatibles(): self
    {
        foreach (PhpVersion::getAllWithoutDot() as $phpVersion) {
            $this->assertCallMethod('isPhp' . $phpVersion . 'Compatible');
        }

        return $this;
    }
}
