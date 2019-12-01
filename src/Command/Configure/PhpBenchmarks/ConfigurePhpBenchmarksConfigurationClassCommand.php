<?php

declare(strict_types=1);

namespace App\Command\Configure\PhpBenchmarks;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Component\ComponentType,
    Utils\Path
};

final class ConfigurePhpBenchmarksConfigurationClassCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks:configurationClass';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription(
            'Create '
                . Path::rmPrefix(Path::getBenchmarkConfigurationClassPath())
                . ' and configure it'
        );
    }

    protected function doExecute(): AbstractCommand
    {
        $this->outputTitle(
            'Creation of ' . Path::rmPrefix(Path::getBenchmarkConfigurationClassPath())
        );

        $configurationPath = Path::getBenchmarkConfigurationClassPath();
        if (file_exists($configurationPath)) {
            unlink($configurationPath);
            $this->outputSuccess('Remove file ' . Path::rmPrefix($configurationPath));
        }

        $componentType = $this->getComponentType();
        if ($componentType === ComponentType::PHP) {
            $componentName = 'PHP';
            $componentSlug = 'php';
        } else {
            $componentName = $this->askQuestion('Component name (exemple: Symfony, Zend Framework)?');
            $componentSlug = $this->askQuestion('Component slug (exemple: symfony, zend-framework)?');
        }

        $benchmarkType = $this->getBenchmarkType($componentType);
        $entryPointFileName = $this->askQuestion('Entry point file name?', 'public/index.php');
        $benchmarkUrl = $this->askQuestion(
            'Benchmark url, after host?',
            BenchmarkType::getDefaultBenchmarkUrl($benchmarkType)
        );

        $coreDependencyName = $this->getCoreDependencyName($componentType);
        $coreDependencyMajorVersion = $this->askQuestionInt('Core dependency major version?');
        $coreDependencyMinorVersion = $this->askQuestionInt('Core dependency minor version?');
        $coreDependencyPatchVersion = $this->askQuestionInt('Core dependency patch version?');

        return $this
            ->writeFileFromTemplate(
                Path::rmPrefix(Path::getBenchmarkConfigurationClassPath()),
                [
                    'componentType' => $componentType,
                    'componentName' => $componentName,
                    'componentSlug' => $componentSlug,
                    'entryPointFileName' => $entryPointFileName,
                    'benchmarkUrl' => $benchmarkUrl,
                    'coreDependencyName' => $coreDependencyName,
                    'coreDependencyMajorVersion' => $coreDependencyMajorVersion,
                    'coreDependencyMinorVersion' => $coreDependencyMinorVersion,
                    'coreDependencyPatchVersion' => $coreDependencyPatchVersion,
                    'benchmarkType' => $benchmarkType
                ],
                $componentType,
                $benchmarkType
            );
    }

    private function askQuestionInt(string $question): int
    {
        do {
            $return = $this->askQuestion($question);
        } while (is_numeric($return) === false);

        return (int) $return;
    }

    private function getCoreDependencyName(int $componentType): string
    {
        if ($componentType === ComponentType::PHP) {
            return 'php';
        }

        $composerPath = Path::getBenchmarkPath() . '/composer.json';
        if (is_file($composerPath)) {
            try {
                $data = json_decode(
                    file_get_contents($composerPath),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
            } catch (\Throwable $e) {
                throw new \Exception('Error while parsing: ' . $e->getMessage());
            }

            $choices = [];
            $choiceIndex = 1;
            foreach (array_keys($data['require'] ?? []) as $dependency) {
                if ($dependency !== 'php' && substr($dependency, 0, 4) !== 'ext-') {
                    $choices[$choiceIndex] = $dependency;
                    $choiceIndex++;
                }
            }
            $return = $this->askChoiceQuestion('Which dependency is the core of the component?', $choices);
        } else {
            $return = $this->askQuestion(
                'Core dependency name of component? Example: symfony/framework-bundle, cakephp/cakephp'
            );
        }

        return $return;
    }

    private function getComponentType(): int
    {
        $componentTypes = ComponentType::getAll();

        return array_search(
            $this->askChoiceQuestion('Component type?', $componentTypes),
            $componentTypes
        );
    }

    private function getBenchmarkType(int $componentType): int
    {
        $benchmarkTypes = BenchmarkType::getByComponentType($componentType);

        return (int) array_search(
            $this->askChoiceQuestion('Benchmark type?', $benchmarkTypes),
            $benchmarkTypes
        );
    }
}
