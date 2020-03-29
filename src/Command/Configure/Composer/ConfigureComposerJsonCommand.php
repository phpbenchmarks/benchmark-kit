<?php

declare(strict_types=1);

namespace App\Command\Configure\Composer;

use App\{
    Command\AbstractCommand,
    Benchmark\Benchmark,
    Utils\Path
};
use Symfony\Component\Console\Input\InputOption;

final class ConfigureComposerJsonCommand extends AbstractCommand
{
    public const LICENSE = 'proprietary';

    /** @var string */
    protected static $defaultName = 'configure:composer:json';

    public static function getComposerName(string $componentSlug = null): string
    {
        return 'phpbenchmarks/' . ($componentSlug ?? Benchmark::getComponentSlug());
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Configure composer.json')
            ->addOption(
                'minor-dependencies',
                null,
                InputOption::VALUE_REQUIRED,
                'Dependencies whose version is to change, separated by ","'
            )
            ->addOption('no-dependency-version', null, InputOption::VALUE_NONE, 'Do not configure dependency version')
            ->addOption(
                'component-slug',
                null,
                InputOption::VALUE_REQUIRED,
                'Slug of the component, if not specified it will be taken in '
                    . Path::rmPrefix(Path::getConfigFilePath())
            );
    }

    protected function doExecute(): int
    {
        $composerJsonFile = Path::getSourceCodePath() . '/composer.json';
        if (is_readable($composerJsonFile) === false) {
            throw new \Exception('File does not exist.');
        }

        try {
            $data = json_decode(file_get_contents($composerJsonFile), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw new \Exception('Error while parsing: ' . $e->getMessage());
        }

        $this
            ->outputTitle('Configure composer.json')
            ->defineLicense($data)
            ->defineVersions($data)
            ->defineName($data)
            ->filePutContent(
                $composerJsonFile,
                json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
            );

        return 0;
    }

    private function defineName(array &$data): self
    {
        $name = static::getComposerName($this->getInput()->getOption('component-slug'));
        $data = array_merge(['name' => $name], $data);

        return $this->outputSuccess("Name defined to $name.");
    }

    private function defineLicense(array &$data): self
    {
        $data = array_merge(['license' => static::LICENSE], $data);

        return $this->outputSuccess('License defined to ' . static::LICENSE . '.');
    }

    private function defineVersions(array &$data): self
    {
        if ($this->getInput()->getOption('no-dependency-version') === true) {
            return $this;
        }

        $dependencyVersion = Benchmark::getCoreDependencyMajorVersion()
            . '.'
            . Benchmark::getCoreDependencyMinorVersion()
            . '.'
            . Benchmark::getCoreDependencyPatchVersion();

        $data['require'][Benchmark::getCoreDependencyName()] = $dependencyVersion;

        $minorDependencies = $this->getInput()->getOption('minor-dependencies');
        if (is_string($minorDependencies)) {
            foreach (explode(',', $minorDependencies) as $minorDependency) {
                $minorDependencyVersionModified = false;
                foreach ($data['require'] as $dependency => &$version) {
                    if ($minorDependency === $dependency) {
                        $version = $dependencyVersion;
                        $minorDependencyVersionModified = true;
                        break;
                    }
                }

                if ($minorDependencyVersionModified === false) {
                    throw new \Exception('Minor dependency "' . $minorDependency . '" not found.');
                }
            }
        }

        return $this->outputSuccess(
            'Dependency ' . Benchmark::getCoreDependencyName() . " required as $dependencyVersion."
        );
    }
}
