<?php

declare(strict_types=1);

namespace App\Command\Configure;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Component\Component,
    Component\ComponentType,
    Utils\Path
};
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Yaml;

final class ConfigurePhpBenchmarksCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Create ' . Path::rmPrefix(Path::getConfigFilePath()))
            ->addOption('component', null, InputOption::VALUE_REQUIRED, 'Component slug')
            ->addOption('benchmark-type', null, InputOption::VALUE_REQUIRED, 'Benchmark type id')
            ->addOption('entry-point', null, InputOption::VALUE_REQUIRED, 'Entry point file name')
            ->addOption(
                'benchmark-relative-url',
                null,
                InputOption::VALUE_REQUIRED,
                'Benchmark relative url (example: /benchmark/helloworld)'
            )
            ->addOption(
                'core-dependency-name',
                null,
                InputOption::VALUE_REQUIRED,
                'Core dependency name (example: foo/bar)'
            );
    }

    protected function doExecute(): int
    {
        $this->outputTitle('Creation of ' . Path::rmPrefix(Path::getConfigFilePath()));

        $componentSlug = $this->getComponentSlug();
        $benchmarkType = $this->getBenchmarkType($componentSlug);
        $sourceCodeEntryPoint = $this->getSourceCodeEntryPoint();
        $benchmarkRelativeUrl = $this->getBenchmarkRelativeUrl($benchmarkType);
        $coreDependency = $this->getCoreDependency($componentSlug);
        while ($this->validateVersion($coreDependency['version']) === false) {
            $coreDependency['version'] = $this->askQuestion('Core dependency version?');
            if ($this->validateVersion($coreDependency['version']) === false) {
                $this->outputError('Invalid semantic version (example: X.Y.Z).');
            }
        }

        try {
            $currentConfig = Yaml::parseFile(Path::getConfigFilePath());
        } catch (\Throwable $exception) {
            $currentConfig = [];
        }

        $sourceCode = $currentConfig['sourceCode'] ?? [];
        $sourceCode['entryPoint'] = $sourceCodeEntryPoint;

        $this->filePutContent(
            Path::getConfigFilePath(),
            Yaml::dump(
                [
                    'component' => ['slug' => $componentSlug],
                    'benchmark' => [
                        'type' => $benchmarkType,
                        'relativeUrl' => $benchmarkRelativeUrl
                    ],
                    'sourceCode' => $sourceCode,
                    'coreDependency' => [
                        'name' => $coreDependency['name'],
                        'version' => $coreDependency['version']
                    ]
                ],
                100
            )
        );

        return 0;
    }

    private function getSourceCodeEntryPoint(): string
    {
        $entryPoint = $this->getInput()->getOption('entry-point');
        if (is_string($entryPoint) === true) {
            return $entryPoint;
        }

        return $this->askQuestion('Entry point file name?', 'public/index.php');
    }

    private function getBenchmarkRelativeUrl(string $benchmarkType): string
    {
        $benchmarkRelativeUrl = $this->getInput()->getOption('benchmark-relative-url');
        if (is_string($benchmarkRelativeUrl) === true) {
            return $benchmarkRelativeUrl;
        }

        return $this->askQuestion(
            'Benchmark relative url (without protocol and host)?',
            BenchmarkType::getDefaultBenchmarkRelativeUrl($benchmarkType)
        );
    }

    private function getComponentSlug(): string
    {
        $componentSlug = $this->getInput()->getOption('component');
        if (is_string($componentSlug) === true) {
            Component::assertExists($componentSlug);

            return $componentSlug;
        }

        $componentType = $this->askChoiceQuestion('Component type?', ComponentType::getAll());

        return $this->askChoiceQuestion('Component?', Component::getByType($componentType));
    }

    private function getBenchmarkType(string $componentSlug): string
    {
        $benchmarkTypes = BenchmarkType::getByComponentType(
            Component::getType($componentSlug)
        );

        $benchmarkType = $this->getInput()->getOption('benchmark-type');
        if (is_string($benchmarkType) === true) {
            if (array_key_exists($benchmarkType, $benchmarkTypes) === false) {
                throw new \Exception("Benchmark type $benchmarkType does not exists.");
            }

            return $benchmarkType;
        }

        return $this->askChoiceQuestion('Benchmark type?', $benchmarkTypes);
    }

    private function getCoreDependency(string $componentSlug): array
    {
        $composerPath = Path::getSourceCodePath() . '/composer.json';
        $composerData = null;
        if (is_file($composerPath)) {
            try {
                $composerData = json_decode(
                    file_get_contents($composerPath),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
            } catch (\Throwable $e) {
                throw new \Exception('Error while parsing: ' . $e->getMessage());
            }
        }

        $componentType = Component::getType($componentSlug);
        if ($componentType === ComponentType::PHP) {
            $name = Component::PHP;
        } else {
            if (is_array($composerData)) {
                $choices = [];
                $choiceIndex = 1;
                foreach (array_keys($composerData['require'] ?? []) as $dependency) {
                    if ($dependency !== 'php' && substr($dependency, 0, 4) !== 'ext-') {
                        $choices[$choiceIndex] = $dependency;
                        $choiceIndex++;
                    }
                }

                if (count($choices) === 0) {
                    throw new \Exception('No dependency found in composer.json.');
                }

                $name = $this->getInput()->getOption('core-dependency-name');
                if (is_string($name) === true) {
                    if (in_array($name, $choices) === false) {
                        throw new \Exception("Core dependency name $name not found in composer.json.");
                    }
                } else {
                    $name = $this->askChoiceQuestion('Which dependency is the core of the component?', $choices);
                }
            } else {
                $name = $this->askQuestion(
                    'Core dependency name (example: symfony/framework-bundle, cakephp/cakephp)?'
                );
            }
        }

        $version = $composerData['require'][$name] ?? null;
        if (is_string($version) && is_numeric(substr($version, 0, 1)) === false) {
            $version = substr($version, 1);
        }
        if ($this->validateVersion($version) === false) {
            $version = null;
        }

        return ['name' => $name, 'version' => $version];
    }

    private function validateVersion(?string $version): bool
    {
        return is_string($version) && preg_match('/^[0-9]{1,}.[0-9]{1,}.[0-9]{1,}$/', $version) === 1;
    }
}
