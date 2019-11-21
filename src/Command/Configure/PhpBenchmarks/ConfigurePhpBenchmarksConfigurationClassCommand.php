<?php

declare(strict_types=1);

namespace App\Command\Configure\PhpBenchmarks;

use App\{
    Benchmark\BenchmarkType,
    Command\AbstractCommand,
    Component\ComponentType,
    PhpVersion\PhpVersion
};

final class ConfigurePhpBenchmarksConfigurationClassCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'configure:phpbenchmarks:configurationClass';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Create ' . $this->getConfigurationFilePath(true) . ' and configure it');
    }

    protected function doExecute(): AbstractCommand
    {
        $this->outputTitle('Creation of ' . $this->getConfigurationFilePath(true));

        $configurationPath = $this->getInstallationPath() . '/' . $this->getConfigurationFilePath(true);
        if (file_exists($configurationPath)) {
            unlink($configurationPath);
            $this->outputSuccess('Remove file ' . $this->removeInstallationPathPrefix($configurationPath));
        }

        $componentType = $this->getComponentType();
        $componentName = $this->askQuestion('Component name (exemple: Symfony, Zend Framework)?');
        $componentSlug = $this->askQuestion('Component slug (exemple: symfony, zend-framework)?');

        $benchmarkType = $this->getBenchmarkType($componentType);
        $benchmarkUrl = $this->askQuestion(
            'Benchmark url, after host?',
            BenchmarkType::getDefaultBenchmarkUrl($benchmarkType)
        );

        $coreDependencyName = $this->getCoreDependencyName();
        $coreDependencyMajorVersion = $this->askQuestionInt('Core dependency major version?');
        $coreDependencyMinorVersion = $this->askQuestionInt('Core dependency minor version?');
        $coreDependencyPatchVersion = $this->askQuestionInt('Core dependency patch version?');

        $compatiblesPhpVersions = $this->getCompatiblesPhpVersions();

        return $this
            ->writeFileFromTemplate(
                $this->getConfigurationFilePath(true),
                [
                    'componentType' => $componentType,
                    'componentName' => $componentName,
                    'componentSlug' => $componentSlug,
                    'benchmarkUrl' => $benchmarkUrl,
                    'coreDependencyName' => $coreDependencyName,
                    'coreDependencyMajorVersion' => $coreDependencyMajorVersion,
                    'coreDependencyMinorVersion' => $coreDependencyMinorVersion,
                    'coreDependencyPatchVersion' => $coreDependencyPatchVersion,
                    'benchmarkType' => $benchmarkType,
                    'compatiblesPhpVersions' => $compatiblesPhpVersions
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

    private function getCompatiblesPhpVersions(): string
    {
        $compatibles = [];
        foreach (PhpVersion::getAll() as $phpVersion) {
            $parts = explode('.', $phpVersion);
            if ($this->askConfirmationQuestion('Is PHP ' . $phpVersion . ' compatible?')) {
                $compatibles[] = '($major === ' . $parts[0] . ' && $minor === ' . $parts[1] . ')';
            }
        }

        return implode("\n" . '            || ', $compatibles);
    }

    private function getCoreDependencyName(): string
    {
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
