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
        parent::configure();

        $this
            ->setName('phpbenchmarks:validate:composerlock')
            ->setDescription('Validation of dependencies in composer.lock.phpX.Y');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

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

            $this
                ->validateComponentVersion($output, $data)
                ->validateCommonVersion($output, $data);
        }

        return $this;
    }

    private function validateComponentVersion(OutputInterface $output, array $data): self
    {
        $packageFound = false;
        foreach ($data['packages'] as $package) {
            if ($package['name'] === ComponentConfiguration::DEPENDENCY_NAME) {
                $packageFound = true;

                if (
                    $package['version'] !== ComponentConfiguration::getDependencyVersion()
                    && $package['version'] !== 'v' . ComponentConfiguration::getDependencyVersion())
                {
                    $this->validationFailed(
                        $output,
                        'Package '
                            . ComponentConfiguration::DEPENDENCY_NAME
                            . ' version should be '
                            . ComponentConfiguration::getDependencyVersion()
                            . ', '
                            . $package['version']
                            . ' found.'
                    );
                } else {
                    $this->validationSuccess(
                        $output,
                        'Package '
                            . ComponentConfiguration::DEPENDENCY_NAME
                            . ' version is '
                            . ComponentConfiguration::getDependencyVersion()
                            . '.'
                    );
                    break;
                }
            }
        }

        if ($packageFound === false) {
            $this->validationFailed($output, 'Package ' . ComponentConfiguration::DEPENDENCY_NAME . ' not found.');
        }

        return $this;
    }

    private function validateCommonVersion(OutputInterface $output, array $data): self
    {
        if ($this->isRepositoriesCreated()) {
            $packageFound = false;
            $commonRepositoryName = $this->getCommonRepositoryName();

            foreach ($data['packages'] as $package) {
                if ($package['name'] === $commonRepositoryName) {
                    $packageFound = true;

                    if ($this->isValidateDev()) {
                        $commonExpectedVersion = $this->getCommonDevBranchName();
                        $isValidBranch = $package['version'] === $commonExpectedVersion;
                    } else {
                        $branchPrefix = $this->getCommonProdBranchPrefix($output);
                        $commonExpectedVersion = $branchPrefix . 'z';
                        $isValidBranch = substr($package['version'], 0, strlen($branchPrefix)) === $branchPrefix;
                    }

                    $isValidBranch
                        ?
                            $this->validationSuccess(
                                $output,
                                'Package ' . $commonRepositoryName . ' version is ' . $commonExpectedVersion . '.'
                            )
                        :
                            $this->validationFailed(
                                $output,
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
                $this->validationFailed($output, 'Package ' . ComponentConfiguration::DEPENDENCY_NAME . ' not found.');
            }
        } else {
            $this->repositoriesNotCreatedWarning($output);
        }

        return $this;
    }
}
