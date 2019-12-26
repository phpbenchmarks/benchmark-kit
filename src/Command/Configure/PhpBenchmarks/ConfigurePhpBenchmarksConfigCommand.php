<?php

declare(strict_types=1);

namespace App\Command\Configure\PhpBenchmarks;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Component\Component,
    Component\ComponentType,
    Utils\Path
};
use Symfony\Component\Yaml\Yaml;

final class ConfigurePhpBenchmarksConfigCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks:config';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create ' . Path::rmPrefix(Path::getConfigFilePath()));
    }

    protected function doExecute(): AbstractCommand
    {
        $this->outputTitle('Creation of ' . Path::rmPrefix(Path::getConfigFilePath()));

        $componentId = $this->getComponentId();
        $benchmarkType = $this->getBenchmarkType($componentId);
        $sourceCodeEntryPoint = $this->askQuestion('Entry point file name?', 'public/index.php');
        $benchmarkUrl = $this->askQuestion(
            'Benchmark url, after host?',
            BenchmarkType::getDefaultBenchmarkUrl($benchmarkType)
        );
        $coreDependency = $this->getCoreDependency($componentId);
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

        return $this->filePutContent(
            Path::getConfigFilePath(),
            Yaml::dump(
                [
                    'component' => ['id' => $componentId],
                    'benchmark' => [
                        'type' => $benchmarkType,
                        'url' => $benchmarkUrl
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
    }

    private function getComponentId(): int
    {
        $componentTypes = ComponentType::getAll();
        $componentType = (int) array_search(
            $this->askChoiceQuestion('Component type?', $componentTypes),
            $componentTypes
        );

        $components = Component::getByType($componentType);

        return (int) array_search(
            $this->askChoiceQuestion('Component?', $components),
            $components
        );
    }

    private function getBenchmarkType(int $componentId): int
    {
        $benchmarkTypes = BenchmarkType::getByComponentType(
            Component::getType($componentId)
        );

        return (int) array_search(
            $this->askChoiceQuestion('Benchmark type?', $benchmarkTypes),
            $benchmarkTypes
        );
    }

    private function getCoreDependency(int $componentId): array
    {
        $composerPath = Path::getBenchmarkPath() . '/composer.json';
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

        $componentType = Component::getType($componentId);
        if ($componentType === ComponentType::PHP) {
            $name = 'php';
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

                $name = $this->askChoiceQuestion('Which dependency is the core of the component?', $choices);
            } else {
                $name = $this->askQuestion(
                    'Core dependency name? Example: symfony/framework-bundle, cakephp/cakephp'
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
