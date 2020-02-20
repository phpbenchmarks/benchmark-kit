<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\{
    Command\AbstractCommand,
    Command\Composer\ComposerUpdateCommand,
    Component\ComponentType,
    Benchmark\Benchmark,
    Utils\Path
};

final class ValidatePhpBenchmarksComposerLockCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'validate:phpbenchmarks:composer:lock';

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Validate dependencies in composer.lock');
    }

    protected function doExecute(): int
    {
        foreach (Benchmark::getCompatiblesPhpVersions() as $phpVersion) {
            $composerLockFilePath = Path::getComposerLockPath($phpVersion);
            $this->outputTitle('Validation of ' . Path::rmPrefix($composerLockFilePath));

            if (is_readable($composerLockFilePath) === false) {
                throw new \Exception(
                    Path::rmPrefix($composerLockFilePath)
                        . ' does not exist. Call "phpbenchkit '
                        . ComposerUpdateCommand::getDefaultName()
                        . '" to create it.'
                );
            }

            $this->outputSuccess(Path::rmPrefix($composerLockFilePath) . ' exists.');

            if (Benchmark::getComponentType() === ComponentType::PHP) {
                continue;
            }

            try {
                $data = json_decode(
                    file_get_contents($composerLockFilePath),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
            } catch (\Throwable $e) {
                throw new \Exception('Error while parsing: ' . $e->getMessage());
            }

            $this->validateComponentVersion($data);
        }

        return 0;
    }

    private function validateComponentVersion(array $data): self
    {
        $packageFound = false;
        foreach ($data['packages'] as $package) {
            if ($package['name'] === Benchmark::getCoreDependencyName()) {
                $packageFound = true;

                if (
                    $package['version'] !== Benchmark::getCoreDependencyVersion()
                    && $package['version'] !== 'v' . Benchmark::getCoreDependencyVersion()
                ) {
                    throw new \Exception(
                        'Package '
                            . Benchmark::getCoreDependencyName()
                            . ' version should be '
                            . Benchmark::getCoreDependencyVersion()
                            . ', '
                            . $package['version']
                            . ' found.'
                    );
                } else {
                    $this->outputSuccess(
                        'Package '
                            . Benchmark::getCoreDependencyName()
                            . ' version is '
                            . Benchmark::getCoreDependencyVersion()
                            . '.'
                    );
                    break;
                }
            }
        }

        if ($packageFound === false) {
            throw new \Exception('Package ' . Benchmark::getCoreDependencyName() . ' not found.');
        }

        return $this;
    }
}
