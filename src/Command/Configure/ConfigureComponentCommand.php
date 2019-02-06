<?php

declare(strict_types=1);

namespace App\Command\Configure;

use AbstractComponentConfiguration\AbstractComponentConfiguration;
use App\{Benchmark\BenchmarkType,
    Component\ComponentType,
    ComponentConfiguration\ComponentConfiguration,
    PhpVersion\PhpVersion};

class ConfigureComponentCommand extends AbstractConfigureCommand
{
    use DefineVariableTrait;

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('configure:component')
            ->setDescription('Create .phpbenchmarks/AbstractComponentConfiguration.php and configure it');
    }

    protected function doExecute(): parent
    {
        $this
            ->title('Creation of .phpbenchmarks/AbstractComponentConfiguration.php')
            ->createConfiguration()
            ->runCommand('validate:configuration:component');

        return $this;
    }

    protected function createConfiguration(): self
    {
        $configurationPath = $this->getConfigurationPath() . '/AbstractComponentConfiguration.php';
        if (is_file($configurationPath)) {
            $this->copyDefaultConfigurationFile('AbstractComponentConfiguration.php', true);
        }

        $benchmarkType = null;
        if (is_file($configurationPath) === false) {
            $benchmarkType = $this->createFile();
        }

        $this
            ->defineStringVariable('____NAMESPACE____', 'AbstractComponentConfiguration', $configurationPath)
            ->defineVariable(
                '____PHPBENCHMARKS_COMPONENT_NAME____',
                function () {
                    return $this->question('Component name?');
                },
                $configurationPath
            )
            ->defineVariable(
                '____PHPBENCHMARKS_COMPONENT_SLUG____',
                function () {
                    return $this->question('Component slug?');
                },
                $configurationPath
            )
            ->defineVariable(
                '____PHPBENCHMARKS_BENCHMARK_URL____',
                function () use ($benchmarkType) {
                    return $this->question(
                        'Benchmark url, after host?',
                        BenchmarkType::getDefaultBenchmarkUrl($benchmarkType ?? ComponentConfiguration::getBenchmarkType())
                    );
                },
                $configurationPath
            )
            ->defineVariable(
                '____PHPBENCHMARKS_CORE_DEPENDENCY_MAJOR_VERSION____',
                function () {
                    do {
                        $return = $this->question('Core dependency major version?');
                    } while (is_numeric($return) === false);

                    return $return;
                },
                $configurationPath
            )
            ->defineVariable(
                '____PHPBENCHMARKS_CORE_DEPENDENCY_MINOR_VERSION____',
                function () {
                    do {
                        $return = $this->question('Core dependency minor version?');
                    } while (is_numeric($return) === false);

                    return $return;
                },
                $configurationPath
            )
            ->defineVariable(
                '____PHPBENCHMARKS_CORE_DEPENDENCY_PATCH_VERSION____',
                function () {
                    do {
                        $return = $this->question('Core dependency patch version?');
                    } while (is_numeric($return) === false);

                    return $return;
                },
                $configurationPath
            );

        foreach (PhpVersion::getAll() as $phpVersion) {
            $this->defineVariable(
                '____PHPBENCHMARKS_PHP' . str_replace('.', null, $phpVersion) . '_ENABLED____',
                function () use ($phpVersion) {
                    return $this->confirmationQuestion('Is PHP ' . $phpVersion . ' enabled?')
                        ? 'true'
                        : 'false';
                },
                $configurationPath
            );
        }

        $this->defineCodeDependencyName($configurationPath);

        return $this;
    }

    protected function defineCodeDependencyName(string $configurationPath): self
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
                        $this->error('Error while parsing: ' . $e->getMessage());
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
                    $return = $this->choiceQuestion('Which dependency is the core of the component?', $choices);
                } else {
                    $return = $this->question(
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
            $this->choiceQuestion('Component type?', $componentTypes),
            $componentTypes
        );

        $benchmarkTypes = BenchmarkType::getAll();
        $benchmarkType = array_search(
            $this->choiceQuestion('Benchmark type?', $benchmarkTypes),
            $benchmarkTypes
        );

        $source =
            $this->getTypedDefaultConfigurationPath($componentType, $benchmarkType)
            . '/AbstractComponentConfiguration.php';
        $destination = $this->getInstallationPath() . '/.phpbenchmarks/AbstractComponentConfiguration.php';
        copy($source, $destination);
        $this->success($destination . ' created.');

        return $benchmarkType;
    }
}
