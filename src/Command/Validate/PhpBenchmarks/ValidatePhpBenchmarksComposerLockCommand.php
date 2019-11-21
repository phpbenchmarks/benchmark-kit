<?php

declare(strict_types=1);

namespace App\Command\Validate\PhpBenchmarks;

use App\{
    Command\AbstractCommand,
    Command\Composer\ComposerUpdateCommand,
    Command\PhpVersionArgumentTrait,
    Command\Validate\AbstractComposerFilesCommand,
    Component\ComponentType,
    ComponentConfiguration\ComponentConfiguration
};
use Symfony\Component\Console\Input\InputArgument;

final class ValidatePhpBenchmarksComposerLockCommand extends AbstractComposerFilesCommand
{
    use PhpVersionArgumentTrait;

    /** @var string */
    protected static $defaultName = 'validate:phpbenchmarks:composer:lock';

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Validate dependencies in ' . $this->getComposerLockFilePath('X.Y', true))
            ->addPhpVersionArgument($this, InputArgument::OPTIONAL);
    }

    protected function doExecute(): AbstractCommand
    {
        if (ComponentConfiguration::getComponentType() === ComponentType::PHP) {
            return $this;
        }

        $this
            ->assertPhpVersionArgument($this, true)
            ->validateDisabledPhpVersions()
            ->validateEnabledPhpVersions();

        return $this;
    }

    private function validateDisabledPhpVersions(): self
    {
        foreach ($this->getPhpVersions(ComponentConfiguration::getDisabledPhpVersions()) as $phpVersion) {
            $this->outputTitle('Validation of ' . $this->getComposerLockFilePath($phpVersion, true));
            is_file($this->getComposerLockFilePath($phpVersion))
                ?
                    $this->throwError(
                        'File should not exist, as this PHP version is disabled by configuration.'
                            . ' See README.md for more informations.'
                    )
                : $this->outputSuccess('File does not exist.');
        }

        return $this;
    }

    private function validateEnabledPhpVersions(): self
    {
        foreach ($this->getPhpVersions(ComponentConfiguration::getEnabledPhpVersions()) as $phpVersion) {
            $this->outputTitle('Validation of ' . $this->getComposerLockFilePath($phpVersion, true));

            $lockPath = $this->getComposerLockFilePath($phpVersion);
            if (is_readable($lockPath) === false) {
                $this->throwError(
                    'File does not exist. Call "phpbenchkit '
                        . ComposerUpdateCommand::getDefaultName()
                        . '" to create it.'
                );
            }

            try {
                $data = json_decode(file_get_contents($lockPath), true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                $this->throwError('Error while parsing: ' . $e->getMessage());
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

    private function validateCommonVersion(array $data): self
    {
        if ($this->skipBranchName() === false) {
            $packageFound = false;
            $commonRepositoryName = $this->getCommonRepositoryName();

            foreach ($data['packages'] as $package) {
                if ($package['name'] === $commonRepositoryName) {
                    $packageFound = true;

                    $branchPrefix = $this->getCommonProdBranchPrefix();
                    $shouldRequire = $branchPrefix . 'z';
                    $isBranchValid = substr($package['version'], 0, strlen($branchPrefix)) === $branchPrefix;

                    if ($isBranchValid === false && $this->isValidateProd() === false) {
                        $shouldRequire .= ' or ' . $this->getCommonDevBranchName();
                        $isBranchValid = $package['version'] === $this->getCommonDevBranchName();
                    }

                    $isBranchValid
                        ?
                            $this->outputSuccess(
                                'Package ' . $commonRepositoryName . ' version is ' . $package['version'] . '.'
                            )
                        :
                            $this->throwError(
                                'Package '
                                    . $commonRepositoryName
                                    . ' version should be '
                                    . $shouldRequire
                                    . ' but is '
                                    . $package['version']
                                    . '. See README.md for more informations.'
                            );
                }
            }

            if ($packageFound === false) {
                $this->throwError('Package ' . ComponentConfiguration::getCoreDependencyName() . ' not found.');
            }
        }

        return $this;
    }

    private function getPhpVersions(array $phpVersions): array
    {
        $phpVersion = $this->getPhpVersionFromArgument($this);
        if (is_string($phpVersion)) {
            $return = in_array($phpVersion, $phpVersions) ? [$phpVersion] : [];
        } else {
            $return = $phpVersions;
        }

        return $return;
    }
}
