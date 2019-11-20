<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Component\ComponentType,
    ComponentConfiguration\ComponentConfiguration,
    PhpVersion\PhpVersion
};

class ConfigureConfigurationClassCommand extends AbstractConfigureCommand
{
    use DefineVariableTrait;

    /** @var string */
    protected static $defaultName = 'configure:configuration-class';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create ' . $this->getConfigurationFilePath(true) . ' and configure it');
    }

    protected function doExecute(): AbstractCommand
    {
        $this
            ->outputTitle('Creation of ' . $this->getConfigurationFilePath(true))
            ->createConfiguration();

        return $this;
    }

    protected function createConfiguration(): self
    {
        $configurationPath = $this->getConfigurationFilePath();
        if (is_file($configurationPath)) {
            $this->copyDefaultConfigurationFile('Configuration.php', true);
        }

        $benchmarkType = null;
        if (is_file($configurationPath) === false) {
            $benchmarkType = $this->createFile();
        }

        $this
            ->defineVariable(
                '____PHPBENCHMARKS_COMPONENT_NAME____',
                function () {
                    return $this->askQuestion('Component name (exemple: Symfony, Zend Framework)?');
                },
                $configurationPath
            )
            ->defineVariable(
                '____PHPBENCHMARKS_COMPONENT_SLUG____',
                function () {
                    return $this->askQuestion('Component slug (exemple: symfony, zend-framework)?');
                },
                $configurationPath
            )
            ->defineVariable(
                '____PHPBENCHMARKS_BENCHMARK_URL____',
                function () use ($benchmarkType) {
                    return $this->askQuestion(
                        'Benchmark url, after host?',
                        BenchmarkType::getDefaultBenchmarkUrl(
                            $benchmarkType ?? ComponentConfiguration::getBenchmarkType()
                        )
                    );
                },
                $configurationPath
            )
            ->defineVariable(
                '____PHPBENCHMARKS_CORE_DEPENDENCY_MAJOR_VERSION____',
                function () {
                    do {
                        $return = $this->askQuestion('Core dependency major version?');
                    } while (is_numeric($return) === false);

                    return $return;
                },
                $configurationPath
            )
            ->defineVariable(
                '____PHPBENCHMARKS_CORE_DEPENDENCY_MINOR_VERSION____',
                function () {
                    do {
                        $return = $this->askQuestion('Core dependency minor version?');
                    } while (is_numeric($return) === false);

                    return $return;
                },
                $configurationPath
            )
            ->defineVariable(
                '____PHPBENCHMARKS_CORE_DEPENDENCY_PATCH_VERSION____',
                function () {
                    do {
                        $return = $this->askQuestion('Core dependency patch version?');
                    } while (is_numeric($return) === false);

                    return $return;
                },
                $configurationPath
            )
            ->defineCoreDependencyName($configurationPath);

        if ($this->hasVariable('____PHPBENCHMARKS_PHP_COMPATIBLE____', $configurationPath)) {
            $compatibles = [];
            foreach (PhpVersion::getAll() as $phpVersion) {
                $parts = explode('.', $phpVersion);
                if ($this->askConfirmationQuestion('Is PHP ' . $phpVersion . ' compatible?')) {
                    $compatibles[] = '($major === ' . $parts[0] . ' && $minor === ' . $parts[1] . ')';
                }
            }

            $this->defineVariable(
                '____PHPBENCHMARKS_PHP_COMPATIBLE____',
                function () use ($compatibles) {
                    return implode("\n" . '            || ', $compatibles);
                },
                $configurationPath
            );
        }

        return $this;
    }

    protected function defineCoreDependencyName(string $configurationPath): self
    {
        $this->defineVariable(
            '____PHPBENCHMARKS_CORE_DEPENDENCY_NAME____',
            function () {
                $composerPath = $this->getInstallationPath() . '/composer.json';
                if (is_file($composerPath)) {
                    try {
                        $data = json_decode(
                            file_get_contents($composerPath),
                            true,
                            512,
                            JSON_THROW_ON_ERROR
                        );
                    } catch (\Throwable $e) {
                        $this->throwError('Error while parsing: ' . $e->getMessage());
                    }

                    $choices = array_keys($data['require'] ?? []);
                    foreach ($choices as $key => $choice) {
                        if (
                            $choice === 'php'
                            || substr($choice, 0, 4) === 'ext-'
                            || substr($choice, 0, 14) === 'phpbenchmarks/'
                        ) {
                            unset($choices[$key]);
                        }
                    }
                    $return = $this->askChoiceQuestion('Which dependency is the core of the component?', $choices);
                } else {
                    $return = $this->askQuestion(
                        'Core dependency name of component? Example: symfony/framework-bundle, cakephp/cakephp'
                    );
                }

                return $return;
            },
            $configurationPath
        );

        return $this;
    }

    protected function createFile(): int
    {
        $componentTypes = ComponentType::getAll();
        $componentType = array_search(
            $this->askChoiceQuestion('Component type?', $componentTypes),
            $componentTypes
        );

        $benchmarkTypes = BenchmarkType::getByComponentType($componentType);
        $benchmarkType = array_search(
            $this->askChoiceQuestion('Benchmark type?', $benchmarkTypes),
            $benchmarkTypes
        );

        $source =
            $this->getTypedDefaultConfigurationPath($componentType, $benchmarkType)
            . '/Configuration.php';
        copy($source, $this->getConfigurationFilePath());
        $this->outputSuccess($this->getConfigurationFilePath(true) . ' created.');

        return $benchmarkType;
    }
}
