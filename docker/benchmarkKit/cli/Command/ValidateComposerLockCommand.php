<?php

declare(strict_types=1);

namespace App\Command;

use App\{
    ComponentConfiguration,
    Exception\ValidationException
};
use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface
};

class ValidateComposerLockCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('phpbenchmarks:validate:composerlock')
            ->setDescription('Validation of dependencies in composer.lock.phpX.Y');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this
                ->validateDisabledPhpVersions($output)
                ->validateEnabledPhpVersions($output);
        } catch (ValidationException $e) {
            return 1;
        }

        return 0;
    }

    private function validateDisabledPhpVersions(OutputInterface $output): self
    {
        foreach (ComponentConfiguration::getDisabledPhpVersions() as $phpVersion) {
            $lockFile = 'composer.lock.php' . $phpVersion;
            $lockPath = $this->getInstallationPath() . '/' . $lockFile;
            $this->validationPrefix = '[' . $lockFile . '] ';
            is_file($lockPath)
                ?
                    $this->validationFailed(
                        $output,
                        'File should not exist, as this PHP version is disabled by configuration.'
                        . ' See README.md for more informations.'
                    )
                : $this->validationSuccess($output, 'File does not exist.');
        }

        return $this;
    }

    private function validateEnabledPhpVersions(OutputInterface $output): self
    {
        foreach (ComponentConfiguration::getEnabledPhpVersions() as $phpVersion) {
            $lockFile = 'composer.lock.php' . $phpVersion;
            $lockPath = $this->getInstallationPath() . '/' . $lockFile;
            $this->validationPrefix = '[' . $lockFile . '] ';
            if (is_readable($lockPath) === false) {
                $this->validationFailed($output, 'File does not exist.');
            }

            try {
                $data = json_decode(file_get_contents($lockPath), true, 512, JSON_THROW_ON_ERROR);
            } catch (\Exception $e) {
                $this->validationFailed($output, 'Error while parsing: ' . $e->getMessage());
            }

            $this->validateRequireComponent($output, $data);
        }

        return $this;
    }

    private function validateRequireComponent(OutputInterface $output, array $data): self
    {
        $packageFound = false;
        foreach ($data['packages'] as $package) {
            if ($package['name'] === ComponentConfiguration::MAIN_REPOSITORY) {
                $packageFound = true;

                if (
                    $package['version'] !== ComponentConfiguration::getVersion()
                    && $package['version'] !== 'v' . ComponentConfiguration::getVersion())
                {
                    $this->validationFailed(
                        $output,
                        'Package '
                            . ComponentConfiguration::MAIN_REPOSITORY
                            . ' version should be '
                            . ComponentConfiguration::getVersion()
                            . ', '
                            . $package['version']
                            . ' found.'
                    );
                } else {
                    $this->validationSuccess(
                        $output,
                        'Package '
                            . ComponentConfiguration::MAIN_REPOSITORY
                            . ' version is '
                            . ComponentConfiguration::getVersion()
                            . '.'
                    );
                    break;
                }
            }
        }

        if ($packageFound === false) {
            $this->validationFailed($output, 'Package ' . ComponentConfiguration::MAIN_REPOSITORY . ' not found.');
        }

        return $this;
    }
}
