<?php

declare(strict_types=1);

namespace App\Command;

use App\ComponentConfiguration\ComponentConfiguration;

class ValidateComposerLockFilesCommand extends AbstractComposerFilesCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('validate:composer:lock')
            ->setDescription('Validate dependencies in composer.lock.phpX.Y');
    }

    protected function doExecute(): parent
    {
        $this
            ->validateDisabledPhpVersions()
            ->validateEnabledPhpVersions();

        return $this;
    }

    private function validateDisabledPhpVersions(): self
    {
        foreach (ComponentConfiguration::getDisabledPhpVersions() as $phpVersion) {
            $this->title('Validation of composer.lock.php' . $phpVersion);

            $lockFile = 'composer.lock.php' . $phpVersion;
            $lockPath = $this->getInstallationPath() . '/' . $lockFile;
            is_file($lockPath)
                ?
                    $this->error(
                        'File should not exist, as this PHP version is disabled by configuration.'
                        . ' See README.md for more informations.'
                    )
                : $this->success('File does not exist.');
        }

        return $this;
    }

    private function validateEnabledPhpVersions(): self
    {
        foreach (ComponentConfiguration::getEnabledPhpVersions() as $phpVersion) {
            $this->title('Validation of composer.lock.php' . $phpVersion);

            $lockFile = 'composer.lock.php' . $phpVersion;
            $lockPath = $this->getInstallationPath() . '/' . $lockFile;
            if (is_readable($lockPath) === false) {
                $this->error('File does not exist.');
            }

            try {
                $data = json_decode(file_get_contents($lockPath), true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                $this->error('Error while parsing: ' . $e->getMessage());
            }

            $this
                ->validateComponentVersion($data)
                ->validateCommonVersion($data);
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
                    $this->error(
                        'Package '
                        . ComponentConfiguration::getCoreDependencyName()
                        . ' version should be '
                        . ComponentConfiguration::getCoreDependencyVersion()
                        . ', '
                        . $package['version']
                        . ' found.'
                    );
                } else {
                    $this->success(
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
            $this->error('Package ' . ComponentConfiguration::getCoreDependencyName() . ' not found.');
        }

        return $this;
    }

    private function validateCommonVersion(array $data): self
    {
        if ($this->isRepositoriesCreated()) {
            $packageFound = false;
            $commonRepositoryName = $this->getCommonRepositoryName();

            foreach ($data['packages'] as $package) {
                if ($package['name'] === $commonRepositoryName) {
                    $packageFound = true;

                    if ($this->isValidateProd()) {
                        $branchPrefix = $this->getCommonProdBranchPrefix();
                        $commonExpectedVersion = $branchPrefix . 'z';
                        $isValidBranch = substr($package['version'], 0, strlen($branchPrefix)) === $branchPrefix;
                    } else {
                        $commonExpectedVersion = $this->getCommonDevBranchName();
                        $isValidBranch = $package['version'] === $commonExpectedVersion;
                    }

                    $isValidBranch
                        ?
                            $this->success(
                                'Package ' . $commonRepositoryName . ' version is ' . $commonExpectedVersion . '.'
                            )
                        :
                            $this->error(
                                'Package '
                                . $commonRepositoryName
                                . ' version should be '
                                . $commonExpectedVersion
                                . ' but is '
                                . $package['version']
                                . '. See README.md for more informations.'
                            );
                }
            }

            if ($packageFound === false) {
                $this->error('Package ' . ComponentConfiguration::getCoreDependencyName() . ' not found.');
            }
        } else {
            $this->skipBranchNameWarning();
        }

        return $this;
    }
}
