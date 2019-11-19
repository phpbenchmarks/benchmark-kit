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
            ->assertCallMethod('getComponentName', '____PHPBENCHMARKS_COMPONENT_NAME____')
            ->assertCallMethod('getComponentSlug', '____PHPBENCHMARKS_COMPONENT_SLUG____')
            ->assertPhpVersionsCompatibles()
            ->assertCallMethod('getBenchmarkUrl', '____PHPBENCHMARKS_BENCHMARK_URL____')
            ->assertCallMethod('getCoreDependencyName', '____PHPBENCHMARKS_CORE_DEPENDENCY_NAME____')
            ->assertCallMethod('getCoreDependencyMajorVersion', '____PHPBENCHMARKS_CORE_DEPENDENCY_MAJOR_VERSION____')
            ->assertCallMethod('getCoreDependencyMinorVersion', '____PHPBENCHMARKS_CORE_DEPENDENCY_MINOR_VERSION____')
            ->assertCallMethod('getCoreDependencyPatchVersion', '____PHPBENCHMARKS_CORE_DEPENDENCY_PATCH_VERSION____')
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

    /** @param mixed $shouldNotReturn */
    private function assertCallMethod(string $method, $shouldNotReturn): self
    {
        $value = ComponentConfiguration::{$method}();
        if ($value === $shouldNotReturn) {
            throw new \Exception(
                'Configuration::' . $method . '() should not return ' . (string) $shouldNotReturn . '.'
            );
        }

        $valueStr = is_bool($value) ? ($value ? 'true' : 'false') : $value;
        $this->outputSuccess($method . '() return ' . $valueStr . '.');

        return $this;
    }

    private function assertPhpVersionsCompatibles(): self
    {
        foreach (PhpVersion::getAllWithoutDot() as $phpVersion) {
            $this->assertCallMethod(
                'isPhp' . $phpVersion . 'Compatible',
                '____PHPBENCHMARKS_PHP' . $phpVersion . '_COMPATIBLE____'
            );
        }

        return $this;
    }
}
