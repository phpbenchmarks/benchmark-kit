<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\{
    Command\AbstractCommand,
    Command\Composer\ComposerUpdateCommand,
    Component\ComponentType,
    ComponentConfiguration\ComponentConfiguration,
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

    protected function doExecute(): AbstractCommand
    {
        foreach (ComponentConfiguration::getCompatiblesPhpVersions() as $phpVersion) {
            $composerLockFilePath = Path::getComposerLockFilePath($phpVersion);
            $this->outputTitle('Validation of ' . Path::removeBenchmarkPathPrefix($composerLockFilePath));

            if (is_readable($composerLockFilePath) === false) {
                $this->throwError(
                    Path::removeBenchmarkPathPrefix($composerLockFilePath)
                    . ' does not exist. Call "phpbenchkit '
                    . ComposerUpdateCommand::getDefaultName()
                    . '" to create it.'
                );
            }

            $this->outputSuccess(Path::removeBenchmarkPathPrefix($composerLockFilePath) . ' exist.');

            if (ComponentConfiguration::getComponentType() === ComponentType::PHP) {
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
                $this->throwError('Error while parsing: ' . $e->getMessage());
            }

            $this->validateComponentVersion($data);
        }

        return $this;
    }

    private function validateComponentVersion(array $data): self
    {
        $packageFound = false;
        foreach ($data['packages'] as $package) {
            if ($package['name'] === ComponentConfiguration::getCoreDependencyName()) {
                $packageFound = true;

                if (
                    $package['version'] !== ComponentConfiguration::getCoreDependencyVersion()
                    && $package['version'] !== 'v' . ComponentConfiguration::getCoreDependencyVersion()
                ) {
                    $this->throwError(
                        'Package '
                            . ComponentConfiguration::getCoreDependencyName()
                            . ' version should be '
                            . ComponentConfiguration::getCoreDependencyVersion()
                            . ', '
                            . $package['version']
                            . ' found.'
                    );
                } else {
                    $this->outputSuccess(
                        'Package '
                            . ComponentConfiguration::getCoreDependencyName()
                            . ' version is '
                            . ComponentConfiguration::getCoreDependencyVersion()
                            . '.'
                    );
                    break;
                }
            }
        }

        if ($packageFound === false) {
            $this->throwError('Package ' . ComponentConfiguration::getCoreDependencyName() . ' not found.');
        }

        return $this;
    }
}
